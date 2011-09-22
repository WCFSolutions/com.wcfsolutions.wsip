<?php
/**
 * A publication type should implement this interface.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.publication.type
 * @category	Infinite Portal
 */
interface PublicationType {
	/**
	 * Returns true, if the publication type enables categorizing.
	 * 
	 * @return	boolean
	 */	
	public function enableCategorizing();
	
	/**
	 * Returns the publication object with the given publication object id.
	 * 
	 * @param	integer		$objectID
	 * @return	mixed
	 */
	public function getObjectByID($objectID);
	
	/**
	 * Returns the box layout of this publication type.
	 * 
	 * @return	integer
	 */
	public function getBoxLayoutID();
	
	/**
	 * Returns true, if the publication type is accessible.
	 *
	 * @return	boolean
	 */
	public function isAccessible();
}
?>