<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/CategoryEditor.class.php');
require_once(WSIP_DIR.'lib/data/news/NewsEntryEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');

/**
 * Provides default implementations for news entry actions.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	action
 * @category	Infinite Portal
 */
abstract class AbstractNewsEntryAction extends AbstractSecureAction {
	/**
	 * entry id
	 *
	 * @var	integer
	 */
	public $entryID = 0;

	/**
	 * entry editor object
	 *
	 * @var	NewsEntryEditor
	 */
	public $entry = null;

	/**
	 * category object
	 *
	 * @var	Category
	 */
	public $category = null;

	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// check module
		if (MODULE_NEWS != 1) {
			throw new IllegalLinkException();
		}

		// get entry
		if (isset($_REQUEST['entryID'])) $this->entryID = intval($_REQUEST['entryID']);
		$this->entry = new NewsEntryEditor($this->entryID);
		if (!$this->entry->entryID) {
			throw new IllegalLinkException();
		}

		// get category
		$this->category = new CategoryEditor($this->entry->categoryID);
		$this->entry->enter($this->category);
	}
}
?>