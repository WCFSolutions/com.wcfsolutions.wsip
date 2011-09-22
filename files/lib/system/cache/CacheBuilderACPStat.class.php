<?php
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Caches the acp stats.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	system.cache
 * @category	Infinite Portal
 */
class CacheBuilderACPStat implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		$data = array();
		
		// get installation age
		$installationAge = (TIME_NOW - INSTALL_DATE) / 86400;
		if ($installationAge < 1) $installationAge = 1;
		
		// members
		$sql = "SELECT	COUNT(*) AS members
			FROM	wcf".WCF_N."_user";
		$row = WCF::getDB()->getFirstRow($sql);
		$data['members'] = $row['members'];
		
		// news categories
		$sql = "SELECT	COUNT(*) AS categories
			FROM	wsip".WSIP_N."_category";
		$row = WCF::getDB()->getFirstRow($sql);
		$data['categories'] = $row['categories'];
		
		// news entries
		$sql = "SELECT	COUNT(*) AS newsEntries
			FROM	wsip".WSIP_N."_news_entry";
		$row = WCF::getDB()->getFirstRow($sql);
		$data['newsEntries'] = $row['newsEntries'];
		$data['newsEntriesPerDay'] = $row['newsEntries'] / $installationAge;
		
		// boxes
		$sql = "SELECT	COUNT(*) AS boxes
			FROM	wcf".WCF_N."_box
			WHERE	packageID = ".PACKAGE_ID;
		$row = WCF::getDB()->getFirstRow($sql);
		$data['boxes'] = $row['boxes'];
		
		// attachments
		$sql = "SELECT	COUNT(*) AS attachments,
				IFNULL((SUM(attachmentSize) + SUM(thumbnailSize)), 0) AS attachmentsSize
			FROM	wcf".WCF_N."_attachment
			WHERE	packageID = ".PACKAGE_ID;
		$row = WCF::getDB()->getFirstRow($sql);
		$data['attachments'] = $row['attachments'];
		$data['attachmentsSize'] = $row['attachmentsSize'];

		// database entries and size
		$data['databaseSize'] = 0;
		$data['databaseEntries'] = 0;
		$sql = "SHOW TABLE STATUS";
		$result = WCF::getDB()->sendQuery($sql);
		while($row = WCF::getDB()->fetchArray($result)) {
			$data['databaseSize'] += $row['Data_length'] + $row['Index_length'];
			$data['databaseEntries'] += $row['Rows'];
		}
		
		return $data;
	}
}
?>