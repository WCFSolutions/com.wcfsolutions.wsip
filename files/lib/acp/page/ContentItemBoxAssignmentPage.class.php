<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/content/ContentItemEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/box/Box.class.php');
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Shows a list of all boxes.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.page
 * @category	Community Framework
 */
class ContentItemBoxAssignmentPage extends AbstractPage {
	// system
	public $templateName = 'contentItemBoxAssignment';
	public $neededPermissions = 'admin.portal.canEditContentItem';
	
	/**
	 * removed box id
	 * 
	 * @var	integer
	 */
	public $removedBoxID = 0;
	
	/**
	 * content item id
	 * 
	 * @var	integer
	 */
	public $contentItemID = 0;
	
	/**
	 * content item editor object
	 * 
	 * @var	ContentItemEditor
	 */
	public $contentItem = null;
	
	/**
	 * list of available content items
	 * 
	 * @var	array
	 */
	public $contentItemOptions = array();
	
	/**
	 * list of available boxes
	 * 
	 * @var	array
	 */
	public $boxOptions = array();
	
	/**
	 * list of boxes
	 * 
	 * @var	array
	 */
	public $boxList = array();
	
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
		
		if (isset($_REQUEST['removedBoxID'])) $this->removedBoxID = intval($_REQUEST['removedBoxID']);
		if (isset($_REQUEST['successfulSorting'])) $this->successfulSorting = true;
		
		// get content item
		if (isset($_REQUEST['contentItemID'])) $this->contentItemID = intval($_REQUEST['contentItemID']);
		if ($this->contentItemID) {
			$this->contentItem = new ContentItemEditor($this->contentItemID);	
		}
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function readData() {
		parent::readData();
		
		// get content item options
		$this->contentItemOptions = ContentItem::getContentItemSelect(array());
		
		// get boxes
		if ($this->contentItem !== null && $this->contentItem->isBoxContainer()) {
			$boxes = WCF::getCache()->get('box-'.PACKAGE_ID);
			$boxToContentItems = WCF::getCache()->get('contentItem', 'boxes');
			
			$boxIDArray = array();
			if (isset($boxToContentItems[$this->contentItemID])) {
				$boxIDArray = $boxToContentItems[$this->contentItemID];
				foreach ($boxIDArray as $boxID => $showOrder) {
					if (!isset($boxes[$boxID])) continue;
					$this->boxList[] = array(
						'box' => $boxes[$boxID],
						'showOrder' => $showOrder
					);
				}
			}
			
			// get box options
			$this->boxOptions = Box::getBoxOptions(array_keys($boxIDArray));
		}
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// init form
		if ($this->contentItem !== null && $this->contentItem->isBoxContainer()) {
			require_once(WSIP_DIR.'lib/acp/form/ContentItemBoxAddForm.class.php');
			new ContentItemBoxAddForm($this->contentItem);
		}
		
		WCF::getTPL()->assign(array(
			'contentItemID' => $this->contentItemID,
			'contentItem' => $this->contentItem,
			'contentItemOptions' => $this->contentItemOptions,
			'boxOptions' => $this->boxOptions,
			'boxes' => $this->boxList,
			'removedBoxID' => $this->removedBoxID,
			'successfulSorting' => $this->successfulSorting
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// enable menu item
		WCFACP::getMenu()->setActiveMenuItem('wsip.acp.menu.link.content.contentItem.boxAssignment');
		
		parent::show();
	}
}
?>