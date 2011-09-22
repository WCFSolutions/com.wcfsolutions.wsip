<?php
// wsip imports
require_once(WSIP_DIR.'lib/form/PublicationObjectCommentAddForm.class.php');

/**
 * Shows publication object comment edit form.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	form
 * @category	Infinite Portal
 */
class PublicationObjectCommentEditForm extends PublicationObjectCommentAddForm {
	/**
	 * Creates a new PublicationObjectCommentEditForm object.
	 * 
	 * @param	PublicationObjectComment	$comment
	 */
	public function __construct(PublicationObjectComment $comment) {
		$this->commentObj = $comment->getEditor();
		CaptchaForm::__construct();
	}
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		CaptchaForm::readParameters();
		
		// get publication type
		$this->publicationType = Publication::getPublicationTypeObject($this->commentObj->publicationType);
		
		// get publication object
		$this->publicationObj = $this->publicationType->getObjectByID($this->commentObj->publicationObjectID);
		
		// get comment
		if (!$this->commentObj->isEditable($this->publicationObj)) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		CaptchaForm::save();
		
		// save comment
		$this->commentObj->update($this->comment);
		$this->saved();
		
		// forward
		HeaderUtil::redirect($this->publicationObj->getURL().SID_ARG_2ND_NOT_ENCODED.'#comment'.$this->commentObj->commentID);
		exit;
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		if (!count($_POST)) {
			$this->comment = $this->commentObj->comment;
		}
	}
}
?>