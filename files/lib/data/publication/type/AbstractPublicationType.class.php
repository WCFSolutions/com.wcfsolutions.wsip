<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/publication/type/PublicationType.class.php');

/**
 * Provides default implementations for publication types.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.publication.type
 * @category	Infinite Portal
 */
abstract class AbstractPublicationType implements PublicationType {
	/**
	 * @see PublicationType::enableCategorizing()
	 */
	public function enableCategorizing() {
		return false;
	}
	
	/**
	 * @see PublicationType::getObjectByID()
	 */	
	public function getObjectByID($objectID) {
		return null;
	}
	
	/**
	 * @see PublicationType::getBoxLayoutID()
	 */
	public function getBoxLayoutID() {
		return 0;
	}
	
	/**
	 * @see	PublicationType::isAccessible()
	 */
	public function isAccessible() {
		return true;
	}
}
?>