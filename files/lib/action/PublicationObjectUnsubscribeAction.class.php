<?php
// wsip imports
require_once(WSIP_DIR.'lib/action/AbstractPublicationObjectAction.class.php');

/**
 * Unsubscribes from a publication object.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	action
 * @category	Infinite Portal
 */
class PublicationObjectUnsubscribeAction extends AbstractPublicationObjectAction {	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();

		if (!WCF::getUser()->userID) {
			throw new PermissionDeniedException();
		}
		
		if (!$this->publicationObj->isCommentable()) {
			throw new IllegalLinkException();
		}
		
		// unsubscribe publication object
		if ($this->publicationObj->isSubscribed()) {
			$sql = "DELETE FROM 	wsip".WSIP_N."_publication_object_subscription
				WHERE 		userID = ".WCF::getUser()->userID."
						AND publicationType = '".escapeString($this->publicationType)."'
						AND publicationObjectID = ".$this->publicationObjectID;
			WCF::getDB()->sendQuery($sql);
		}
		$this->executed();
		
		// forward
		HeaderUtil::redirect($this->publicationObj->getURL().SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>