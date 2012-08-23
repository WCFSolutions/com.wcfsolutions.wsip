<?php
// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');

/**
 * Represents a publication object comment.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.publication.object.comment
 * @category	Infinite Portal
 */
class PublicationObjectComment extends DatabaseObject {
	/**
	 * Creates a new PublicationObjectComment object.
	 *
	 * @param	integer		$commentID
	 * @param 	array<mixed>	$row
	 */
	public function __construct($commentID, $row = null) {
		if ($commentID !== null) {
			$sql = "SELECT	*
				FROM 	wsip".WSIP_N."_publication_object_comment
				WHERE 	commentID = ".$commentID;
			$row = WCF::getDB()->getFirstRow($sql);
		}
		parent::__construct($row);
	}

	/**
	 * Returns true, if the active user can edit this comment.
	 *
	 * @param	PublicationObject	$publicationObj
	 * @return	boolean
	 */
	public function isEditable($publicationObj) {
		if (($publicationObj->getOwnerID() == WCF::getUser()->userID && WCF::getUser()->getPermission('user.portal.canEditComment')) || ($this->userID && $this->userID == WCF::getUser()->userID && WCF::getUser()->getPermission('user.portal.canEditOwnComment')) || WCF::getUser()->getPermission('mod.portal.canEditComment')) {
			return true;
		}
		return false;
	}

	/**
	 * Returns true, if the active user can delete this comment.
	 *
	 * @param	PublicationObject	$publicationObj
	 * @return	boolean
	 */
	public function isDeletable($publicationObj) {
		if (($publicationObj->getOwnerID() == WCF::getUser()->userID && WCF::getUser()->getPermission('user.portal.canDeleteComment')) || ($this->userID && $this->userID == WCF::getUser()->userID && WCF::getUser()->getPermission('user.portal.canDeleteOwnComment')) || WCF::getUser()->getPermission('mod.portal.canDeleteComment')) {
			return true;
		}
		return false;
	}

	/**
	 * Returns an editor object for this comment.
	 *
	 * @return	PublicationObjectCommentEditor
	 */
	public function getEditor() {
		require_once(WSIP_DIR.'lib/data/publication/object/comment/PublicationObjectCommentEditor.class.php');
		return new PublicationObjectCommentEditor(null, $this->data);
	}
}
?>