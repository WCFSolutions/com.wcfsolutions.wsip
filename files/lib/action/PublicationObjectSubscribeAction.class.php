<?php
// wsip imports
require_once(WSIP_DIR.'lib/action/AbstractPublicationObjectAction.class.php');

/**
 * Subscribes to a publication object.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	action
 * @category	Infinite Portal
 */
class PublicationObjectSubscribeAction extends AbstractPublicationObjectAction {	
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
		
		// subscribe publication object
		if (!$this->publicationObj->isSubscribed()) {
			$sql = "INSERT INTO	wsip".WSIP_N."_publication_object_subscription
						(userID, publicationType, publicationObjectID)
				VALUES 		(".WCF::getUser()->userID.", '".escapeString($this->publicationType)."', ".$this->publicationObjectID.")";
			WCF::getDB()->sendQuery($sql);
		}
		$this->executed();
		
		// forward
		HeaderUtil::redirect($this->publicationObj->getURL().SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>