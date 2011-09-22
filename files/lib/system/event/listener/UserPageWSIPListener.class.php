<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/Category.class.php');

// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Shows the last news entries and articles.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	system.event.listener
 * @category	Infinite Portal
 */
class UserPageWSIPListener implements EventListener {
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		$user = $eventObj->frame->getUser();
		
		// news
		if (MODULE_NEWS && PROFILE_SHOW_LAST_NEWS_ENTRIES) {
			require_once(WSIP_DIR.'lib/data/news/ViewableNewsEntry.class.php');
			$categoryIDArray = Category::getAccessibleCategoryIDArray(array('canViewCategory', 'canEnterCategory', 'canReadNewsEntry'));
			
			if (count($categoryIDArray)) {
				$entries = array();
				$sql = "SELECT		entryID, subject, time
					FROM		wsip".WSIP_N."_news_entry
					WHERE		userID = ".$user->userID."
							AND isDeleted = 0
							AND isDisabled = 0
							AND categoryID IN (".implode(',', $categoryIDArray).")
							".(count(WCF::getSession()->getVisibleLanguageIDArray()) ? "AND languageID IN (".implode(',', WCF::getSession()->getVisibleLanguageIDArray()).")" : "")."
					ORDER BY	time DESC";
				$result = WCF::getDB()->sendQuery($sql, 5);
				while ($row = WCF::getDB()->fetchArray($result)) {
					$entries[] = new ViewableNewsEntry(null, $row);
				}
				
				if (count($entries)) {
					WCF::getTPL()->assign(array(
						'entries' => $entries,
						'user' => $user
					));
					WCF::getTPL()->append('additionalContent2', WCF::getTPL()->fetch('userProfileLastNewsEntries'));
				}
			}
		}
		
		// articles
		if (MODULE_ARTICLE && PROFILE_SHOW_LAST_ARTICLES) {
			require_once(WSIP_DIR.'lib/data/article/Article.class.php');
			$categoryIDArray = Category::getAccessibleCategoryIDArray(array('canViewCategory', 'canEnterCategory', 'canReadArticle'));
			
			if (count($categoryIDArray)) {
				// get number of articles
				$sql = "SELECT	COUNT(*) AS count
					FROM	wsip".WSIP_N."_article
					WHERE	userID = ".$user->userID;
				$row = WCF::getDB()->getFirstRow($sql);
				$numberOfArticles = $row['count'];
				
				// get articles
				$articles = array();
				$sql = "SELECT		firstSectionID, subject, time
					FROM		wsip".WSIP_N."_article
					WHERE		userID = ".$user->userID."
							AND categoryID IN (".implode(',', $categoryIDArray).")
							".(count(WCF::getSession()->getVisibleLanguageIDArray()) ? "AND languageID IN (".implode(',', WCF::getSession()->getVisibleLanguageIDArray()).")" : "")."
					ORDER BY	time DESC";
				$result = WCF::getDB()->sendQuery($sql, 5);
				while ($row = WCF::getDB()->fetchArray($result)) {
					$articles[] = new Article(null, $row);
				}
				
				if (count($articles)) {
					WCF::getTPL()->assign(array(
						'articles' => $articles,
						'user' => $user,
						'numberOfArticles' => $numberOfArticles
					));
					WCF::getTPL()->append('additionalContent2', WCF::getTPL()->fetch('userProfileLastArticles'));
				}
			}
		}
	}
}
?>