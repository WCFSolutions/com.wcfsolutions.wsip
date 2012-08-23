<?php
// wcf imports
require_once(WCF_DIR.'lib/data/user/UserProfile.class.php');

/**
 * Represents a user in the portal.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.user
 * @category	Infinite Portal
 */
class WSIPUser extends UserProfile {
	protected $avatar = null;

	/**
	 * @see UserProfile::__construct()
	 */
	public function __construct($userID = null, $row = null, $username = null, $email = null) {
		$this->sqlJoins .= ' LEFT JOIN wsip'.WSIP_N.'_user wsip_user ON (wsip_user.userID = user.userID) ';
		parent::__construct($userID, $row, $username, $email);
	}

	/**
	 * Updates the amount of news entries of an user.
	 *
	 * @param	integer		$userID
	 * @param	integer		$newsEntries
	 */
	public static function updateUserNewsEntries($userID, $newsEntries) {
		$sql = "UPDATE	wsip".WSIP_N."_user
			SET	newsEntries = IF(".$newsEntries." > 0 OR newsEntries > ABS(".$newsEntries."), newsEntries + ".$newsEntries.", 0)
			WHERE	userID = ".$userID;
		WCF::getDB()->registerShutdownUpdate($sql);
	}
}
?>