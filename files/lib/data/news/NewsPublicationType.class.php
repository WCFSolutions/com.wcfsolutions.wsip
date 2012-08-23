<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/Category.class.php');
require_once(WSIP_DIR.'lib/data/news/NewsEntry.class.php');
require_once(WSIP_DIR.'lib/data/publication/type/AbstractPublicationType.class.php');

/**
 * Represents the news publication object.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.news
 * @category	Infinite Portal
 */
class NewsPublicationType extends AbstractPublicationType {
	/**
	 * @see PublicationType::enableCategorizing()
	 */
	public function enableCategorizing() {
		return true;
	}

	/**
	 * @see PublicationType::getObjectByID()
	 */
	public function getObjectByID($objectID) {
		// get object
		$entry = new NewsEntry($objectID);
		if (!$entry->entryID) return null;

		// check permissions
		$category = Category::getCategory($entry->categoryID);
		if (!$category->getPermission('canViewCategory') || !$category->getPermission('canEnterCategory') || !$category->getPermission('canReadNewsEntry')) return null;

		// return object
		return $entry;
	}

	/**
	 * @see PublicationType::getBoxLayoutID()
	 */
	public function getBoxLayoutID() {
		return NEWS_BOX_LAYOUT;
	}

	/**
	 * @see	PublicationType::isAccessible()
	 */
	public function isAccessible() {
		return MODULE_NEWS;
	}
}
?>