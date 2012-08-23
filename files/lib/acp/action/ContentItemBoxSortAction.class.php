<?php
// wsip imports
require_once(WSIP_DIR.'lib/acp/action/AbstractContentItemAction.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/box/BoxEditor.class.php');

/**
 * Sorts the structure of boxes.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.action
 * @category	Community Framework
 */
class ContentItemBoxSortAction extends AbstractContentItemAction {
	/**
	 * box id
	 *
	 * @var integer
	 */
	public $boxID = 0;

	/**
	 * box object
	 *
	 * @var BoxEditor
	 */
	public $box = null;

	/**
	 * new show order
	 *
	 * @var integer
	 */
	public $showOrder = 0;

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

		// get show order
		if (isset($_REQUEST['showOrder'])) $this->showOrder = intval($_REQUEST['showOrder']);
	}

	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();

		// check permission
		WCF::getUser()->checkPermission('admin.portal.canEditContentItem');

		// update show order
		$this->contentItem->updateBoxShowOrder($this->boxID, $this->showOrder);

		// reset cache
		ContentItemEditor::resetCache();
		$this->executed();
	}
}
?>