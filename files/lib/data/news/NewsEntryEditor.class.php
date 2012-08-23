<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/news/NewsEntry.class.php');
require_once(WSIP_DIR.'lib/data/publication/object/PublicationObjectEditor.class.php');

/**
 * Provides functions to manage news entries.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.news
 * @category	Infinite Portal
 */
class NewsEntryEditor extends NewsEntry implements PublicationObjectEditor {
	/**
	 * Sets the subject of this entry.
	 *
	 * @param	string		$subject
	 */
	public function setSubject($subject) {
		if ($subject == $this->subject) return;

		$sql = "UPDATE 	wsip".WSIP_N."_news_entry
			SET	subject = '".escapeString($subject)."'
			WHERE 	entryID = ".$this->entryID;
		WCF::getDB()->registerShutdownUpdate($sql);
	}

	/**
	 * Marks this entry.
	 */
	public function mark() {
		$markedEntries = self::getMarkedEntries();
		if ($markedEntries === null || !is_array($markedEntries)) {
			$markedEntries = array($this->entryID);
			WCF::getSession()->register('markedNewsEntries', $markedEntries);
		}
		else {
			if (!in_array($this->entryID, $markedEntries)) {
				array_push($markedEntries, $this->entryID);
				WCF::getSession()->register('markedNewsEntries', $markedEntries);
			}
		}
	}

	/**
	 * Unmarks this entry.
	 */
	public function unmark() {
		$markedEntries = self::getMarkedEntries();
		if (is_array($markedEntries) && in_array($this->entryID, $markedEntries)) {
			$key = array_search($this->entryID, $markedEntries);
			unset($markedEntries[$key]);
			if (count($markedEntries) == 0) {
				self::unmarkAll();
			}
			else {
				WCF::getSession()->register('markedNewsEntries', $markedEntries);
			}
		}
	}

	/**
	 * Disables this entry.
	 */
	public function disable() {
		self::disableAll($this->entryID);
	}

	/**
	 * Enables this entry.
	 */
	public function enable() {
		self::enableAll($this->entryID);
	}

	/**
	 * Moves this entry into the recycle bin.
	 *
	 * @param	string		$reason
	 */
	public function trash($reason = '') {
		self::trashAll($this->entryID, $reason);
	}

	/**
	 * Deletes this entry completely.
	 */
	public function delete() {
		self::deleteAllCompletely($this->entryID);
	}

	/**
	 * Restores this deleted entry.
	 */
	public function restore() {
		self::restoreAll($this->entryID);
	}

	/**
	 * Updates this entry.
	 *
	 * @param	integer				$categoryID
	 * @param	integer				$languageID
	 * @param	string				$subject
	 * @param	string				$message
	 * @param	string				$teaser
	 * @param	integer				$publishingTime
	 * @param	integer				$enableComments
	 * @param	array				$options
	 * @param	MessageAttachmentListEditor	$attachmentList
	 * @param	PollEditor			$poll
	 */
	public function update($categoryID, $languageID, $subject, $message, $teaser, $publishingTime, $enableComments, $options, $attachmentList = null, $poll = null) {
		// get number of attachments
		$attachmentsAmount = ($attachmentList !== null ? count($attachmentList->getAttachments($this->entryID)) : 0);

		// update entry
		$sql = "UPDATE 	wsip".WSIP_N."_news_entry
			SET	categoryID = ".$categoryID.",
				languageID = ".$languageID.",
				subject = '".escapeString($subject)."',
				message = '".escapeString($message)."',
				teaser = '".escapeString($teaser)."',
				publishingTime = ".$publishingTime.",
				attachments = ".$attachmentsAmount.",
				".($poll != null ? "pollID = ".intval($poll->pollID)."," : '')."
				enableSmilies = ".$options['enableSmilies'].",
				enableHtml = ".$options['enableHtml'].",
				enableBBCodes = ".$options['enableBBCodes'].",
				enableComments = ".$enableComments."
			WHERE 	entryID = ".$this->entryID;
		WCF::getDB()->sendQuery($sql);

		// update attachments
		if ($attachmentList != null) {
			$attachmentList->findEmbeddedAttachments($message);
		}

		// update poll
		if ($poll != null) {
			$poll->updateMessageID($this->entryID);
		}
	}

	/**
	 * Updates the tags of this entry.
	 *
	 * @param	array		$tags
	 */
	public function updateTags($tagArray) {
		// include files
		require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
		require_once(WSIP_DIR.'lib/data/news/TaggedNewsEntry.class.php');

		// save tags
		$tagged = new TaggedNewsEntry(null, array(
			'entryID' => $this->entryID,
			'taggable' => TagEngine::getInstance()->getTaggable('com.wcfsolutions.wsip.news.entry')
		));

		// delete old tags
		TagEngine::getInstance()->deleteObjectTags($tagged, array($this->languageID));

		// save new tags
		if (count($tagArray) > 0) TagEngine::getInstance()->addTags($tagArray, $tagged, $this->languageID);
	}

	/**
	 * Creates a new entry.
	 *
	 * @param	integer				$categoryID
	 * @param	integer				$languageID
	 * @param	string				$subject
	 * @param	string				$message
	 * @param	string				$teaser
	 * @param	integer				$userID
	 * @param	string				$username
	 * @param	integer				$publishingTime
 	 * @param	integer				$enableComments
	 * @param	array				$options
	 * @param	MessageAttachmentListEditor	$attachmentList
	 * @param	integer				$isDisabled
	 * @param	string				$ipAddress
	 * @return	NewsEntryEditor
	 */
	public static function create($categoryID, $languageID, $subject, $message, $teaser, $userID, $username, $publishingTime, $enableComments, $options = array(), $attachmentList = null, $poll = null, $isDisabled = 0, $ipAddress = null) {
		if ($ipAddress == null) $ipAddress = WCF::getSession()->ipAddress;
		$attachmentsAmount = $attachmentList != null ? count($attachmentList->getAttachments()) : 0;

		// insert entry
		$sql = "INSERT INTO	wsip".WSIP_N."_news_entry
					(categoryID, languageID, userID, username, subject, message, teaser, time, publishingTime, everEnabled, isDisabled, attachments, pollID, enableComments, ipAddress, enableSmilies, enableHtml, enableBBCodes)
			VALUES		(".$categoryID.", ".$languageID.", ".$userID.", '".escapeString($username)."', '".escapeString($subject)."', '".escapeString($message)."', '".escapeString($teaser)."', ".TIME_NOW.", ".$publishingTime.", ".($isDisabled ? 0 : 1).", ".$isDisabled.", ".$attachmentsAmount.", ".($poll != null ? intval($poll->pollID) : 0).", ".$enableComments.", '".escapeString($ipAddress)."',
					".(isset($options['enableSmilies']) ? $options['enableSmilies'] : 1).",
					".(isset($options['enableHtml']) ? $options['enableHtml'] : 0).",
					".(isset($options['enableBBCodes']) ? $options['enableBBCodes'] : 1).")";
		WCF::getDB()->sendQuery($sql);

		// get entry id
		$entryID = WCF::getDB()->getInsertID("wsip".WSIP_N."_news_entry", 'entryID');

		// get new entry
		$entry = new NewsEntryEditor($entryID);

		// update attachments
		if ($attachmentList != null) {
			$attachmentList->updateContainerID($entryID);
			$attachmentList->findEmbeddedAttachments($message);
		}

		// update poll
		if ($poll != null) {
			$poll->updateMessageID($entryID);
		}

		// return entry
		return $entry;
	}

	/**
	 * Creates a preview of an entry.
	 *
	 * @param	string		$subject
	 * @param 	string		$message
	 * @param 	boolean		$enableSmilies
	 * @param 	boolean		$enableHtml
	 * @param 	boolean		$enableBBCodes
	 * @return	string
	 */
	public static function createPreview($subject, $message, $enableSmilies = 1, $enableHtml = 0, $enableBBCodes = 1) {
		$row = array(
			'entryID' => 0,
			'subject' => $subject,
			'message' => $message,
			'enableSmilies' => $enableSmilies,
			'enableHtml' => $enableHtml,
			'enableBBCodes' => $enableBBCodes,
			'messagePreview' => true
		);

		// get entry
		require_once(WSIP_DIR.'lib/data/news/ViewableNewsEntry.class.php');
		$entry = new ViewableNewsEntry(null, $row);
		return $entry->getFormattedMessage();
	}

	/**
	 * Returns the currently marked entries.
	 *
	 * @return	mixed
	 */
	public static function getMarkedEntries() {
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedNewsEntries'])) {
			return $sessionVars['markedNewsEntries'];
		}
		return null;
	}

	/**
	 * Unmarks all marked entries.
	 */
	public static function unmarkAll() {
		WCF::getSession()->unregister('markedNewsEntries');
	}

	/**
	 * Disables the entries with the given entry ids.
	 *
	 * @param	string		$entryIDs
	 */
	public static function disableAll($entryIDs) {
		if (empty($entryIDs)) return;

		$sql = "UPDATE 	wsip".WSIP_N."_news_entry
			SET	isDeleted = 0,
				isDisabled = 1
			WHERE 	entryID IN (".$entryIDs.")";
		WCF::getDB()->sendQuery($sql);
	}

	/**
	 * Enables the entries with the given entry ids.
	 *
	 * @param	string		$entryIDs
	 */
	public static function enableAll($entryIDs) {
		if (empty($entryIDs)) return;

		// get not yet enabled entries
		$statEntryIDs = '';
		$sql = "SELECT	entryID
			FROM	wsip".WSIP_N."_news_entry
			WHERE	entryID IN (".$entryIDs.")
				AND isDisabled = 1
				AND everEnabled = 0";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($statEntryIDs)) $statEntryIDs .= ',';
			$statEntryIDs .= $row['entryID'];
		}

		// update user entries and activity points
		self::updateUserStats($statEntryIDs, 'enable');

		// enable entries
		$sql = "UPDATE 	wsip".WSIP_N."_news_entry
			SET	isDisabled = 0,
				everEnabled = 1,
				publishingTime = 0
			WHERE 	entryID IN (".$entryIDs.")";
		WCF::getDB()->registerShutdownUpdate($sql);
	}

	/**
	 * Refreshes the stats of this entry.
	 */
	public function refresh() {
		self::refreshAll($this->entryID);
	}

	/**
	 * Moves all entries with the given entry ids into the category with the given category id.
	 *
	 * @param	string		$entryIDs
	 * @param	integer		$newCategoryID
	 */
	public static function moveAll($entryIDs, $newCategoryID) {
		if (empty($entryIDs)) return;

		// move entries
		$sql = "UPDATE 	wsip".WSIP_N."_news_entry
			SET	categoryID = ".$newCategoryID."
			WHERE 	entryID IN (".$entryIDs.")
				AND categoryID <> ".$newCategoryID;
		WCF::getDB()->sendQuery($sql);
	}

	/**
	 * Refreshes the stats of this entry.
	 *
	 * @param	string		$entryIDs
	 */
	public static function refreshAll($entryIDs) {
		if (empty($entryIDs)) return;

		$sql = "UPDATE 	wsip".WSIP_N."_news_entry news_entry
			SET	comments = (
					SELECT	COUNT(*)
					FROM	wsip".WSIP_N."_publication_object_comment
					WHERE	publicationObjectID = news_entry.entryID
						AND publicationType = 'news'
				)
			WHERE 	entryID IN (".$entryIDs.")";
		WCF::getDB()->sendQuery($sql);
	}

	/**
	 * Deletes the entries with the given entry ids.
	 *
	 * @param	string		$entryIDs
	 * @param	string		$reason
	 */
	public static function deleteAll($entryIDs, $reason = '') {
		if (empty($entryIDs)) return;

		$trashIDs = '';
		$deleteIDs = '';
		if (NEWS_ENTRY_ENABLE_RECYCLE_BIN) {
			$sql = "SELECT 	entryID, isDeleted
				FROM 	wsip".WSIP_N."_news_entry
				WHERE 	entryID IN (".$entryIDs.")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if ($row['isDeleted']) {
					if (!empty($deleteIDs)) $deleteIDs .= ',';
					$deleteIDs .= $row['entryID'];
				}
				else {
					if (!empty($trashIDs)) $trashIDs .= ',';
					$trashIDs .= $row['entryID'];
				}
			}
		}
		else {
			$deleteIDs = $entryIDs;
		}

		self::trashAll($trashIDs, $reason);
		self::deleteAllCompletely($deleteIDs);
	}

	/**
	 * Moves the entries with the given entry ids into the recycle bin.
	 *
	 * @param	string		$entryIDs
	 * @param	string		$reason
	 */
	public static function trashAll($entryIDs, $reason = '') {
		if (empty($entryIDs)) return;

		// trash entry
		$sql = "UPDATE 	wsip".WSIP_N."_news_entry
			SET	isDeleted = 1,
				deleteTime = ".TIME_NOW.",
				deletedBy = '".escapeString(WCF::getUser()->username)."',
				deletedByID = ".WCF::getUser()->userID.",
				deleteReason = '".escapeString($reason)."',
				isDisabled = 0
			WHERE 	entryID IN (".$entryIDs.")";
		WCF::getDB()->sendQuery($sql);
	}

	/**
	 * Deletes the entries with the given entry ids completely.
	 *
	 * @param	string		$entryIDs
	 */
	public static function deleteAllCompletely($entryIDs) {
		if (empty($entryIDs)) return;

		// delete attachments
		require_once(WCF_DIR.'lib/data/attachment/MessageAttachmentListEditor.class.php');
		$attachment = new MessageAttachmentListEditor(explode(',', $entryIDs), 'newsEntry');
		$attachment->deleteAll();

		// delete tags
		require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
		$taggable = TagEngine::getInstance()->getTaggable('com.wcfsolutions.wsip.news.entry');

		$sql = "DELETE FROM	wcf".WCF_N."_tag_to_object
			WHERE 		taggableID = ".$taggable->getTaggableID()."
					AND objectID IN (".$entryIDs.")";
		WCF::getDB()->registerShutdownUpdate($sql);

		// delete polls
		require_once(WCF_DIR.'lib/data/message/poll/PollEditor.class.php');
		PollEditor::deleteAll($entryIDs, 'newsEntry');

		// delete ratings
		$sql = "DELETE FROM	wcf".WCF_N."_rating
			WHERE		objectID IN (".$entryIDs.")
					AND objectName = 'com.wcfsolutions.wsip.news.entry'
					AND packageID = ".PACKAGE_ID;
		WCF::getDB()->sendQuery($sql);

		// delete comments
		$sql = "DELETE FROM	wsip".WSIP_N."_publication_object_comment
			WHERE		publicationObjectID IN (".$entryIDs.")
					AND publicationType = 'news'";
		WCF::getDB()->sendQuery($sql);

		// delete subscriptions
		$sql = "DELETE FROM	wsip".WSIP_N."_publication_object_subscription
			WHERE		publicationObjectID IN (".$entryIDs.")
					AND publicationType = 'news'";
		WCF::getDB()->sendQuery($sql);

		// update user posts and activity points
		self::updateUserStats($entryIDs, 'delete');

		// delete entries
		$sql = "DELETE FROM 	wsip".WSIP_N."_news_entry
			WHERE 		entryID IN (".$entryIDs.")";
		WCF::getDB()->sendQuery($sql);
	}

	/**
	 * Restores the entries with the given entry ids.
	 *
	 * @param	string		$entryIDs
	 */
	public static function restoreAll($entryIDs) {
		if (empty($entryIDs)) return;

		// restore entries
		$sql = "UPDATE 	wsip".WSIP_N."_news_entry
			SET	isDeleted = 0
			WHERE 	entryID IN (".$entryIDs.")";
		WCF::getDB()->sendQuery($sql);
	}

	/**
	 * Updates the user stats.
	 *
	 * @param	string		$entryIDs
	 * @param 	string		$mode
	 */
	public static function updateUserStats($entryIDs, $mode) {
		if (empty($entryIDs)) return;

		// update user news entries and activity points
		$userNewsEntries = array();
		$userActivityPoints = array();
		$sql = "SELECT	categoryID, userID
			FROM	wsip".WSIP_N."_news_entry
			WHERE	entryID IN (".$entryIDs.")
				".($mode != 'enable' ? "AND everEnabled = 1" : '')."
				AND userID <> 0";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$category = new Category($row['categoryID']);
			switch ($mode) {
				case 'enable':
					// entries
					if (!isset($userNewsEntries[$row['userID']])) $userNewsEntries[$row['userID']] = 0;
					$userNewsEntries[$row['userID']]++;

					// activity points
					if (!isset($userActivityPoints[$row['userID']])) $userActivityPoints[$row['userID']] = 0;
					$userActivityPoints[$row['userID']] += ACTIVITY_POINTS_PER_NEWS_ENTRY;
					break;
				case 'delete':
					// entries
					if (!isset($userNewsEntries[$row['userID']])) $userNewsEntries[$row['userID']] = 0;
					$userNewsEntries[$row['userID']]--;

					// activity points
					if (!isset($userActivityPoints[$row['userID']])) $userActivityPoints[$row['userID']] = 0;
					$userActivityPoints[$row['userID']] -= ACTIVITY_POINTS_PER_NEWS_ENTRY;
					break;
			}
		}

		// save user news entries
		if (count($userNewsEntries)) {
			require_once(WSIP_DIR.'lib/data/user/WSIPUser.class.php');
			foreach ($userNewsEntries as $userID => $entries) {
				WSIPUser::updateUserNewsEntries($userID, $entries);
			}
		}

		// save activity points
		if (count($userActivityPoints)) {
			require_once(WCF_DIR.'lib/data/user/rank/UserRank.class.php');
			foreach ($userActivityPoints as $userID => $points) {
				UserRank::updateActivityPoints($points, $userID);
			}
		}
	}

	/**
	 * Returns the categories of the entries with the given entry ids.
	 *
	 * @param	string		$entryIDs
	 * @return	array
	 */
	public static function getCategoriesByEntryIDs($entryIDs) {
		if (empty($entryIDs)) return array(array(), '', 'categories' => array(), 'categoryIDs' => '');

		$categories = array();
		$categoryIDs = '';
		$sql = "SELECT 	DISTINCT categoryID
			FROM 	wsip".WSIP_N."_news_entry
			WHERE 	entryID IN (".$entryIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($categoryIDs)) $categoryIDs .= ',';
			$categoryIDs .= $row['categoryID'];
			$categories[$row['categoryID']] = new CategoryEditor($row['categoryID']);
		}

		return array($categories, $categoryIDs, 'categories' => $categories, 'categoryIDs' => $categoryIDs);
	}

	// PublicationObjectEditor implementation
	/**
	 * @see PublicationObjectEditor::addComment()
	 */
	public function addComment(PublicationObjectComment $comment) {
		$sql = "UPDATE	wsip".WSIP_N."_news_entry
			SET	comments = comments + 1
			WHERE	entryID = ".$this->entryID;
		WCF::getDB()->sendQuery($sql);

		// reset box tab cache
		require_once(WCF_DIR.'lib/data/box/tab/BoxTab.class.php');
		BoxTab::resetBoxTabCacheByBoxTabType('newsEntries');
	}

	/**
	 * @see PublicationObjectEditor::removeComment()
	 */
	public function removeComment(PublicationObjectComment $comment) {
		$sql = "UPDATE	wsip".WSIP_N."_news_entry
			SET	comments = comments - 1
			WHERE	entryID = ".$this->entryID;
		WCF::getDB()->sendQuery($sql);

		// reset box tab cache
		require_once(WCF_DIR.'lib/data/box/tab/BoxTab.class.php');
		BoxTab::resetBoxTabCacheByBoxTabType('newsEntries');
	}
}
?>