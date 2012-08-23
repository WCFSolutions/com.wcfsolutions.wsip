<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/article/Article.class.php');
require_once(WSIP_DIR.'lib/data/publication/object/PublicationObjectEditor.class.php');

/**
 * Provides functions to manage articles.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.article
 * @category	Infinite Portal
 */
class ArticleEditor extends Article implements PublicationObjectEditor {
	/**
	 * Moves this article to the category with the given category id.
	 *
	 * @param	integer		$categoryID
	 */
	public function moveTo($categoryID) {
		// update old category
		$sql = "UPDATE	wsip".WSIP_N."_category
			SET	articles = articles - 1
			WHERE	categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);

		// update new category
		$sql = "UPDATE	wsip".WSIP_N."_category
			SET	articles = articles + 1
			WHERE	categoryID = ".$categoryID;
		WCF::getDB()->sendQuery($sql);

		// update article
		$sql = "UPDATE 	wsip".WSIP_N."_article
			SET	categoryID = ".$categoryID."
			WHERE 	articleID = ".$this->articleID;
		WCF::getDB()->sendQuery($sql);
	}

	/**
	 * Refreshes the first section id of this article.
	 *
	 * @return	integer
	 */
	public function refreshFirstSectionID() {
		$sql = "SELECT 		sectionID
			FROM 		wsip".WSIP_N."_article_section
			WHERE 		articleID = ".$this->articleID."
			ORDER BY 	showOrder ASC";
		$row = WCF::getDB()->getFirstRow($sql);
		if (!empty($row['sectionID'])) {
			$sql = "UPDATE	wsip".WSIP_N."_article
				SET	firstSectionID = ".$row['sectionID']."
				WHERE	articleID = ".$this->articleID;
			WCF::getDB()->sendQuery($sql);
			return $row['sectionID'];
		}
		return 0;
	}

	/**
	 * Updates this article.
	 *
	 * @param	integer		$categoryID
	 * @param	integer		$languageID
	 * @param	string		$subject
	 * @param	string		$teaser
	 * @param	integer		$enableComments
	 */
	public function update($categoryID, $languageID, $subject, $teaser, $enableComments) {
		$sql = "UPDATE 	wsip".WSIP_N."_article
			SET	categoryID = ".$categoryID.",
				languageID = ".$languageID.",
				subject = '".escapeString($subject)."',
				teaser = '".escapeString($teaser)."',
				enableComments = ".$enableComments."
			WHERE 	articleID = ".$this->articleID;
		WCF::getDB()->sendQuery($sql);
	}

	/**
	 * Updates the tags of this article.
	 *
	 * @param	array		$tags
	 */
	public function updateTags($tagArray) {
		// include files
		require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
		require_once(WSIP_DIR.'lib/data/article/TaggedArticle.class.php');

		// save tags
		$tagged = new TaggedArticle(null, array(
			'articleID' => $this->articleID,
			'taggable' => TagEngine::getInstance()->getTaggable('com.wcfsolutions.wsip.article')
		));

		// delete old tags
		TagEngine::getInstance()->deleteObjectTags($tagged, array($this->languageID));

		// save new tags
		if (count($tagArray) > 0) {
			TagEngine::getInstance()->addTags($tagArray, $tagged, $this->languageID);
		}
	}

	/**
	 * Deletes this article.
	 */
	public function delete() {
		self::deleteAll($this->articleID);
	}

	/**
	 * Creates a new article.
	 *
	 * @param	integer				$categoryID
	 * @param	integer				$languageID
	 * @param	string				$subject
	 * @param	string				$message
	 * @param	string				$teaser
	 * @param	integer				$userID
	 * @param	string				$username
	 * @param	integer				$enableComments
	 * @param	array				$options
	 * @param	MessageAttachmentListEditor	$attachmentList
	 * @param	string				$ipAddress
	 * @return	array
	 */
	public static function create($categoryID, $languageID, $subject, $message, $teaser, $userID, $username, $enableComments, $options = array(), $attachmentList = null, $ipAddress = null) {
		if ($ipAddress == null) $ipAddress = WCF::getSession()->ipAddress;

		// insert article
		$sql = "INSERT INTO	wsip".WSIP_N."_article
					(categoryID, languageID, userID, username, subject, teaser, time, enableComments, ipAddress)
			VALUES		(".$categoryID.", ".$languageID.", ".$userID.", '".escapeString($username)."', '".escapeString($subject)."', '".escapeString($teaser)."', ".TIME_NOW.", ".$enableComments.", '".escapeString($ipAddress)."')";
		WCF::getDB()->sendQuery($sql);

		// get article id
		$articleID = WCF::getDB()->getInsertID("wsip".WSIP_N."_article", 'articleID');

		// create section
		$section = ArticleSectionEditor::create(0, $articleID, $subject, $message, $options, $attachmentList);

		// update first section id
		$sql = "UPDATE	wsip".WSIP_N."_article
			SET	firstSectionID = ".$section->sectionID."
			WHERE	articleID = ".$articleID;
		WCF::getDB()->sendQuery($sql);

		// get new article
		$article = new ArticleEditor($articleID);

		// return article and section
		return array($article, $section);
	}

	/**
	 * Deletes the articles with the given article ids.
	 *
	 * @param	string		$articleIDs
	 */
	public static function deleteAll($articleIDs) {
		if (empty($articleIDs)) return;

		// get all section ids
		$sectionIDs = '';
		$sql = "SELECT	sectionID
			FROM	wsip".WSIP_N."_article_section
			WHERE	articleID IN (".$articleIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($sectionIDs)) $sectionIDs .= ',';
			$sectionIDs .= $row['sectionID'];
		}
		if (!empty($sectionIDs)) {
			// delete sections
			require_once(WSIP_DIR.'lib/data/article/section/ArticleSectionEditor.class.php');
			ArticleSectionEditor::deleteAll($sectionIDs);
		}

		// delete tags
		require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
		$taggable = TagEngine::getInstance()->getTaggable('com.wcfsolutions.wsip.article');

		$sql = "DELETE FROM	wcf".WCF_N."_tag_to_object
			WHERE 		taggableID = ".$taggable->getTaggableID()."
					AND objectID IN (".$articleIDs.")";
		WCF::getDB()->registerShutdownUpdate($sql);

		// delete ratings
		$sql = "DELETE FROM	wcf".WCF_N."_rating
			WHERE		objectID IN (".$articleIDs.")
					AND objectName = 'com.wcfsolutions.wsip.article'
					AND packageID = ".PACKAGE_ID;
		WCF::getDB()->sendQuery($sql);

		// delete comments
		$sql = "DELETE FROM	wsip".WSIP_N."_publication_object_comment
			WHERE		publicationObjectID IN (".$articleIDs.")
					AND publicationType = 'article'";
		WCF::getDB()->sendQuery($sql);

		// delete subscriptions
		$sql = "DELETE FROM	wsip".WSIP_N."_publication_object_subscription
			WHERE		publicationObjectID IN (".$articleIDs.")
					AND publicationType = 'article'";
		WCF::getDB()->sendQuery($sql);

		// delete articles
		$sql = "DELETE FROM 	wsip".WSIP_N."_article
			WHERE 		articleID IN (".$articleIDs.")";
		WCF::getDB()->sendQuery($sql);
	}

	// PublicationObjectEditor implementation
	/**
	 * @see PublicationObjectEditor::addComment()
	 */
	public function addComment(PublicationObjectComment $comment) {
		$sql = "UPDATE	wsip".WSIP_N."_article
			SET	comments = comments + 1
			WHERE	articleID = ".$this->articleID;
		WCF::getDB()->sendQuery($sql);

		// reset box tab cache
		require_once(WCF_DIR.'lib/data/box/tab/BoxTab.class.php');
		BoxTab::resetBoxTabCacheByBoxTabType('articles');
	}

	/**
	 * @see PublicationObjectEditor::removeComment()
	 */
	public function removeComment(PublicationObjectComment $comment) {
		$sql = "UPDATE	wsip".WSIP_N."_article
			SET	comments = comments - 1
			WHERE	articleID = ".$this->articleID;
		WCF::getDB()->sendQuery($sql);

		// reset box tab cache
		require_once(WCF_DIR.'lib/data/box/tab/BoxTab.class.php');
		BoxTab::resetBoxTabCacheByBoxTabType('articles');
	}
}
?>