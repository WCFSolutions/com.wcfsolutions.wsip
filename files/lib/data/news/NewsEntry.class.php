<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/publication/object/PublicationObject.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');
require_once(WCF_DIR.'lib/data/rating/Rating.class.php');

/**
 * Represents a news entry.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.news
 * @category	Infinite Portal
 */
class NewsEntry extends DatabaseObject implements PublicationObject {
	/**
	 * Creates a new NewsEntry object.
	 *
	 * @param	integer		$entryID
	 * @param 	array<mixed>	$row
	 */
	public function __construct($entryID, $row = null) {
		if ($entryID !== null) {
			$sql = "SELECT		news_entry.*
						".(WCF::getUser()->userID ? ', IF(subscription.userID IS NOT NULL, 1, 0) AS subscribed' : '')."
				FROM 		wsip".WSIP_N."_news_entry news_entry
				".(WCF::getUser()->userID ? "
				LEFT JOIN 	wsip".WSIP_N."_publication_object_subscription subscription
				ON 		(subscription.userID = ".WCF::getUser()->userID."
						AND subscription.publicationType = 'news'
						AND subscription.publicationObjectID = news_entry.entryID)" : '')."
				WHERE 		news_entry.entryID = ".$entryID;
			$row = WCF::getDB()->getFirstRow($sql);
		}
		parent::__construct($row);
	}

	/**
	 * Returns the subject of this entry.
	 *
	 * @return	string
	 */
	public function __toString() {
		return $this->subject;
	}

	/**
	 * Enters this entry.
	 *
	 * @param	Category	$category
	 */
	public function enter($category = null) {
		// get category
		if ($category == null || $category->categoryID != $this->categoryID) {
			$category = new Category($this->categoryID);
		}
		$category->enter('news');

		// check permissions
		if ((!$category->getPermission('canReadNewsEntry') && (!$category->getPermission('canReadOwnNewsEntry') || !$this->userID || $this->userID != WCF::getUser()->userID)) || ($this->isDeleted && !$category->getModeratorPermission('canReadDeletedNewsEntry')) || ($this->isDisabled && !$category->getModeratorPermission('canEnableNewsEntry'))) {
			throw new PermissionDeniedException();
		}

		// refresh session
		WCF::getSession()->setPublicationObjectID('news', $this->entryID);
	}

	/**
	 * Returns true, if this entry is marked.
	 *
	 * @return	integer
	 */
	public function isMarked() {
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedNewsEntries'])) {
			if (in_array($this->entryID, $sessionVars['markedNewsEntries'])) return 1;
		}
		return 0;
	}

	/**
	 * Returns the formatted tesaer.
	 *
	 * @return	string
	 */
	public function getFormattedTeaser() {
		return nl2br(StringUtil::encodeHTML($this->teaser));
	}

	/**
	 * Returns the number of views per day.
	 *
	 * @return	float
	 */
	public function getViewsPerDay() {
		$age = round(((TIME_NOW - $this->time) / 86400), 0);
		if ($age > 0) {
			return $this->views / $age;
		}
		return $this->views;
	}

	/**
	 * Returns the tags of this entry.
	 *
	 * @return	array
	 */
	public function getTags($languageIDArray) {
		// include files
		require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
		require_once(WSIP_DIR.'lib/data/news/TaggedNewsEntry.class.php');

		// get tags
		return TagEngine::getInstance()->getTagsByTaggedObject(new TaggedNewsEntry(null, array(
			'entryID' => $this->entryID,
			'taggable' => TagEngine::getInstance()->getTaggable('com.wcfsolutions.wsip.news.entry')
		)), $languageIDArray);
	}

	/**
	 * Returns the news entry rating result for template output.
	 *
	 * @return	string
	 */
	public function getRatingOutput() {
		return Rating::getDynamicRatingOutput($this->rating, $this->ratings);
	}

	/**
	 * Returns true, if the active user can edit this entry.
	 *
	 * @param	Category		$category
	 * @return	boolean
	 */
	public function isEditable($category) {
		if (($this->userID && $this->userID == WCF::getUser()->userID && $category->getPermission('canEditOwnNewsEntry')) || $category->getModeratorPermission('canEditNewsEntry')) {
			return true;
		}
		return false;
	}

	/**
	 * Returns true, if the active user can delete this entry.
	 *
	 * @param	Category		$category
	 * @return	boolean
	 */
	public function isDeletable($category) {
		if (($this->userID && $this->userID == WCF::getUser()->userID && $category->getPermission('canDeleteOwnNewsEntry')) || $category->getModeratorPermission('canDeleteNewsEntry')) {
			return true;
		}
		return false;
	}

	// PublicationObject implementation
	/**
	 * @see PublicationObject::getPublicationObjectID()
	 */
	public function getPublicationObjectID() {
		return $this->entryID;
	}

	/**
	 * @see PublicationObject::getPublicationType()
	 */
	public function getPublicationType() {
		return 'news';
	}

	/**
	 * @see PublicationObject::getTitle()
	 */
	public function getTitle() {
		return $this->subject;
	}

	/**
	 * @see PublicationObject::getURL()
	 */
	public function getURL() {
		return 'index.php?page=NewsEntry&entryID='.$this->entryID;
	}

	/**
	 * @see PublicationObject::getOwnerID()
	 */
	public function getOwnerID() {
		return $this->userID;
	}

	/**
	 * @see PublicationObject::getEditor()
	 */
	public function getEditor() {
		require_once(WSIP_DIR.'lib/data/news/NewsEntryEditor.class.php');
		return new NewsEntryEditor(null, $this->data);
	}

	/**
	 * @see PublicationObject::isCommentable()
	 */
	public function isCommentable() {
		return WCF::getUser()->getPermission('user.portal.canComment');
	}

	/**
	 * @see PublicationObject::isSubscribed()
	 */
	public function isSubscribed() {
		return $this->subscribed;
	}
}
?>