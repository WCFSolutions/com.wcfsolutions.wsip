<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/CategoryEditor.class.php');
require_once(WSIP_DIR.'lib/data/news/NewsEntryEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');

/**
 * Moves all marked news entries.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	action
 * @category	Infinite Portal
 */
class NewsEntryMoveMarkedAction extends AbstractSecureAction {
	/**
	 * category id
	 * 
	 * @var integer
	 */
	public $categoryID = 0;
	
	/**
	 * category object
	 * 
	 * @var Category
	 */
	public $category = null;
	
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
		
		// get category
		if (isset($_REQUEST['categoryID'])) $this->categoryID = intval($_REQUEST['categoryID']);
		$this->category = new Category($this->categoryID);
		$this->category->enter('news');
		
		// get url		
		if (isset($_REQUEST['url'])) $this->url = $_REQUEST['url'];
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// check permission		
		$this->category->checkModeratorPermission('canMoveNewsEntry');
		
		// get marked entries
		$markedEntryIDArray = WCF::getSession()->getVar('markedNewsEntries');
		if ($markedEntryIDArray === null) throw new IllegalLinkException();
		$markedEntryIDs = implode(',', $markedEntryIDArray);
		
		// get categories
		list($categories, $categoryIDs) = NewsEntryEditor::getCategoriesByEntryIDs($markedEntryIDs);
		
		// check permissions
		foreach ($categories as $category) {
			$category->checkModeratorPermission('canMoveNewsEntry');
		}
		
		// move entries
		NewsEntryEditor::moveAll($markedEntryIDs, $this->categoryID);
		NewsEntryEditor::unmarkAll();
		
		// refresh stats
		CategoryEditor::refreshAll($categoryIDs.','.$this->category->categoryID);
		
		// reset cache
		WCF::getCache()->clearResource('categoryData', true);
		WCF::getCache()->clearResource('stat');
		$this->executed();
		
		// forward to page
		HeaderUtil::redirect($this->url);
		exit;
	}
}
?>