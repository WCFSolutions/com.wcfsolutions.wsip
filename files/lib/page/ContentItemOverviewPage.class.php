<?php
// wsip imports
require_once(WSIP_DIR.'lib/acp/page/ContentItemListPage.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');

/**
 * Shows a list of all content items.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	page
 * @category	Infinite Portal
 */
class ContentItemOverviewPage extends ContentItemListPage {
	// system
	public $templateName = 'contentItemOverview';
	public $neededPermissions = array();

	/**
	 * @see Page::show()
	 */
	public function show() {
		// set active page menu item
		PageMenu::setActiveMenuItem('wsip.header.menu.contentItem');

		AbstractPage::show();
	}

	/**
	 * Renders the structured content item list.
	 */
	public function renderContentItems() {
		// render content items
		$this->contentItems = WCF::getCache()->get('contentItem', 'contentItems');
		$this->contentItemStructure = WCF::getCache()->get('contentItem', 'contentItemStructure');
		$this->clearContentItemList();
		$this->makeContentItemList();
	}

	/**
	 * Removes invisible content items from content item list.
	 *
	 * @param	integer		parentID
	 */
	protected function clearContentItemList($parentID = 0) {
		if (!isset($this->contentItemStructure[$parentID])) return;

		// remove invisible categories
		foreach ($this->contentItemStructure[$parentID] as $key => $contentItemID) {
			$contentItem = $this->contentItems[$contentItemID];
			if (!$contentItem->getPermission() || (!$contentItem->isPublished() && !$contentItem->getPermission('canViewHiddenContentItem'))) {
				unset($this->contentItemStructure[$parentID][$key]);
				continue;
			}
			$this->clearContentItemList($contentItemID);
		}

		if (!count($this->contentItemStructure[$parentID])) {
			unset($this->contentItemStructure[$parentID]);
		}
	}
}
?>