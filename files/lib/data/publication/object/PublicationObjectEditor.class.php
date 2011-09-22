<?php
/**
 * Any publication object editor should implement this interface.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.publication.object
 * @category	Infinite Portal
 */
interface PublicationObjectEditor {
	/**
	 * Adds the given comment to this publication object.
	 * 
	 * @param	PublicationObjectComment	$comment
	 */
	public function addComment(PublicationObjectComment $comment);
	
	/**
	 * Removes the given comment from this publication object.
	 * 
	 * @param	PublicationObjectComment	$comment
	 */
	public function removeComment(PublicationObjectComment $comment);
}
?>