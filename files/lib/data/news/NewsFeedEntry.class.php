<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/news/ViewableNewsEntry.class.php');

/**
 * Represents a viewable news entry in a rss or an atom feed.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.news
 * @category	Infinite Portal
 */
class NewsFeedEntry extends ViewableNewsEntry {
	/**
	 * @see ViewableNewsEntry::getFormattedMessage()
	 */
	public function getFormattedMessage() {
		// replace relative urls
		$text = preg_replace('~(?<=href="|src=")(?![a-z0-9]+://)~i', PAGE_URL.'/', parent::getFormattedMessage());

		return StringUtil::escapeCDATA($text);
	}
}
?>