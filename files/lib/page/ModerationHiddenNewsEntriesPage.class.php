<?php
// wsip imports
require_once(WSIP_DIR.'lib/page/ModerationNewsEntriesPage.class.php');

/**
 * Shows the hidden news entries.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	page
 * @category	Infinite Portal
 */
class ModerationHiddenNewsEntriesPage extends ModerationNewsEntriesPage {
	public $action = 'hiddenNewsEntries';
	public $neededPermissions = 'mod.portal.canEnableNewsEntry';
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		$categoryIDs = Category::getModeratedCategories(array('canEnableNewsEntry'));
		$this->entryList->sqlConditions .= 'news_entry.isDisabled = 1 AND news_entry.categoryID IN ('.(!empty($categoryIDs) ? $categoryIDs : 0).')';
	}
}
?>