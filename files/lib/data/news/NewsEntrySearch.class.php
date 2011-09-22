<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/news/NewsEntrySearchResult.class.php');
require_once(WSIP_DIR.'lib/data/publication/object/AbstractPublicationObjectSearch.class.php');

/**
 * An implementation of SearchableMessageType for searching in news entries.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.news
 * @category	Infinite Portal
 */
class NewsEntrySearch extends AbstractPublicationObjectSearch {
	public $publicationType = 'news';
	public $searchableMessageType = 'newsEntry';
	public $neededCategoryPermissions = array('canViewCategory', 'canEnterCategory', 'canReadNewsEntry');
	
	/**
	 * @see SearchableMessageType::cacheMessageData()
	 */
	public function cacheMessageData($messageIDs, $additionalData = null) {
		// get entries
		$sql = "SELECT	*
			FROM	wsip".WSIP_N."_news_entry
			WHERE	entryID IN (".$messageIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$entry = new NewsEntrySearchResult(null, $row);
			$this->messageCache[$row['entryID']] = array('type' => 'newsEntry', 'message' => $entry);
		}
	}
	
	/**
	 * @see SearchableMessageType::getConditions()
	 */
	public function getConditions($form = null) {
		// get selected categories
		$selectedCategoryIDs = $this->getSelectedCategories($form);
		
		// build final condition
		require_once(WCF_DIR.'lib/system/database/ConditionBuilder.class.php');
		$condition = new ConditionBuilder(false);
		
		// category ids
		if (!empty($selectedCategoryIDs)) $condition->add('messageTable.categoryID IN ('.$selectedCategoryIDs.')');
		$condition->add('messageTable.isDeleted = 0');
		$condition->add('messageTable.isDisabled = 0');
		
		// language
		if (count(WCF::getSession()->getVisibleLanguageIDArray())) $condition->add('messageTable.languageID IN ('.implode(',', WCF::getSession()->getVisibleLanguageIDArray()).')');
		
		// return sql condition
		return $condition->get();
	}
	
	/**
	 * @see SearchableMessageType::getTableName()
	 */
	public function getTableName() {
		return 'wsip'.WSIP_N.'_news_entry';
	}
	
	/**
	 * @see SearchableMessageType::getIDFieldName()
	 */
	public function getIDFieldName() {
		return 'entryID';
	}
	
	/**
	 * @see SearchableMessageType::getResultTemplateName()
	 */
	public function getResultTemplateName() {
		return 'searchResultNewsEntry';
	}
	
	/**
	 * @see SearchableMessageType::isAccessible()
	 */
	public function isAccessible() {
		return (MODULE_NEWS && parent::isAccessible());
	}
}
?>