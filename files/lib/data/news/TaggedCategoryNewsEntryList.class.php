<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/news/TaggedNewsEntryList.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObjectList.class.php');

/**
 * TaggedCategoryNewsEntryList provides extended functions for displaying a list of news entries of a specific tag in a category.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.news
 * @category	Infinite Portal
 */
class TaggedCategoryNewsEntryList extends TaggedNewsEntryList {
	/**
	 * Creates a new TaggedCategoryNewsEntryList object.
	 *
	 * @param	integer		$tagID
	 * @param	array		$categoryIDArray
	 */
	public function __construct($tagID, $categoryIDArray) {
		$this->sqlConditions .= 'news_entry.categoryID IN ('.implode(',', $categoryIDArray).')';

		// language filter
		if (count(WCF::getSession()->getVisibleLanguageIDArray())) {
			$this->sqlConditions .= " AND news_entry.languageID IN (".implode(',', WCF::getSession()->getVisibleLanguageIDArray()).")";
		}

		// enabled entry filter
		$enabledEntryCategoryIDArray = Category::getModeratedCategoryIDArray(array('canEnableNewsEntry'));
		$this->sqlConditions .= ' AND (news_entry.isDisabled = 0'.(count($enabledEntryCategoryIDArray) ? ' OR news_entry.categoryID IN ('.implode(',', $enabledEntryCategoryIDArray).')' : '').')';

		// deleted entry filter
		$deletedEntryCategoryIDArray = Category::getModeratedCategoryIDArray(array('canReadDeletedNewsEntry'));
		$this->sqlConditions .= ' AND (news_entry.isDeleted = 0'.(count($deletedEntryCategoryIDArray) ? ' OR news_entry.categoryID IN ('.implode(',', $deletedEntryCategoryIDArray).')' : '').')';
		parent::__construct($tagID);
	}
}
?>