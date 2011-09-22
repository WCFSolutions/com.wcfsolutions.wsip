<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/Category.class.php');
require_once(WSIP_DIR.'lib/data/news/TaggedNewsEntry.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/tag/AbstractTaggableObject.class.php');

/**
 * An implementation of Taggable to support the tagging of news entries.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.news
 * @category	Infinite Portal
 */
class TaggableNewsEntry extends AbstractTaggableObject {
	/**
	 * @see Taggable::getObjectsByIDs()
	 */
	public function getObjectsByIDs($objectIDs, $taggedObjects) {
		$sql = "SELECT		*
			FROM		wsip".WSIP_N."_news_entry
			WHERE		entryID IN (".implode(",", $objectIDs).")
					AND isDeleted = 0
					AND isDisabled = 0";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$row['taggable'] = $this;
			$taggedObjects[] = new TaggedNewsEntry(null, $row);
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
			LEFT JOIN	wsip".WSIP_N."_news_entry news_entry
			ON		(news_entry.entryID = tag_to_object.objectID)
			WHERE 		tag_to_object.tagID = ".$tagID."
					AND tag_to_object.taggableID = ".$this->getTaggableID()."
					AND news_entry.categoryID IN (".implode(',', $accessibleCategoryIDArray).")
					AND news_entry.isDeleted = 0
					AND news_entry.isDisabled = 0";
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
		$sql = "SELECT		news_entry.*
			FROM		wcf".WCF_N."_tag_to_object tag_to_object
			LEFT JOIN	wsip".WSIP_N."_news_entry news_entry
			ON		(news_entry.entryID = tag_to_object.objectID)
			WHERE		tag_to_object.tagID = ".$tagID."
					AND tag_to_object.taggableID = ".$this->getTaggableID()."
					AND news_entry.categoryID IN (".implode(',', $accessibleCategoryIDArray).")
					AND news_entry.isDisabled = 0
			ORDER BY	news_entry.time DESC";
		$result = WCF::getDB()->sendQuery($sql, $limit, $offset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$row['taggable'] = $this;
			$entries[] = new TaggedNewsEntry(null, $row);
		}
		return $entries;
	}
	
	/**
	 * @see Taggable::getIDFieldName()
	 */
	public function getIDFieldName() {
		return 'entryID';
	}
	
	/**
	 * @see Taggable::getResultTemplateName()
	 */
	public function getResultTemplateName() {
		return 'taggedNewsEntries';
	}
	
	/**
	 * @see Taggable::getSmallSymbol()
	 */
	public function getSmallSymbol() {
		return StyleManager::getStyle()->getIconPath('newsEntryS.png');
	}
	
	/**
	 * @see Taggable::getMediumSymbol()
	 */
	public function getMediumSymbol() {
		return StyleManager::getStyle()->getIconPath('newsEntryM.png');
	}
	
	/**
	 * @see Taggable::getLargeSymbol()
	 */
	public function getLargeSymbol() {
		return StyleManager::getStyle()->getIconPath('newsEntryL.png');
	}
}
?>