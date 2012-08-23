<?php
// wsip imports
require_once(WSIP_DIR.'lib/acp/action/AbstractContentItemAction.class.php');
require_once(WSIP_DIR.'lib/data/content/ContentItemEditor.class.php');

/**
 * Deletes a content item.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	acp.action
 * @category	Infinite Portal
 */
class ContentItemDeleteAction extends AbstractContentItemAction {
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();

		// check permission
		WCF::getUser()->checkPermission('admin.portal.canDeleteContentItem');

		// delete content item
		$this->contentItem->delete();

		// reset cache
		WCF::getCache()->clearResource('contentItem');
		$this->executed();

		// forward to list page
		HeaderUtil::redirect('index.php?page=ContentItemList&deletedContentItemID='.$this->contentItemID.'&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>