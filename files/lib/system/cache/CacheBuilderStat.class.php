<?php
// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');
require_once(WCF_DIR.'lib/data/user/User.class.php');

/**
 * Caches portal stats.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	system.cache
 * @category	Infinite Portal
 */
class CacheBuilderStat implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		$data = array();

		// amount of news entries
		$sql = "SELECT	COUNT(*) AS amount
			FROM	wsip".WSIP_N."_news_entry";
		$result = WCF::getDB()->getFirstRow($sql);
		$data['newsEntries'] = $result['amount'];

		// news entries per day
		$days = ceil((TIME_NOW - INSTALL_DATE) / 86400);
		if ($days <= 0) $days = 1;
		$data['newsEntriesPerDay'] = $data['newsEntries'] / $days;

		// amount of articles
		$sql = "SELECT	COUNT(*) AS amount
			FROM	wsip".WSIP_N."_article";
		$result = WCF::getDB()->getFirstRow($sql);
		$data['articles'] = $result['amount'];

		// articles per day
		$days = ceil((TIME_NOW - INSTALL_DATE) / 86400);
		if ($days <= 0) $days = 1;
		$data['articlesPerDay'] = $data['articles'] / $days;

		return $data;
	}
}
?>