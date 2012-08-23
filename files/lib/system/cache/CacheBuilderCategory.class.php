<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/Category.class.php');

// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Caches all categories and the category structure.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	system.cache
 * @category	Infinite Portal
 */
class CacheBuilderCategory implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		$data = array('categories' => array(), 'categoryStructure' => array(), 'publicationTypes' => array());

		// categories
		$sql = "SELECT		*
			FROM		wsip".WSIP_N."_category
			ORDER BY	showOrder";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$data['categories'][$row['categoryID']] = new Category(null, $row);

			if (!isset($data['categoryStructure'][$row['parentID']])) {
				$data['categoryStructure'][$row['parentID']] = array();
			}
			$data['categoryStructure'][$row['parentID']][] = $row['categoryID'];
		}

		// category to publication types
		$sql = "SELECT 		publication_type.publicationType, category.categoryID
			FROM		wsip".WSIP_N."_category_to_publication_type publication_type
			LEFT JOIN	wsip".WSIP_N."_category category
			ON		(category.categoryID = publication_type.categoryID)";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!isset($data['publicationTypes'][$row['publicationType']])) {
				$data['publicationTypes'][$row['publicationType']] = array();
			}
			$data['publicationTypes'][$row['publicationType']][] = $row['categoryID'];
		}

		return $data;
	}
}
?>