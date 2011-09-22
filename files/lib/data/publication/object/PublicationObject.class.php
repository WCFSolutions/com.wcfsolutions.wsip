<?php
/**
 * Any publication object should implement this interface.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.publication.object
 * @category	Infinite Portal
 */
interface PublicationObject {
	/**
	 * Returns the publication object id.
	 * 
	 * @return 	integer
	 */
	public function getPublicationObjectID();
	
	/**
	 * Returns the publication type.
	 * 
	 * @return 	string
	 */
	public function getPublicationType();
	
	/**
	 * Returns the title of the publication object.
	 * 
	 * @return 	string
	 */	
	public function getTitle();
	
	/**
	 * Returns the url of the publication object.
	 * 
	 * @return 	string
	 */
	public function getURL();
	
	/**
	 * Returns the owner of the publication object.
	 * 
	 * @return	integer
	 */
	public function getOwnerID();
	
	/**
	 * Returns an editor object for the publication object.
	 * 
	 * @return	object
	 */
	public function getEditor();
	
	/**
	 * Returns true, if the active user can comment this publication object.
	 * 
	 * @return	boolean
	 */
	public function isCommentable();
	
	/**
	 * Returns true, if this publication object is subscribed.
	 * 
	 * @return	boolean
	 */
	public function isSubscribed();	
}
?>