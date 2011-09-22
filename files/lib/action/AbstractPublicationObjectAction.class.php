<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/publication/Publication.class.php');

// wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');

/**
 * Provides default implementations for publication object actions.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	action
 * @category	Infinite Portal
 */
abstract class AbstractPublicationObjectAction extends AbstractSecureAction {
	/**
	 * publication type
	 * 
	 * @var	string
	 */
	public $publicationType = '';
	
	/**
	 * publication object id
	 * 
	 * @var	integer
	 */
	public $publicationObjectID = 0;
	
	/**
	 * publication type object
	 * 
	 * @var PublicationType
	 */
	public $publicationTypeObj = null;
	
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
		
		// get publication type
		if (isset($_REQUEST['publicationType'])) $this->publicationType = StringUtil::trim($_REQUEST['publicationType']);
		try {
			$this->publicationTypeObj = Publication::getPublicationTypeObject($this->publicationType);
		}
		catch (SystemException $e) {
			throw new IllegalLinkException();
		}
		
		// get publication object
		if (isset($_REQUEST['publicationObjectID'])) $this->publicationObjectID = intval($_REQUEST['publicationObjectID']);
		$this->publicationObj = $this->publicationTypeObj->getObjectByID($this->publicationObjectID);
		if ($this->publicationObj === null) {
			throw new IllegalLinkException();
		}
	}
}
?>