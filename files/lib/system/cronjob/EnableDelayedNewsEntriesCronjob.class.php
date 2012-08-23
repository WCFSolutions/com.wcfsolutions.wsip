<?php
// wcf imports
require_once(WCF_DIR.'lib/data/cronjobs/Cronjob.class.php');

/**
 * Cronjob enables delayed news entries.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	system.cronjob
 * @category	Infinite Portal
 */
class EnableDelayedNewsEntriesCronjob implements Cronjob {
	/**
	 * @see Cronjob::execute()
	 */
	public function execute($data) {
		$entryIDs = '';
		$sql = "SELECT	entryID, userID, publishingTime
			FROM	wsip".WSIP_N."_news_entry
			WHERE	publishingTime <> 0
				AND publishingTime <= ".TIME_NOW;
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($entryIDs)) $entryIDs .= ',';
			$entryIDs .= $row['entryID'];
		}
		if (!empty($entryIDs)) {
			require_once(WSIP_DIR.'lib/data/category/CategoryEditor.class.php');
			require_once(WSIP_DIR.'lib/data/news/NewsEntryEditor.class.php');

			// enable news entries
			NewsEntryEditor::enableAll($entryIDs);

			// update news entries
			$sql = "UPDATE 	wsip".WSIP_N."_news_entry
				SET	time = publishingTime
				WHERE 	entryID IN (".$entryIDs.")";
			WCF::getDB()->sendQuery($sql);

			// get categories
			list($categories, $categoryIDs) = NewsEntryEditor::getCategoriesByEntryIDs($entryIDs);

			// refresh categories
			CategoryEditor::refreshAll($categoryIDs);

			// reset cache
			WCF::getCache()->clearResource('categoryData', true);
			WCF::getCache()->clearResource('stat');

			// reset box tab cache
			BoxTab::resetBoxTabCacheByBoxTabType('newsEntries');
		}
	}
}
?>