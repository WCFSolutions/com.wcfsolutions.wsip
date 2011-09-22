<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/CategoryEditor.class.php');
require_once(WSIP_DIR.'lib/data/news/NewsEntryEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');
require_once(WCF_DIR.'lib/data/box/Box.class.php');

/**
 * Deletes all marked news entries.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	action
 * @category	Infinite Portal
 */
class NewsEntryDeleteMarkedAction extends AbstractSecureAction {
	/**
	 * delete reason
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
		
		// delete marked entries
		$markedEntries = WCF::getSession()->getVar('markedNewsEntries');
		if ($markedEntries !== null) {
			$markedEntries = implode(',', $markedEntries);
			list($categories, $categoryIDs) = NewsEntryEditor::getCategoriesByEntryIDs($markedEntries);
			
			// check permissions
			$sql = "SELECT 	entryID, isDeleted, categoryID
				FROM 	wsip".WSIP_N."_news_entry
				WHERE 	entryID IN (".$markedEntries.")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if ($row['isDeleted'] || !NEWS_ENTRY_ENABLE_RECYCLE_BIN) {
					$categories[$row['categoryID']]->checkModeratorPermission('canDeleteNewsEntryCompletely');
				}
				else {
					$categories[$row['categoryID']]->checkModeratorPermission('canDeleteNewsEntry');
				}
			}
			
			// delete / trash entries
			NewsEntryEditor::deleteAll($markedEntries, $this->reason);
			NewsEntryEditor::unmarkAll();
			
			// refresh stats
			CategoryEditor::refreshAll($categoryIDs);
			
			// reset cache
			WCF::getCache()->clearResource('categoryData', true);
			WCF::getCache()->clearResource('stat');
		}
		
		// reset box tab cache
		BoxTab::resetBoxTabCacheByBoxTabType('newsEntries');
		$this->executed();
		
		// forward to page
		HeaderUtil::redirect($this->url);
		exit;
	}
}
?>