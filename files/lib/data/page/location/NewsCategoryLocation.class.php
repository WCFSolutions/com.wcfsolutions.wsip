<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/Category.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/page/location/Location.class.php');

/**
 * NewsCategoryLocation is an implementation of Location for the news category page.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.page.location
 * @category	Infinite Portal
 */
class NewsCategoryLocation implements Location {
	/**
	 * publication type
	 * 
	 * @var	string
	 */
	public $publicationType = 'news';
	
	/**
	 * list of categories
	 * 
	 * @var	array<Category>
	 */
	public $categories = null;
	
	/**
	 * @see Location::cache()
	 */
	public function cache($location, $requestURI, $requestMethod, $match) {}
	
	/**
	 * @see Location::get()
	 */
	public function get($location, $requestURI, $requestMethod, $match) {
		if ($this->categories === null) {
			$this->readCategories();
		}
		
		$categoryID = $match[1];
		if (!isset($this->categories[$categoryID]) || !$this->categories[$categoryID]->getPermission() || !$this->categories[$categoryID]->isAvailablePublicationType($this->publicationType)) {
			return '';
		}
		
		return WCF::getLanguage()->get($location['locationName'], array('$category' => '<a href="index.php?page='.ucfirst($this->publicationType).'Overview&amp;categoryID='.$this->categories[$categoryID]->categoryID.SID_ARG_2ND.'">'.StringUtil::encodeHTML($this->categories[$categoryID]->getTitle()).'</a>'));
	}
	
	/**
	 * Gets categories from cache.
	 */
	protected function readCategories() {
		$this->categories = WCF::getCache()->get('category', 'categories');
	}
}
?>