<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/Category.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/page/location/Location.class.php');

/**
 * ArticleLocation is an implementation of Location for the article page.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.page.location
 * @category	Infinite Portal
 */
class ArticleLocation implements Location {
	/**
	 * list of cached article ids
	 *
	 * @var	array
	 */
	public $cachedArticleIDs = array();

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
		$this->cachedArticleIDs[] = $match[1];
	}

	/**
	 * @see Location::get()
	 */
	public function get($location, $requestURI, $requestMethod, $match) {
		if ($this->articles === null) {
			$this->readArticles();
		}

		$articleID = $match[1];
		if (!isset($this->articles[$articleID])) {
			return '';
		}

		return WCF::getLanguage()->get($location['locationName'], array('$article' => '<a href="index.php?page=Article&amp;sectionID='.$this->articles[$articleID]['firstSectionID'].SID_ARG_2ND.'">'.StringUtil::encodeHTML($this->articles[$articleID]['subject']).'</a>'));
	}

	/**
	 * Gets the entries.
	 */
	protected function readEntries() {
		$this->articles = array();

		if (!count($this->cachedArticleIDs)) {
			return;
		}

		// get accessible categories
		$categoryIDs = Category::getAccessibleCategories();
		if (empty($categoryIDs)) return;

		$sql = "SELECT	articleID, subject, firstSectionID
			FROM	wsip".WSIP_N."_article
			WHERE	articleID IN (".implode(',', $this->cachedArticleIDs).")
				AND categoryID IN (".$categoryIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->articles[$row['articleID']] = $row;
		}
	}
}
?>