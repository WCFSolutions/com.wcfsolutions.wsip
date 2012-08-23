<?php
// wsip imports
require_once(WSIP_DIR.'lib/page/ModerationNewsEntriesPage.class.php');

/**
 * Shows the marked news entries of the active user.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	page
 * @category	Infinite Portal
 */
class ModerationMarkedNewsEntriesPage extends ModerationNewsEntriesPage {
	public $action = 'markedNewsEntries';

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		$markedEntries = WCF::getSession()->getVar('markedNewsEntries');
		$this->entryList->sqlConditions .= (!empty($this->entryList->sqlConditions) ? ' AND ' : '').'news_entry.entryID IN ('.($markedEntries ? implode(',', $markedEntries) : 0).')';
	}
}
?>