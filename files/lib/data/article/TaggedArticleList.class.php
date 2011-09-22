<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/article/ViewableArticleList.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');

/**
 * Represents a list of tagged articles.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.article
 * @category	Infinite Portal
 */
class TaggedArticleList extends ViewableArticleList {
	/**
	 * tag id
	 * 
	 * @var	integer
	 */
	public $tagID = 0;
	
	/**
	 * taggable object
	 * 
	 * @var	Taggable
	 */
	public $taggable = null;
	
	/**
	 * Creates a new TaggedArticleList object.
	 */
	public function __construct($tagID, $categoryIDArray) {
		$this->tagID = $tagID;
		$this->taggable = TagEngine::getInstance()->getTaggable('com.wcfsolutions.wsip.article');
		parent::__construct($categoryIDArray);
	}
	
	/**
	 * @see DatabaseObjectList::countObjects()
	 */
	public function countObjects() {
		if (!empty($this->sqlConditions)) {
			$sql = "SELECT	COUNT(*) AS count
				FROM	wcf".WCF_N."_tag_to_object tag_to_object,
					wsip".WSIP_N."_article article
				WHERE	tag_to_object.tagID = ".$this->tagID."
					AND tag_to_object.taggableID = ".$this->taggable->getTaggableID()."
					AND article.articleID = tag_to_object.objectID
					AND ".$this->sqlConditions;
		}
		else {
			$sql = "SELECT	COUNT(*) AS count
				FROM	wcf".WCF_N."_tag_to_object
				WHERE	tagID = ".$this->tagID."
					AND taggableID = ".$this->taggable->getTaggableID();
		}
		$row = WCF::getDB()->getFirstRow($sql);
		return $row['count'];
	}
	
	/**
	 * Gets the object ids.
	 */
	protected function readObjectIDArray() {
		$sql = "SELECT		article.articleID
			FROM		wcf".WCF_N."_tag_to_object tag_to_object,
					wsip".WSIP_N."_article article
			WHERE		tag_to_object.tagID = ".$this->tagID."
					AND tag_to_object.taggableID = ".$this->taggable->getTaggableID()."
					AND article.articleID = tag_to_object.objectID
					".(!empty($this->sqlConditions) ? "AND ".$this->sqlConditions : '')."
			".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
		$result = WCF::getDB()->sendQuery($sql, $this->sqlLimit, $this->sqlOffset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->objectIDArray[] = $row['articleID'];
		}
	}
}
?>