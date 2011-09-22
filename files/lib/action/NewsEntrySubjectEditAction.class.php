<?php
// wsip imports
require_once(WSIP_DIR.'lib/action/AbstractNewsEntryAction.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/box/Box.class.php');

/**
 * Edits the subject of a news entry.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	action
 * @category	Infinite Portal
 */
class NewsEntrySubjectEditAction extends AbstractNewsEntryAction {
	/**
	 * new subject
	 * 
	 * @var	string
	 */
	public $subject = '';
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get subject
		if (isset($_REQUEST['subject'])) {
			$this->subject = StringUtil::trim($_REQUEST['subject']);
			if (CHARSET != 'UTF-8') $this->subject = StringUtil::convertEncoding('UTF-8', CHARSET, $this->subject);
		}
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		$this->category->checkModeratorPermission('canEditNewsEntry');
		
		// edit subject	
		$this->entry->setSubject($this->subject);
		
		// reset cache
		WCF::getCache()->clearResource('categoryData', true);
		
		// reset box tab cache
		BoxTab::resetBoxTabCacheByBoxTabType('newsEntries');
		$this->executed();
	}
}
?>