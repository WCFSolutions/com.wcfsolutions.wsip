<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/Category.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/moderation/type/AbstractModerationType.class.php');

/**
 * Represents the moderation type for marked news entries.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.moderation.type
 * @category	Infinite Portal
 */
class MarkedNewsEntriesModerationType extends AbstractModerationType {
	/**
	 * @see ModerationType::getName()
	 */
	public function getName() {
		return 'markedNewsEntries';
	}

	/**
	 * @see ModerationType::getIcon()
	 */
	public function getIcon() {
		return 'newsEntry';
	}

	/**
	 * Returns the url of this moderation type.
	 *
	 * @return	string
	 */
	public function getURL() {
		return "index.php?page=ModerationMarkedNewsEntries".SID_ARG_2ND;
	}

	/**
	 * @see ModerationType::isImportant()
	 */
	public function isImportant() {
		return false;
	}

	/**
	 * @see ModerationType::isModerator()
	 */
	public function isModerator() {
		return intval(WCF::getUser()->getPermission('mod.portal.canDeleteNewsEntry') || WCF::getUser()->getPermission('mod.portal.canMoveNewsEntry'));
	}

	/**
	 * @see	ModerationType::isAccessible()
	 */
	public function isAccessible() {
		return MODULE_NEWS;
	}

	/**
	 * @see ModerationType::getOutstandingModerations()
	 */
	public function getOutstandingModerations() {
		return (($markedEntries = WCF::getSession()->getVar('markedNewsEntries')) ? count($markedEntries) : 0);
	}
}
?>