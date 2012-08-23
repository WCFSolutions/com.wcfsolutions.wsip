<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/news/ViewableNewsEntry.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObjectList.class.php');

/**
 * Represents a list of news entries.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.article.section
 * @category	Infinite Portal
 */
class ArticleSectionList {
	/**
	 * list of unsorted sections
	 *
	 * @var array<ArticleSection>
	 */
	public $sections = array();

	/**
	 * list of sections
	 *
	 * @var	array
	 */
	public $sectionList = array();

	/**
	 * Creates a new ArticleSectionList.
	 *
	 * @param	integer		$articleID
	 */
	public function __construct($articleID) {
		$this->articleID = $articleID;
	}

	/**
	 * Reads the sections.
	 */
	public function readSections() {
		$sql = "SELECT		*
			FROM		wsip".WSIP_N."_article_section
			WHERE		articleID = ".$this->articleID."
			ORDER BY	parentSectionID, showOrder";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->sections[$row['parentSectionID']][] = new ViewableArticleSection(null, $row);
		}

		// make section list
		$this->makeSectionList();
	}

	/**
	 * Returns the section list.
	 *
	 * @return	array
	 */
	public function getSectionList() {
		return $this->sectionList;
	}

	/**
	 * Renders one level of the section structure.
	 *
	 * @param	integer		$parentSectionID
	 * @param	integer		$depth
	 * @param	integer		$openParents
	 */
	protected function makeSectionList($parentSectionID = 0, $depth = 1, $openParents = 0) {
		if (!isset($this->sections[$parentSectionID])) return;

		$i = 0;
		$children = count($this->sections[$parentSectionID]);
		foreach ($this->sections[$parentSectionID] as $section) {
			$childrenOpenParents = $openParents + 1;
			$hasChildren = isset($this->sections[$section->sectionID]);
			$last = $i == count($this->sections[$parentSectionID]) - 1;
			if ($hasChildren && !$last) $childrenOpenParents = 1;

			// update section list
			$this->sectionList[] = array(
				'depth' => $depth,
				'hasChildren' => $hasChildren,
				'openParents' => ((!$hasChildren && $last) ? ($openParents) : 0),
				'section' => $section,
				'position' => $i + 1,
				'maxPosition' => $children
			);

			// make next level of the section list
			$this->makeSectionList($section->sectionID, $depth + 1, $childrenOpenParents);
			$i++;
		}
	}
}
?>