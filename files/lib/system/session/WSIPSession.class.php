<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/user/WSIPUserSession.class.php');
require_once(WSIP_DIR.'lib/data/user/WSIPGuestSession.class.php');

// wcf imports
require_once(WCF_DIR.'lib/system/session/CookieSession.class.php');
require_once(WCF_DIR.'lib/data/user/User.class.php');

/**
 * WSIPSession extends the CookieSession class with portal specific functions.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	system.session
 * @category	Infinite Portal
 */
class WSIPSession extends CookieSession {
	protected $userSessionClassName = 'WSIPUserSession';
	protected $guestSessionClassName = 'WSIPGuestSession';
	protected $categoryID = 0;
	protected $publicationObjectID = 0;
	protected $publicationType = '';

	/**
	 * Initialises the session.
	 */
	public function init() {
		parent::init();

		// handle style id
		if ($this->user->userID) $this->styleID = $this->user->styleID;
		if (($styleID = $this->getVar('styleID')) !== null) $this->styleID = $styleID;
	}

	/**
	 * @see CookieSession::update()
	 */
	public function update() {
		$this->updateSQL .= ", portalCategoryID = ".$this->categoryID.", publicationObjectID = ".$this->publicationObjectID.", publicationType = '".escapeString($this->publicationType)."'";

		parent::update();
	}

	/**
	 * Sets the current category id for this session.
	 *
	 * @param	string		$publicationType
	 * @param	integer		$categoryID
	 */
	public function setCategoryID($publicationType, $categoryID) {
		$this->publicationType = $publicationType;
		$this->categoryID = $categoryID;
	}

	/**
	 * Sets the current entry id for this session.
	 *
	 * @param	string		$publicationType
	 * @param	integer		$publicationObjectID
	 */
	public function setPublicationObjectID($publicationType, $publicationObjectID) {
		$this->publicationType = $publicationType;
		$this->publicationObjectID = $publicationObjectID;
	}

	/**
	 * Sets the active style id.
	 *
	 * @param 	integer		$newStyleID
	 */
	public function setStyleID($newStyleID) {
		$this->styleID = $newStyleID;
		if ($newStyleID > 0) $this->register('styleID', $newStyleID);
		else $this->unregister('styleID');
	}

	/**
	 * Returns the active style id.
	 *
	 * @return	integer
	 */
	public function getStyleID() {
		return $this->styleID;
	}
}
?>