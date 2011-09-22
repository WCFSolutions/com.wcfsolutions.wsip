<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/content/ContentItem.class.php');

// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Caches all content items and the content item structure.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	system.cache
 * @category	Infinite Portal
 */
class CacheBuilderContentItem implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		$data = array('contentItems' => array(), 'contentItemStructure' => array(), 'items' => array(), 'structure' => array(), 'boxes' => array());
		
		// content items
		$sql = "SELECT		* 
			FROM		wsip".WSIP_N."_content_item
			ORDER BY	showOrder";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$data['items'][$row['contentItemID']] = new ContentItem(null, $row);
			
			if (!isset($data['structure'][$row['parentID']])) {
				$data['structure'][$row['parentID']] = array();
			}
			$data['structure'][$row['parentID']][] = $row['contentItemID'];
		}
		
		// get boxes to content items
		$sql = "SELECT		*
			FROM		wsip".WSIP_N."_content_item_box
			ORDER BY	showOrder";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!isset($data['boxes'][$row['contentItemID']])) {
				$data['boxes'][$row['contentItemID']] = array();
			}
			$data['boxes'][$row['contentItemID']][$row['boxID']] = $row['showOrder'];
		}
		
		// deprecated stuff
		$data['contentItems'] = $data['items'];
		$data['contentItemStructure'] = $data['structure'];
		
		return $data;
	}
}
?>