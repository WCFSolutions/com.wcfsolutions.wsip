<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/content/ContentItemEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/form/AbstractForm.class.php');
require_once(WCF_DIR.'lib/data/box/Box.class.php');

/**
 * Shows the content item box add form.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.form
 * @category	Community Framework
 */
class ContentItemBoxAddForm extends AbstractForm {
	// parameters
	public $boxID = 0;

	/**
	 * content item object
	 *
	 * @var ContentItem
	 */
	public $contentItem = null;

	/**
	 * Creates a new ContentItemBoxAddForm object.
	 *
	 * @param	ContentItem	$contentItem
	 */
	public function __construct(ContentItem $contentItem) {
		$this->contentItem = $contentItem;
		parent::__construct();
	}

	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		// get parameters
		if (isset($_POST['boxID'])) $this->boxID = intval($_POST['boxID']);
	}

	/**
	 * @see Form::validate()
	 */
	public function validate() {
		parent::validate();

		$box = new Box($this->boxID);
		if (!$box->boxID) {
			throw new UserInputException('boxID', 'invalid');
		}

		$sql = "SELECT	COUNT(*) AS amount
			FROM	wsip".WSIP_N."_content_item_box
			WHERE	contentItemID = ".$this->contentItem->contentItemID."
				AND boxID = ".$this->boxID;
		$row = WCF::getDB()->getFirstRow($sql);
		if ($row['amount']) {
			throw new UserInputException('boxID', 'invalid');
		}
	}

	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();

		// add box
		$this->contentItem->addBox($this->boxID);

		// reset cache
		ContentItemEditor::resetCache();
		$this->saved();

		// forward
		HeaderUtil::redirect('index.php?page=ContentItemBoxAssignment&contentItemID='.$this->contentItem->contentItemID.'&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'boxID' => $this->boxID
		));
	}
}
?>