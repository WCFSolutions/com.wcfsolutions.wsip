<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/CategoryEditor.class.php');
require_once(WSIP_DIR.'lib/data/news/NewsEntryEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');

/**
 * Marks / unmarks news entries.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	action
 * @category	Infinite Portal
 */
class NewsEntryMarkAction extends AbstractSecureAction {
	public $entryIDArray = array();
	public $action = '';

	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['newsEntryID'])) {
			$this->entryIDArray = ArrayUtil::toIntegerArray($_REQUEST['newsEntryID']);
			if (!is_array($this->entryIDArray)) {
				$this->entryIDArray = array($this->entryIDArray);
			}
		}
		if (isset($_POST['action'])) $this->action = $_POST['action'];
	}

	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();

		// get categories
		list($categories, $categoryIDs) = NewsEntryEditor::getCategoriesByEntryIDs(implode(',', $this->entryIDArray));

		// check permissions
		foreach ($categories as $category) {
			$category->checkModeratorPermission(array('canDeleteNewsEntry', 'canMoveNewsEntry', 'canCopyNewsEntry'));
		}

		// mark / unmark
		foreach ($this->entryIDArray as $entryID) {
			$entry = new NewsEntryEditor($entryID);
			$entry->enter();
			if ($this->action == 'mark') $entry->mark();
			else if ($this->action == 'unmark') $entry->unmark();
		}
		$this->executed();
	}
}
?>