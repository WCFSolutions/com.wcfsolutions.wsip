<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/publication/object/comment/PublicationObjectCommentEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/form/CaptchaForm.class.php');

/**
 * Shows the publication object comment add form.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	form
 * @category	Infinite Portal
 */
class PublicationObjectCommentAddForm extends CaptchaForm {
	// parameters
	public $comment = '';
	public $username = '';

	/**
	 * publication type object
	 *
	 * @var PublicationType
	 */
	public $publicationType = null;

	/**
	 * publication object editor
	 *
	 * @var PublicationObjectEditor
	 */
	public $publicationObj = null;

	/**
	 * comment editor
	 *
	 * @var PublicationObjectCommentEditor
	 */
	public $commentObj = null;

	/**
	 * Creates a new PublicationObjectCommentAddForm object.
	 *
	 * @param	PublicationType		$type
	 * @param	PublicationObjectEditor	$publicationObj
	 */
	public function __construct(PublicationType $publicationType, PublicationObjectEditor $publicationObj) {
		$this->publicationType = $publicationType;
		$this->publicationObj = $publicationObj;
		parent::__construct();
	}

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// check permission
		if (!$this->publicationObj->isCommentable()) {
			throw new PermissionDeniedException();
		}
	}

	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		// get parameters
		if (isset($_POST['comment'])) $this->comment = StringUtil::trim($_POST['comment']);
		if (isset($_POST['username'])) $this->username = StringUtil::trim($_POST['username']);
	}

	/**
	 * @see Form::validate()
	 */
	public function validate() {
		parent::validate();

		if (empty($this->comment)) {
			throw new UserInputException('comment');
		}

		if (StringUtil::length($this->comment) > WCF::getUser()->getPermission('user.portal.maxCommentLength')) {
			throw new UserInputException('comment', 'tooLong');
		}

		// username
		$this->validateUsername();
	}

	/**
	 * Validates the username.
	 */
	protected function validateUsername() {
		// only for guests
		if (WCF::getUser()->userID == 0) {
			// username
			if (empty($this->username)) {
				throw new UserInputException('username');
			}
			if (!UserUtil::isValidUsername($this->username)) {
				throw new UserInputException('username', 'notValid');
			}
			if (!UserUtil::isAvailableUsername($this->username)) {
				throw new UserInputException('username', 'notAvailable');
			}

			WCF::getSession()->setUsername($this->username);
		}
		else {
			$this->username = WCF::getUser()->username;
		}
	}

	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();

		// save comment
		$this->commentObj = PublicationObjectCommentEditor::create($this->publicationObj->getPublicationObjectID(), $this->publicationObj->getPublicationType(), WCF::getUser()->userID, $this->username, $this->comment);
		$this->publicationObj->addComment($this->commentObj);

		// send noticications
		$this->commentObj->sendNotifications($this->publicationObj);
		$this->saved();

		// forward
		HeaderUtil::redirect($this->publicationObj->getURL().'&commentID='.$this->commentObj->commentID.SID_ARG_2ND_NOT_ENCODED.'#comment'.$this->commentObj->commentID);
		exit;
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'comment' => $this->comment,
			'username' => $this->username,
			'maxTextLength' => WCF::getUser()->getPermission('user.portal.maxCommentLength')
		));
	}

	/**
	 * @see Page::show()
	 */
	public function show() {
		// check module
		if (MODULE_COMMENT != 1) {
			throw new IllegalLinkException();
		}
		parent::show();
	}
}
?>