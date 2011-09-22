<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/publication/Publication.class.php');
require_once(WSIP_DIR.'lib/data/publication/object/comment/PublicationObjectCommentEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');

/**
 * Deletes a publication object comment.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	action
 * @category	Infinite Portal
 */
class PublicationObjectCommentDeleteAction extends AbstractSecureAction {
	/**
	 * comment id
	 * 
	 * @var integer
	 */
	public $commentID = 0;
	
	/**
	 * comment editor object
	 * 
	 * @var PublicationObjectCommentEditor
	 */
	public $comment = null;
	
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
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// check module
		if (MODULE_COMMENT != 1) {
			throw new IllegalLinkException();
		}
		
		// get comment
		if (isset($_REQUEST['commentID'])) $this->commentID = intval($_REQUEST['commentID']);
		$this->comment = new PublicationObjectCommentEditor($this->commentID);
		if (!$this->comment->commentID) {
			throw new IllegalLinkException();
		}
		
		// get publication type
		$this->publicationType = Publication::getPublicationTypeObject($this->comment->publicationType);
		
		// get publication object
		$this->publicationObj = $this->publicationType->getObjectByID($this->comment->publicationObjectID);
		if ($this->publicationObj === null) {
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		if (!$this->comment->isDeletable($this->publicationObj)) {
			throw new PermissionDeniedException();
		}
		
		// delete comment
		$this->comment->delete();
		$this->publicationObj->getEditor()->removeComment($this->comment);
		$this->executed();
		
		// forward
		HeaderUtil::redirect($this->publicationObj->getURL().SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>