<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/Category.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/page/location/Location.class.php');

/**
 * NewsEntryLocation is an implementation of Location for the news entry page.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.page.location
 * @category	Infinite Portal
 */
class NewsEntryLocation implements Location {
	/**
	 * list of cached entry ids
	 * 
	 * @var	array
	 */
	public $cachedEntryIDs = array();
	
	/**
	 * list of news entries
	 * 
	 * @var	array
	 */
	public $entries = null;
	
	/**
	 * @see Location::cache()
	 */
	public function cache($location, $requestURI, $requestMethod, $match) {
		$this->cachedEntryIDs[] = $match[1];
	}
	
	/**
	 * @see Location::get()
	 */
	public function get($location, $requestURI, $requestMethod, $match) {
		if ($this->entries === null) {
			$this->readEntries();
		}
		
		$entryID = $match[1];
		if (!isset($this->entries[$entryID])) {
			return '';
		}
		
		return WCF::getLanguage()->get($location['locationName'], array('$entry' => '<a href="index.php?page=NewsEntry&amp;entryID='.$entryID.SID_ARG_2ND.'">'.StringUtil::encodeHTML($this->entries[$entryID]['subject']).'</a>'));
	}
	
	/**
	 * Gets the entries.
	 */
	protected function readEntries() {
		$this->entries = array();
		
		if (!count($this->cachedEntryIDs)) {
			return;
		}
		
		// get accessible categories
		$categoryIDs = Category::getAccessibleCategories();
		if (empty($categoryIDs)) return;
		
		$sql = "SELECT	entryID, subject
			FROM	wsip".WSIP_N."_news_entry
			WHERE	entryID IN (".implode(',', $this->cachedEntryIDs).")
				AND isDeleted = 0
				AND isDisabled = 0
				AND categoryID IN (".$categoryIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->entries[$row['entryID']] = $row;
		}
	}
}
?>