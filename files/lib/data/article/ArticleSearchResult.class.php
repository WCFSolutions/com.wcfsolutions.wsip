<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/article/Article.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/message/util/SearchResultTextParser.class.php');

/**
 * Represents an article search result output.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.article
 * @category	Infinite Portal
 */
class ArticleSearchResult extends Article {
	/**
	 * @see Article::getFormattedTeaser()
	 */
	public function getFormattedTeaser() {
		return SearchResultTextParser::parse(parent::getFormattedTeaser());
	}
}
?>