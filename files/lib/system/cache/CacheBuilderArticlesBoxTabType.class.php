<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/article/Article.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/box/tab/BoxTab.class.php');
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Caches the last articles box tab data.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	system.cache
 * @category	Infinite Portal
 */
class CacheBuilderArticlesBoxTabType implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		$information = explode('-', $cacheResource['cache']);
		$boxTabID = $information[1];
		$languageIDs = '';
		if (count($information) == 3) {
			$languageIDs = $information[2];
		}
		
		$data = array();
		
		// get box tab
		try {
			$boxTab = new BoxTab($boxTabID);
		}
		catch (IllegalLinkException $e) {
			return $data;
		}
		if (!$boxTab->categoryIDs) return $data;
		
		// get entries
		$sql = "SELECT		*
			FROM		wsip".WSIP_N."_article
			WHERE		categoryID IN (".$boxTab->categoryIDs.")
					".(!empty($languageIDs) ? " AND languageID IN (".$languageIDs.")" : '')."
					".($boxTab->sortField == 'comments' ? 'AND enableComments = 1' : '')."
			ORDER BY	".$boxTab->sortField." ".$boxTab->sortOrder;
		$result = WCF::getDB()->sendQuery($sql, $boxTab->maxArticles);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$data[] = new Article(null, $row);
		}
		
		return $data;
	}
}
?>