<?php
// wsip imports
require_once(WSIP_DIR.'lib/acp/action/AbstractContentItemAction.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/box/BoxEditor.class.php');
require_once(WCF_DIR.'lib/data/box/position/BoxPosition.class.php');

/**
 * Removes a box from a box layout.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.action
 * @category	Community Framework
 */
class ContentItemBoxRemoveAction extends AbstractContentItemAction {
	/**
	 * box id
	 * 
	 * @var	integer
	 */
	public $boxID = 0;
	
	/**
	 * box editor object
	 * 
	 * @var	BoxEditor
	 */
	public $box = null;
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// check box container
		if (!$this->contentItem->isBoxContainer()) {
			throw new IllegalLinkException();
		}
		
		// get box
		if (isset($_REQUEST['boxID'])) $this->boxID = intval($_REQUEST['boxID']);
		$this->box = new BoxEditor($this->boxID);		
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		WCF::getUser()->checkPermission('admin.portal.canEditContentItem');
		
		// remove box
		$this->contentItem->removeBox($this->box->boxID);
		
		// reset cache
		ContentItemEditor::resetCache();
		$this->executed();
		
		// forward to list page
		HeaderUtil::redirect('index.php?page=ContentItemBoxAssignment&contentItemID='.$this->contentItem->contentItemID.'&removedBoxID='.$this->boxID.'&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>