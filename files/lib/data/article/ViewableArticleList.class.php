<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/article/ArticleList.class.php');

/**
 * Represents a viewable list of articles.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.article
 * @category	Infinite Portal
 */
class ViewableArticleList extends ArticleList {
	/**
	 * list of object ids
	 *
	 * @var	array<integer>
	 */
	public $objectIDArray = array();

	/**
	 * list of tags
	 *
	 * @var	array
	 */
	public $tags = array();

	/**
	 * Creates a new ViewableArticleList object.
	 *
	 * @param	array		$categoryIDArray
	 */
	public function __construct($categoryIDArray) {
		$this->categoryIDArray = $categoryIDArray;
		$this->sqlConditions .= 'article.categoryID IN ('.implode(',', $categoryIDArray).')';

		// language filter
		if (count(WCF::getSession()->getVisibleLanguageIDArray())) {
			$this->sqlConditions .= " AND article.languageID IN (".implode(',', WCF::getSession()->getVisibleLanguageIDArray()).")";
		}
	}

	/**
	 * Gets the object ids.
	 */
	protected function readObjectIDArray() {
		$sql = "SELECT		article.articleID
			FROM		wsip".WSIP_N."_article article
			".(!empty($this->sqlConditions) ? "WHERE ".$this->sqlConditions : '')."
			".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
		$result = WCF::getDB()->sendQuery($sql, $this->sqlLimit, $this->sqlOffset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->objectIDArray[] = $row['articleID'];
		}
	}

	/**
	 * Gets the list of tags.
	 */
	protected function readTags() {
		if (MODULE_TAGGING) {
			require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
			$taggable = TagEngine::getInstance()->getTaggable('com.wcfsolutions.wsip.article');
			$sql = "SELECT		tag_to_object.objectID AS articleID,
						tag.tagID, tag.name
				FROM		wcf".WCF_N."_tag_to_object tag_to_object
				LEFT JOIN	wcf".WCF_N."_tag tag
				ON		(tag.tagID = tag_to_object.tagID)
				WHERE		tag_to_object.taggableID = ".$taggable->getTaggableID()."
						AND tag_to_object.languageID IN (".implode(',', (count(WCF::getSession()->getVisibleLanguageIDArray()) ? WCF::getSession()->getVisibleLanguageIDArray() : array(0))).")
						AND tag_to_object.objectID IN (".implode(',', $this->objectIDArray).")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if (!isset($this->tags[$row['articleID']])) $this->tags[$row['articleID']] = array();
				$this->tags[$row['articleID']][] = new Tag(null, $row);
			}
		}
	}

	/**
	 * @see DatabaseObjectList::readObjects()
	 */
	public function readObjects() {
		// get ids
		$this->readObjectIDArray();

		// get articles
		if (count($this->objectIDArray)) {
			$this->readTags();

			$sql = "SELECT		".(!empty($this->sqlSelects) ? $this->sqlSelects.',' : '')."
						article.*
				FROM		wsip".WSIP_N."_article article
				".$this->sqlJoins."
				WHERE 		article.articleID IN (".implode(',', $this->objectIDArray).")
				".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$this->articles[] = new Article(null, $row);
			}
		}
	}

	/**
	 * Returns the list of tags.
	 *
	 * @return	array
	 */
	public function getTags() {
		return $this->tags;
	}
}
?>