<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/publication/object/comment/PublicationObjectComment.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/user/notification/NotificationHandler.class.php');

/**
 * Provides functions to manage publication object comments.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.publication.object.comment
 * @category	Infinite Portal
 */
class PublicationObjectCommentEditor extends PublicationObjectComment {	
	/**
	 * Updates this comment.
	 * 
	 * @param	string		$comment
	 */
	public function update($comment) {
		$sql = "UPDATE	wsip".WSIP_N."_publication_object_comment
			SET	comment = '".escapeString($comment)."'
			WHERE	commentID = ".$this->commentID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Deletes this comment.
	 */
	public function delete() {
		// revoke notifications
		NotificationHandler::revokeEvent(array('newPublicationObjectComment', 'publicationObjectSubscription'), 'publicationObjectComment', $this);
		
		// delete comment
		$sql = "DELETE FROM	wsip".WSIP_N."_publication_object_comment
			WHERE		commentID = ".$this->commentID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Creates a new comment.
	 * 
	 * @param	integer				$publicationObjectID
	 * @param	string				$publicationType
	 * @param	integer				$userID
	 * @param	string				$username
	 * @param	string				$comment
	 * @return	PublicationObjectCommentEditor
	 */
	public static function create($publicationObjectID, $publicationType, $userID, $username, $comment) {
		$sql = "INSERT INTO	wsip".WSIP_N."_publication_object_comment
					(publicationObjectID, publicationType, userID, username, comment, time, ipAddress)
			VALUES		(".$publicationObjectID.", '".escapeString($publicationType)."', ".$userID.", '".escapeString($username)."', '".escapeString($comment)."', ".TIME_NOW.", '".escapeString(WCF::getSession()->ipAddress)."')";
		WCF::getDB()->sendQuery($sql);
		
		$commentID = WCF::getDB()->getInsertID("wsip".WSIP_N."_publication_object_comment", 'commentID');
		return new PublicationObjectCommentEditor($commentID);
	}
	
	/**
	 * Sends the notifications.
	 * 
	 * @param	PublicationObject		$publicationObj
	 */
	public function sendNotifications($publicationObj) {
		// send owner notification
		if ($this->userID != $publicationObj->getOwnerID()) {
			NotificationHandler::fireEvent('newPublicationObjectComment', 'publicationObjectComment', $this->commentID, $publicationObj->getOwnerID(), array('publicationObjectTitle' => $publicationObj->getTitle(), 'publicationObjectURL' => $publicationObj->getURL()));
		}
		
		// send subscription notifications
		$sql = "SELECT		user.*
			FROM		wsip".WSIP_N."_publication_object_subscription subscription
			LEFT JOIN	wcf".WCF_N."_user user
			ON		(user.userID = subscription.userID)
			WHERE		subscription.publicationObjectID = ".$this->publicationObjectID."
					AND subscription.publicationType = '".escapeString($publicationObj->getPublicationType())."'
					AND subscription.userID <> ".$this->userID."
					AND user.userID IS NOT NULL";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			NotificationHandler::fireEvent('publicationObjectSubscription', 'publicationObjectComment', $this->commentID, $row['userID'], array('publicationObjectTitle' => $publicationObj->getTitle(), 'publicationObjectURL' => $publicationObj->getURL()));
		}
	}
}
?>