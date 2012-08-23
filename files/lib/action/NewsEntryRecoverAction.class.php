<?php
// wsip imports
require_once(WSIP_DIR.'lib/action/AbstractNewsEntryAction.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/box/Box.class.php');

/**
 * Recovers a news entry.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	action
 * @category	Infinite Portal
 */
class NewsEntryRecoverAction extends AbstractNewsEntryAction {
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();

		if (!NEWS_ENTRY_ENABLE_RECYCLE_BIN) {
			throw new IllegalLinkException();
		}

		// check permission
		$this->category->checkModeratorPermission('canDeleteNewsEntry');

		// trash entry
		if ($this->entry->isDeleted) {
			$this->entry->restore();

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