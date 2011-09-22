<?php
// wsip imports
require_once(WSIP_DIR.'lib/acp/action/AbstractContentItemAction.class.php');

/**
 * Renames a content item.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	acp.action
 * @category	Infinite Portal
 */
class ContentItemRenameAction extends AbstractContentItemAction {
	/**
	 * new title
	 * 
	 * @var string
	 */
	public $title = '';
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_POST['title'])) {
			$this->title = $_POST['title'];
			if (CHARSET != 'UTF-8') $this->title = StringUtil::convertEncoding('UTF-8', CHARSET, $this->title);
		}
	}
	
	/**
	 * @see Action::execute();
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		WCF::getUser()->checkPermission('admin.portal.canEditContentItem');
		
		// change language variable
		require_once(WCF_DIR.'lib/system/language/LanguageEditor.class.php');
		$language = new LanguageEditor(WCF::getLanguage()->getLanguageID());
		$language->updateItems(array(('wsip.contentItem.'.$this->contentItem->contentItem) => $this->title), 0, PACKAGE_ID, array('wsip.contentItem.'.$this->contentItem->contentItem => 1));
		
		// reset cache
		WCF::getCache()->clearResource('contentItem');
		$this->executed();
	}
}
?>