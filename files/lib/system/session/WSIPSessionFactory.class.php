<?php
// wsip imports
require_once(WSIP_DIR.'lib/system/session/WSIPSession.class.php');
require_once(WSIP_DIR.'lib/data/user/WSIPUserSession.class.php');
require_once(WSIP_DIR.'lib/data/user/WSIPGuestSession.class.php');

// wcf imports
require_once(WCF_DIR.'lib/system/session/CookieSessionFactory.class.php');

/**
 * WSIPSessionFactory extends the CookieSessionFactory class with portal specific functions.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	system.session
 * @category	Infinite Portal
 */
class WSIPSessionFactory extends CookieSessionFactory {
	protected $guestClassName = 'WSIPGuestSession';
	protected $userClassName = 'WSIPUserSession';
	protected $sessionClassName = 'WSIPSession';
}
?>