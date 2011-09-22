<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/Category.class.php');
require_once(WSIP_DIR.'lib/data/article/Article.class.php');
require_once(WSIP_DIR.'lib/data/publication/type/AbstractPublicationType.class.php');

/**
 * An implementation of PublicationType for the article publication type.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.article
 * @category	Infinite Portal
 */
class ArticlePublicationType extends AbstractPublicationType {
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
		$article = new Article($objectID);
		if (!$article->articleID) return null;
		
		// check permissions
		$category = Category::getCategory($article->categoryID);
		if (!$category->getPermission('canViewCategory') || !$category->getPermission('canEnterCategory') || !$category->getPermission('canReadArticle')) return null;
		
		// return object
		return $article;
	}
	
	/**
	 * @see PublicationType::getBoxLayoutID()
	 */	
	public function getBoxLayoutID() {
		return ARTICLE_BOX_LAYOUT;
	}
	
	/**
	 * @see	PublicationType::isAccessible()
	 */
	public function isAccessible() {
		return MODULE_ARTICLE;
	}
}
?>