<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/article/ArticleSearchResult.class.php');
require_once(WSIP_DIR.'lib/data/publication/object/AbstractPublicationObjectSearch.class.php');

/**
 * An implementation of SearchableMessageType for searching in articles.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.article
 * @category	Infinite Portal
 */
class ArticleSearch extends AbstractPublicationObjectSearch {
	public $publicationType = 'article';
	public $searchableMessageType = 'article';
	public $neededCategoryPermissions = array('canViewCategory', 'canEnterCategory', 'canReadArticle');
	
	/**
	 * @see SearchableMessageType::cacheMessageData()
	 */
	public function cacheMessageData($messageIDs, $additionalData = null) {
		// get links
		$sql = "SELECT	*
			FROM	wsip".WSIP_N."_article
			WHERE	articleID IN (".$messageIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$article = new ArticleSearchResult(null, $row);
			$this->messageCache[$row['articleID']] = array('type' => 'article', 'message' => $article);
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
		if (!empty($selectedCategoryIDs)) $condition->add('article.categoryID IN ('.$selectedCategoryIDs.')');
		
		// language
		if (count(WCF::getSession()->getVisibleLanguageIDArray())) $condition->add('article.languageID IN ('.implode(',', WCF::getSession()->getVisibleLanguageIDArray()).')');
		
		// return sql condition
		return $condition->get();
	}
	
	/**
	 * @see SearchableMessageType::getJoins()
	 */
	public function getJoins() {
		return ", wsip".WSIP_N."_article article";
	}
	
	/**
	 * @see SearchableMessageType::getTableName()
	 */
	public function getTableName() {
		return 'wsip'.WSIP_N.'_article_section';
	}
	
	/**
	 * @see SearchableMessageType::getIDFieldName()
	 */
	public function getIDFieldName() {
		return 'article.articleID';
	}
	
	/**
	 * @see SearchableMessageType::getUserIDFieldName()
	 */
	public function getUserIDFieldName() {
		return 'article.userID';
	}
	
	/**
	 * @see SearchableMessageType::getTimeFieldName()
	 */
	public function getTimeFieldName() {
		return 'article.time';
	}
	
	/**
	 * @see SearchableMessageType::getResultTemplateName()
	 */
	public function getResultTemplateName() {
		return 'searchResultArticle';
	}
	
	/**
	 * @see SearchableMessageType::isAccessible()
	 */
	public function isAccessible() {
		return (MODULE_ARTICLE && parent::isAccessible());
	}
}
?>