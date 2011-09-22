<?php
// wsip imports
require_once(WSIP_DIR.'lib/action/AbstractNewsEntryAction.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/box/Box.class.php');

/**
 * Trashes a news entry.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	action
 * @category	Infinite Portal
 */
class NewsEntryTrashAction extends AbstractNewsEntryAction {
	/**
	 * trash reason
	 * 
	 * @var	string
	 */
	public $reason = '';
	
	/**
	 * redirection url
	 * 
	 * @var	string
	 */
	public $url = '';
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get reason
		if (isset($_REQUEST['reason'])) {
			$this->reason = StringUtil::trim($_REQUEST['reason']);
			if (CHARSET != 'UTF-8') $this->reason = StringUtil::convertEncoding('UTF-8', CHARSET, $this->reason);
		}
		
		// get url
		if (isset($_REQUEST['url'])) $this->url = $_REQUEST['url'];
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		if (!NEWS_ENTRY_ENABLE_RECYCLE_BIN) {
			throw new IllegalLinkException();
		}
		if (!$this->category->getModeratorPermission('canDeleteNewsEntry')) {
			throw new PermissionDeniedException();
		}
		
		// trash entry
		if ($this->entry != null && !$this->entry->isDeleted) {
			$this->entry->trash($this->reason);
			
			// refresh entries
			$this->category->refresh();
			
			// reset cache
			WCF::getCache()->clearResource('categoryData', true);
			WCF::getCache()->clearResource('stat');
		}
		
		// reset box tab cache
		BoxTab::resetBoxTabCacheByBoxTabType('newsEntries');
		$this->executed();
		
		// forward to page
		if ($this->url) {
			if (strpos($this->url, 'page=NewsEntry') !== false) HeaderUtil::redirect('index.php?page=NewsOverview&categoryID='.$this->entry->categoryID.SID_ARG_2ND_NOT_ENCODED);
			else HeaderUtil::redirect($this->url);
			exit;
		}
	}
}
?>