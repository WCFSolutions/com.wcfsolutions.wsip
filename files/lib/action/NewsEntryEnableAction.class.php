<?php
// wsip imports
require_once(WSIP_DIR.'lib/action/AbstractNewsEntryAction.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/box/Box.class.php');

/**
 * Enables a news entry.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	action
 * @category	Infinite Portal
 */
class NewsEntryEnableAction extends AbstractNewsEntryAction {
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();

		// check permission
		$this->category->checkModeratorPermission('canEnableNewsEntry');

		// enable entry
		if ($this->entry->isDisabled) {
			$this->entry->enable();

			// refresh entries
			$this->category->refresh();

			// reset cache
			WCF::getCache()->clearResource('categoryData', true);
			WCF::getCache()->clearResource('stat');
		}

		// reset box tab cache
		BoxTab::resetBoxTabCacheByBoxTabType('newsEntries');
		$this->executed();
	}
}
?>