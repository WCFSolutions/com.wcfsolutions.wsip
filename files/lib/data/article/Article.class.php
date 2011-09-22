<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/publication/object/PublicationObject.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');

/**
 * Represents an article in the portal.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.article
 * @category	Infinite Portal
 */
class Article extends DatabaseObject implements PublicationObject {
	/**
	 * Creates a new Article object.
	 * 
	 * @param	integer		$articleID
	 * @param 	array<mixed>	$row
	 */
	public function __construct($articleID, $row = null) {
		if ($articleID !== null) {
			$sql = "SELECT		article.*
						".(WCF::getUser()->userID ? ', IF(subscription.userID IS NOT NULL, 1, 0) AS subscribed' : '')."
				FROM 		wsip".WSIP_N."_article article
				".(WCF::getUser()->userID ? "
				LEFT JOIN 	wsip".WSIP_N."_publication_object_subscription subscription
				ON 		(subscription.userID = ".WCF::getUser()->userID."
						AND subscription.publicationType = 'article'
						AND subscription.publicationObjectID = article.articleID)" : '')."
				WHERE 		article.articleID = ".$articleID;
			$row = WCF::getDB()->getFirstRow($sql);
		}
		parent::__construct($row);
	}
	
	/**
	 * Enters this article.
	 * 
	 * @param	Category		$category
	 */
	public function enter($category = null) {
		// get category
		if ($category == null || $category->categoryID != $this->categoryID) {
			$category = new Category($this->categoryID);
		}
		$category->enter('article');
		
		// check permissions
		if (!$category->getPermission('canReadArticle') && (!$category->getPermission('canReadOwnArticle') || !$this->userID || $this->userID != WCF::getUser()->userID)) {
			throw new PermissionDeniedException();
		}
		
		// refresh session
		WCF::getSession()->setPublicationObjectID('article', $this->articleID);
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
	 * Returns the tags of this article.
	 * 
	 * @return	array
	 */
	public function getTags($languageIDArray) {
		// include files
		require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
		require_once(WSIP_DIR.'lib/data/article/TaggedArticle.class.php');
		
		// get tags
		return TagEngine::getInstance()->getTagsByTaggedObject(new TaggedArticle(null, array(
			'articleID' => $this->articleID,
			'taggable' => TagEngine::getInstance()->getTaggable('com.wcfsolutions.wsip.article')
		)), $languageIDArray);
	}
	
	/**
	 * Returns the article rating result for template output.
	 * 
	 * @return	string
	 */
	public function getRatingOutput() {
		return Rating::getDynamicRatingOutput($this->rating, $this->ratings);
	}
	
	/**
	 * Returns true, if the active user can edit this article.
	 *
	 * @param	Category		$category
	 * @return	boolean
	 */	
	public function isEditable($category) {
		if (($this->userID && $this->userID == WCF::getUser()->userID && $category->getPermission('canEditOwnArticle')) || $category->getModeratorPermission('canEditArticle')) {
			return true;
		}
		return false;
	}
	
	/**
	 * Returns true, if the active user can delete this article.
	 * 
	 * @param	Category		$category
	 * @return	boolean
	 */		
	public function isDeletable($category) {
		if (($this->userID && $this->userID == WCF::getUser()->userID && $category->getPermission('canDeleteOwnArticle')) || $category->getModeratorPermission('canDeleteArticle')) {
			return true;
		}
		return false;
	}
	
	// PublicationObject implementation
	/**
	 * @see PublicationObject::getPublicationObjectID()
	 */
	public function getPublicationObjectID() {
		return $this->articleID;
	}
	
	/**
	 * @see PublicationObject::getPublicationType()
	 */
	public function getPublicationType() {
		return 'article';
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
		return 'index.php?page=Article&sectionID='.$this->firstSectionID;
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
		require_once(WSIP_DIR.'lib/data/article/ArticleEditor.class.php');
		return new ArticleEditor(null, $this->data);
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