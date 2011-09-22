<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/Category.class.php');
require_once(WSIP_DIR.'lib/data/article/TaggedArticle.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/tag/AbstractTaggableObject.class.php');

/**
 * An implementation of Taggable to support the tagging of articles.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.article
 * @category	Infinite Portal
 */
class TaggableArticle extends AbstractTaggableObject {
	/**
	 * @see Taggable::getObjectsByIDs()
	 */
	public function getObjectsByIDs($objectIDs, $taggedObjects) {
		$sql = "SELECT		*
			FROM		wsip".WSIP_N."_article
			WHERE		articleID IN (".implode(",", $objectIDs).")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$row['taggable'] = $this;
			$taggedObjects[] = new TaggedArticle(null, $row);
		}
		return $taggedObjects;
	}
	
	/**
	 * @see Taggable::countObjectsByTagID()
	 */
	public function countObjectsByTagID($tagID) {
		$accessibleCategoryIDArray = Category::getAccessibleCategoryIDArray();
		if (count($accessibleCategoryIDArray) == 0) {
			return array();
		}
		
		$sql = "SELECT		COUNT(*) AS count
			FROM		wcf".WCF_N."_tag_to_object tag_to_object
			LEFT JOIN	wsip".WSIP_N."_article article
			ON		(article.articleID = tag_to_object.objectID)
			WHERE 		tag_to_object.tagID = ".$tagID."
					AND tag_to_object.taggableID = ".$this->getTaggableID()."
					AND article.categoryID IN (".implode(',', $accessibleCategoryIDArray).")";
		$row = WCF::getDB()->getFirstRow($sql);
		return $row['count'];
	}
	
	/**
	 * @see Taggable::getObjectsByTagID()
	 */
	public function getObjectsByTagID($tagID, $limit = 0, $offset = 0) {
		$accessibleCategoryIDArray = Category::getAccessibleCategoryIDArray();
		if (count($accessibleCategoryIDArray) == 0) {
			return array();
		}
		
		$entries = array();
		$sql = "SELECT		article.*
			FROM		wcf".WCF_N."_tag_to_object tag_to_object
			LEFT JOIN	wsip".WSIP_N."_article article
			ON		(article.articleID = tag_to_object.objectID)
			WHERE		tag_to_object.tagID = ".$tagID."
					AND tag_to_object.taggableID = ".$this->getTaggableID()."
					AND article.categoryID IN (".implode(',', $accessibleCategoryIDArray).")
			ORDER BY	article.time DESC";
		$result = WCF::getDB()->sendQuery($sql, $limit, $offset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$row['taggable'] = $this;
			$entries[] = new TaggedArticle(null, $row);
		}
		return $entries;
	}
	
	/**
	 * @see Taggable::getIDFieldName()
	 */
	public function getIDFieldName() {
		return 'articleID';
	}
	
	/**
	 * @see Taggable::getResultTemplateName()
	 */
	public function getResultTemplateName() {
		return 'taggedArticles';
	}
	
	/**
	 * @see Taggable::getSmallSymbol()
	 */
	public function getSmallSymbol() {
		return StyleManager::getStyle()->getIconPath('articleS.png');
	}
	
	/**
	 * @see Taggable::getMediumSymbol()
	 */
	public function getMediumSymbol() {
		return StyleManager::getStyle()->getIconPath('articleM.png');
	}
	
	/**
	 * @see Taggable::getLargeSymbol()
	 */
	public function getLargeSymbol() {
		return StyleManager::getStyle()->getIconPath('articleL.png');
	}
}
?>