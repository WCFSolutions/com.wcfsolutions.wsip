<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/Category.class.php');
require_once(WSIP_DIR.'lib/data/news/ViewableNewsEntryList.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/MultipleLinkPage.class.php');

/**
 * Provides default implementations for the pages of news entry moderation.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	page
 * @category	Infinite Portal
 */
abstract class ModerationNewsEntriesPage extends MultipleLinkPage {
	// system
	public $templateName = 'moderationNewsEntries';
	public $itemsPerPage = NEWS_ENTRIES_PER_PAGE;

	/**
	 * list of entries
	 *
	 * @var	ViewableNewsEntryList
	 */
	public $entryList = null;

	/**
	 * number of marked entries
	 *
	 * @var	integer
	 */
	public $markedEntries = 0;

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// entries per page
		if (WCF::getUser()->entriesPerPage) $this->itemsPerPage = WCF::getUser()->entriesPerPage;

		// get entry list
		$this->entryList = new ViewableNewsEntryList();
	}

	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		// get entries
		$this->entryList->sqlLimit = $this->itemsPerPage;
		$this->entryList->sqlOffset = ($this->pageNo - 1) * $this->itemsPerPage;
		$this->entryList->readObjects();

		// get marked entries
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedNewsEntries'])) {
			$this->markedEntries = count($sessionVars['markedNewsEntries']);
		}
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'url' => 'index.php?page=Moderation'.ucfirst($this->action).'&pageNo='.$this->pageNo.SID_ARG_2ND_NOT_ENCODED,
			'permissions' => Category::getGlobalModeratorPermissions(),
			'markedEntries' => $this->markedEntries,
			'entries' => $this->entryList->getObjects(),
			'pageName' => 'Moderation'.ucfirst($this->action),
			'pageType' => 'moderation'
		));
	}

	/**
	 * @see Page::show()
	 */
	public function show() {
		// check module
		if (MODULE_NEWS != 1) {
			throw new IllegalLinkException();
		}

		// set active menu item
		require_once(WCF_DIR.'lib/page/util/menu/ModerationCPMenu.class.php');
		ModerationCPMenu::getInstance()->setActiveMenuItem('wcf.moderation.menu.link.'.$this->action);

		parent::show();
	}

	/**
	 * @see MultipleLinkPage::countItems()
	 */
	public function countItems() {
		parent::countItems();

		return $this->entryList->countObjects();
	}
}
?>