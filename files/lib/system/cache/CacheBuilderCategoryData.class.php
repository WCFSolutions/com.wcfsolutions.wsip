<?php
// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Caches the category stats.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	system.cache
 * @category	Infinite Portal
 */
class CacheBuilderCategoryData implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		$data = array('stats' => array());

		// stats
		$sql = "SELECT	categoryID, time, newsEntries, articles
			FROM 	wsip".WSIP_N."_category";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			// get days
			$days = ceil((TIME_NOW - $row['time']) / 86400);
			if ($days <= 0) $days = 1;

			// objects per day
			$row['newsEntriesPerDay'] = $row['articlesPerDay'] = 0;
			if ($row['time']) {
				$row['newsEntriesPerDay'] = $row['newsEntries'] / $days;
				$row['articlesPerDay'] = $row['articles'] / $days;
			}
			$categoryID = $row['categoryID'];
			unset($row['categoryID'], $row['time']);

			// category stats
			$data['stats'][$categoryID] = $row;
		}

		return $data;
	}
}
?>