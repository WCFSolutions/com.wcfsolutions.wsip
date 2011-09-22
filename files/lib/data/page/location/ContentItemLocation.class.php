<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/content/ContentItem.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/page/location/Location.class.php');

/**
 * ContentItemLocation is an implementation of Location for the content item page.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.page.location
 * @category	Infinite Portal
 */
class ContentItemLocation implements Location {
	/**
	 * list of content items
	 * 
	 * @var	array<ContentItem>
	 */
	public $contentItems = null;
	
	/**
	 * @see Location::cache()
	 */
	public function cache($location, $requestURI, $requestMethod, $match) {}
	
	/**
	 * @see Location::get()
	 */
	public function get($location, $requestURI, $requestMethod, $match) {
		if ($this->contentItems === null) {
			$this->readContentItems();
		}
		
		$contentItemID = $match[1];
		if (!isset($this->contentItems[$contentItemID]) || !$this->contentItems[$contentItemID]->getPermission() || (!$this->contentItems[$contentItemID]->isPublished() && !$this->contentItems[$contentItemID]->getPermission('canViewHiddenContentItem'))) {
			return '';
		}
		
		return WCF::getLanguage()->get($location['locationName'], array('$contentItem' => '<a href="index.php?page=ContentItem&amp;contentItemID='.$this->contentItems[$contentItemID]->contentItemID.SID_ARG_2ND.'">'.StringUtil::encodeHTML($this->contentItems[$contentItemID]->getTitle()).'</a>'));
	}
	
	/**
	 * Gets content items from cache.
	 */
	protected function readContentItems() {
		$this->contentItems = WCF::getCache()->get('contentItem', 'items');
	}
}
?>