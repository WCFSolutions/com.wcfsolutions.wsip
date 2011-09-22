<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/content/ContentItem.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Shows a list of all content items.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	acp.page
 * @category	Infinite Portal
 */
class ContentItemListPage extends AbstractPage {
	// system
	public $templateName = 'contentItemList';
	public $neededPermissions = array('admin.portal.canEditContentItem', 'admin.portal.canDeleteContentItem');
	
	/**
	 * list of content items
	 * 
	 * @var	array
	 */
	public $contentItems = null;

	/**
	 * content item structure
	 * 
	 * @var	array
	 */
	public $contentItemStructure = null;
	
	/**
	 * structured list of content items
	 * 
	 * @var	array
	 */
	public $contentItemList = array();
	
	/**
	 * deleted content item id
	 * 
	 * @var	integer
	 */
	public $deletedContentItemID = 0;
	
	/**
	 * True, if the list was sorted successfully.
	 *
	 * @var boolean
	 */
	public $successfulSorting = false;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['deletedContentItemID'])) $this->deletedContentItemID = intval($_REQUEST['deletedContentItemID']);
		if (isset($_REQUEST['successfulSorting'])) $this->successfulSorting = true;
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function readData() {
		parent::readData();
		
		// render content items
		$this->renderContentItems();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'contentItems' => $this->contentItemList,
			'deletedContentItemID' => $this->deletedContentItemID,
			'successfulSorting' => $this->successfulSorting
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// enable menu item
		WCFACP::getMenu()->setActiveMenuItem('wsip.acp.menu.link.content.contentItem.view');
		
		parent::show();
	}
	
	/**
	 * Renders the structured content item list.
	 */
	public function renderContentItems() {
		// render content items
		$this->contentItems = WCF::getCache()->get('contentItem', 'contentItems');
		$this->contentItemStructure = WCF::getCache()->get('contentItem', 'contentItemStructure');
		$this->makeContentItemList();
	}
	
	/**
	 * Renders one level of the content item structure.
	 *
	 * @param	integer		$parentID
	 * @param	integer		$depth
	 * @param	integer		$openParents
	 */
	protected function makeContentItemList($parentID = 0, $depth = 1, $openParents = 0) {
		if (!isset($this->contentItemStructure[$parentID])) return;
		
		$i = 0;
		$children = count($this->contentItemStructure[$parentID]);
		foreach ($this->contentItemStructure[$parentID] as $contentItemID) {
			$contentItem = $this->contentItems[$contentItemID];
			
			// add content item to list
			$childrenOpenParents = $openParents + 1;
			$hasChildren = isset($this->contentItemStructure[$contentItemID]);
			$last = $i == $children - 1;
			if ($hasChildren && !$last) $childrenOpenParents = 1;
			$this->contentItemList[] = array(
				'depth' => $depth,
				'hasChildren' => $hasChildren,
				'openParents' => ((!$hasChildren && $last) ? $openParents : 0),
				'contentItem' => $contentItem,
				'parentID' => $parentID,
				'position' => $i + 1,
				'maxPosition' => $children
			);
			
			// make next level
			$this->makeContentItemList($contentItemID, $depth + 1, $childrenOpenParents);
			
			$i++;
		}
	}
}
?>