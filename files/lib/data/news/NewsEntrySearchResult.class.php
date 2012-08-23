<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/news/ViewableNewsEntry.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/message/util/SearchResultTextParser.class.php');

/**
 * Represents a news entry search result output.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.news
 * @category	Infinite Portal
 */
class NewsEntrySearchResult extends ViewableNewsEntry {
	/**
	 * @see ViewableNewsEntry::getFormattedTeaser()
	 */
	public function getFormattedTeaser() {
		return SearchResultTextParser::parse(parent::getFormattedTeaser());
	}
}
?>