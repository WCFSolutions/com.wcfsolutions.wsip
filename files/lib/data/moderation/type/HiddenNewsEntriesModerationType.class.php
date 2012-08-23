<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/Category.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/moderation/type/AbstractModerationType.class.php');

/**
 * Represents the moderation type for hidden news entries.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.moderation.type
 * @category	Infinite Portal
 */
class HiddenNewsEntriesModerationType extends AbstractModerationType {
	/**
	 * @see ModerationType::getName()
	 */
	public function getName() {
		return 'hiddenNewsEntries';
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
		return "index.php?page=ModerationHiddenNewsEntries".SID_ARG_2ND;
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
		return WCF::getUser()->getPermission('mod.portal.canEnableNewsEntry');
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
		$categoryIDs = Category::getModeratedCategories(array('canEnableNewsEntry'));
		if (!empty($categoryIDs)) {
			$sql = "SELECT	COUNT(*) AS count
				FROM	wsip".WSIP_N."_news_entry
				WHERE	isDisabled = 1
					AND categoryID IN (".$categoryIDs.")";
			$row = WCF::getDB()->getFirstRow($sql);
			return $row['count'];
		}
		return 0;
	}
}
?>