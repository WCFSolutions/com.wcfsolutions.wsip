<?php
// wcf imports
require_once(WCF_DIR.'lib/data/tag/TagList.class.php');

/**
 * Represents a list of tags.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.article
 * @category	Infinite Portal
 */
class CategoryArticleTagList extends TagList {
	/**
	 * list of category ids
	 *
	 * @var	integer
	 */
	public $categoryIDArray = 0;

	/**
	 * Creates a new CategoryArticleTagList object.
	 *
	 * @param	array		$categoryIDArray
	 * @param	array<integer>	$languageIDArray
	 */
	public function __construct($categoryIDArray, $languageIDArray = array()) {
		parent::__construct(array('com.wcfsolutions.wsip.article'), $languageIDArray);
		$this->categoryIDArray = $categoryIDArray;
	}

	/**
	 * Gets the tag ids.
	 */
	public function getTagsIDArray() {
		$tagIDArray = array();
		$sql = "SELECT		COUNT(*) AS counter, object.tagID
			FROM 		wsip".WSIP_N."_article article,
					wcf".WCF_N."_tag_to_object object
			WHERE 		article.categoryID IN (".implode(',', $this->categoryIDArray).")
					AND object.taggableID IN (".implode(',', $this->taggableIDArray).")
					AND object.languageID IN (".implode(',', $this->languageIDArray).")
					AND object.objectID = article.articleID
			GROUP BY 	object.tagID
			".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
		$result = WCF::getDB()->sendQuery($sql, $this->sqlLimit, $this->sqlOffset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if ($row['counter'] > $this->maxCounter) $this->maxCounter = $row['counter'];
			if ($row['counter'] < $this->minCounter) $this->minCounter = $row['counter'];
			$tagIDArray[$row['tagID']] = $row['counter'];
		}

		return $tagIDArray;
	}
}
?>