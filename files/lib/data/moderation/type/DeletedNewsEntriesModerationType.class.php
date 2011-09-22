<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/Category.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/moderation/type/AbstractModerationType.class.php');

/**
 * Represents the moderation type for deleted news entries.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.moderation.type
 * @category	Infinite Portal
 */
class DeletedNewsEntriesModerationType extends AbstractModerationType {	
	/**
	 * @see ModerationType::getName()
	 */
	public function getName() {
		return 'deletedNewsEntries';
	}
	
	/**
	 * @see ModerationType::getIcon()
	 */
	public function getIcon() {
		return 'newsEntry';
	}
	
	/**
	 * @see ModerationType::getURL()
	 */
	public function getURL() {
		return "index.php?page=ModerationDeletedNewsEntries".SID_ARG_2ND;
	}
	
	/**
	 * @see ModerationType::isImportant()
	 */	
	public function isImportant() {
		return true;
	}
	
	/**
	 * @see ModerationType::isModerator()
	 */
	public function isModerator() {
		return WCF::getUser()->getPermission('mod.portal.canReadDeletedNewsEntry');
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
		$categoryIDs = Category::getModeratedCategories(array('canReadDeletedNewsEntry'));
		if (!empty($categoryIDs)) {
			$sql = "SELECT	COUNT(*) AS count
				FROM	wsip".WSIP_N."_news_entry
				WHERE	isDeleted = 1
					AND categoryID IN (".$categoryIDs.")";
			$row = WCF::getDB()->getFirstRow($sql);
			return $row['count'];
		}
		return 0;
	}
}
?>