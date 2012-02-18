<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/Category.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/page/location/Location.class.php');

/**
 * ArticleSectionLocation is an implementation of Location for the article section page.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.page.location
 * @category	Infinite Portal
 */
class ArticleSectionLocation implements Location {
	/**
	 * list of cached entry ids
	 * 
	 * @var	array
	 */
	public $cachedSectionIDs = array();
	
	/**
	 * list of articles
	 * 
	 * @var	array
	 */
	public $articles = null;
	
	/**
	 * @see Location::cache()
	 */
	public function cache($location, $requestURI, $requestMethod, $match) {
		$this->cachedSectionIDs[] = $match[1];
	}
	
	/**
	 * @see Location::get()
	 */
	public function get($location, $requestURI, $requestMethod, $match) {
		if ($this->articles === null) {
			$this->readArticles();
		}
		
		$sectionID = $match[1];
		if (!isset($this->articles[$sectionID])) {
			return '';
		}
		
		return WCF::getLanguage()->get($location['locationName'], array('$article' => '<a href="index.php?page=Article&amp;sectionID='.$this->articles[$sectionID]['firstSectionID'].SID_ARG_2ND.'">'.StringUtil::encodeHTML($this->articles[$sectionID]['subject']).'</a>'));
	}
	
	/**
	 * Gets the articles.
	 */
	protected function readArticles() {
		$this->articles = array();
		
		if (!count($this->cachedSectionIDs)) {
			return;
		}
		
		// get accessible categories
		$categoryIDs = Category::getAccessibleCategories();
		if (empty($categoryIDs)) return;
		
		$sql = "SELECT		article_section.sectionID, article.firstSectionID, article.subject
			FROM		wsip".WSIP_N."_article_section article_section
			LEFT JOIN	wsip".WSIP_N."_article article
			ON		(article.articleID = article_section.articleID)
			WHERE		article_section.sectionID IN (".implode(',', $this->cachedSectionIDs).")
					AND article.categoryID IN (".$categoryIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->articles[$row['sectionID']] = $row;
		}
	}
}
?>