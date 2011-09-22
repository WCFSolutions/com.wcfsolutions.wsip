<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/page/location/NewsCategoryLocation.class.php');

/**
 * ArticleCategoryLocation is an implementation of Location for the article category page.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.page.location
 * @category	Infinite Portal
 */
class ArticleCategoryLocation extends NewsCategoryLocation {
	/**
	 * @see	NewsEntryLocation::$publicationType
	 */
	public $publicationType = 'article';
}
?>