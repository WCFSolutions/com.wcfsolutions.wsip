<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/publication/Publication.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');

/**
 * Represents a category.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.category
 * @category	Infinite Portal
 */
class Category extends DatabaseObject {
	/**
	 * list of all categories
	 *
	 * @var	array<Category>
	 */
	protected static $categories = null;

	/**
	 * category structure
	 *
	 * @var	array
	 */
	protected static $categoryStructure = null;

	/**
	 * list of categories matched to publication types
	 *
	 * @var	array
	 */
	protected static $publicationTypes = null;

	/**
	 * category options
	 *
	 * @var	array
	 */
	protected static $categorySelect;

	/**
	 * Creates a new Category object.
	 *
	 * @param 	integer		$categoryID
	 * @param 	array		$row
	 * @param 	Category	$cacheObject
	 */
	public function __construct($categoryID, $row = null, $cacheObject = null) {
		if ($categoryID !== null) $cacheObject = self::getCategory($categoryID);
		if ($row != null) parent::__construct($row);
		if ($cacheObject != null) parent::__construct($cacheObject->data);
	}

	/**
	 * Enters this category.
	 *
	 * @param	string		$publicationType
	 */
	public function enter($publicationType = '') {
		// publication type
		if (!empty($publicationType)) {
			if (!$this->isAvailablePublicationType($publicationType)) {
				throw new IllegalLinkException();
			}

			// init publication type
			Publication::initPublicationType($publicationType);
		}

		// check permissions
		if (!$this->getPermission('canViewCategory') || !$this->getPermission('canEnterCategory')) {
			throw new PermissionDeniedException();
		}

		// refresh session
		WCF::getSession()->setCategoryID($publicationType, $this->categoryID);
	}

	/**
	 * Returns true, if this category is available in the given publication type.
	 *
	 * @param	string		$publicationType
	 * @return	boolean
	 */
	public function isAvailablePublicationType($publicationType) {
		if (self::$publicationTypes === null) self::$publicationTypes = WCF::getCache()->get('category', 'publicationTypes');
		if (isset(self::$publicationTypes[$publicationType]) && in_array($this->categoryID, self::$publicationTypes[$publicationType])) {
			return true;
		}
		return false;
	}

	/**
	 * Returns the title of this category.
	 *
	 * @return	string
	 */
	public function getTitle() {
		return WCF::getLanguage()->get('wsip.category.'.$this->category);
	}

	/**
	 * Returns the formatted description of this category.
	 *
	 * @return	string
	 */
	public function getFormattedDescription() {
		if ($this->allowDescriptionHtml) {
			return WCF::getLanguage()->get('wsip.category.'.$this->category.'.description');
		}
		return nl2br(StringUtil::encodeHTML(WCF::getLanguage()->get('wsip.category.'.$this->category.'.description')));
	}

	/**
	 * Returns a list of the parent categories of this category.
	 *
	 * @return	array
	 */
	public function getParentCategories() {
		$parentCategories = array();
		$categories = WCF::getCache()->get('category', 'categories');

		$parentCategory = $this;
		while ($parentCategory->parentID != 0) {
			$parentCategory = $categories[$parentCategory->parentID];
			array_unshift($parentCategories, $parentCategory);
		}

		return $parentCategories;
	}

	/**
	 * Returns true, if the active user has the permission with the given name on this category.
	 *
	 * @param	string		$permission
	 * @return	boolean
	 */
	public function getPermission($permission = 'canViewCategory') {
		return (boolean) WCF::getUser()->getCategoryPermission($permission, $this->categoryID);
	}

	/**
	 * Checks the requested permissions of the actie user with the given names on this category.
	 * Throws a PermissionDeniedException if no permission is true.
	 *
	 * @param	array		$permissions
	 */
	public function checkPermission($permissions) {
		if (!is_array($permissions)) $permissions = array($permissions);

		$result = false;
		foreach ($permissions as $permission) {
			$result = $result || $this->getPermission($permission);
		}

		if (!$result) {
			throw new PermissionDeniedException();
		}
	}

	/**
	 * Returns true, if the active user has the moderator permission with the given name on this category.
	 *
	 * @param	string		$permission
	 * @return	boolean
	 */
	public function getModeratorPermission($permission) {
		return (boolean) WCF::getUser()->getCategoryModeratorPermission($permission, $this->categoryID);
	}

	/**
	 * Checks the requested moderator permissions of the actie user with the given names on this category.
	 * Throws a PermissionDeniedException if one moderator permission is false.
	 *
	 * @param	array		$permissions
	 */
	public function checkModeratorPermission($permissions) {
		if (!is_array($permissions)) $permissions = array($permissions);

		$result = false;
		foreach ($permissions as $permission) {
			$result = $result || $this->getModeratorPermission($permission);
		}

		if (!$result) {
			throw new PermissionDeniedException();
		}
	}

	/**
	 * Returns the moderator permissions of the active user.
	 *
	 * @return	array
	 */
	public function getModeratorPermissions() {
		$permissions = array();

		// news entry permissions
		$permissions['canDeleteNewsEntry'] = intval($this->getModeratorPermission('canDeleteNewsEntry'));
		$permissions['canReadDeletedNewsEntry'] = intval($this->getModeratorPermission('canReadDeletedNewsEntry'));
		$permissions['canDeleteNewsEntryCompletely'] = intval($this->getModeratorPermission('canDeleteNewsEntryCompletely'));
		$permissions['canEnableNewsEntry'] = intval($this->getModeratorPermission('canEnableNewsEntry'));
		$permissions['canEditNewsEntry'] = intval($this->getModeratorPermission('canEditNewsEntry'));
		$permissions['canMoveNewsEntry'] = intval($this->getModeratorPermission('canMoveNewsEntry'));
		$permissions['canMarkNewsEntry'] = intval($permissions['canDeleteNewsEntry'] || $permissions['canMoveNewsEntry']);
		$permissions['canHandleNewsEntry'] = intval($permissions['canEnableNewsEntry'] || $permissions['canEditNewsEntry'] || $permissions['canMarkNewsEntry']);

		return $permissions;
	}

	/**
	 * Returns the global moderator permissions.
	 *
	 * @return	array
	 */
	public static function getGlobalModeratorPermissions() {
		$permissions = array();

		// news entry permissions
		$permissions['canDeleteNewsEntry'] = intval(WCF::getUser()->getPermission('mod.portal.canDeleteNewsEntry'));
		$permissions['canReadDeletedNewsEntry'] = intval(WCF::getUser()->getPermission('mod.portal.canReadDeletedNewsEntry'));
		$permissions['canDeleteNewsEntryCompletely'] = intval(WCF::getUser()->getPermission('mod.portal.canDeleteNewsEntryCompletely'));
		$permissions['canEnableNewsEntry'] = intval(WCF::getUser()->getPermission('mod.portal.canEnableNewsEntry'));
		$permissions['canEditNewsEntry'] = intval(WCF::getUser()->getPermission('mod.portal.canEditNewsEntry'));
		$permissions['canMoveNewsEntry'] = intval(WCF::getUser()->getPermission('mod.portal.canMoveNewsEntry'));
		$permissions['canMarkNewsEntry'] = intval($permissions['canDeleteNewsEntry'] || $permissions['canMoveNewsEntry']);
		$permissions['canHandleNewsEntry'] = intval($permissions['canEnableNewsEntry'] || $permissions['canEditNewsEntry'] || $permissions['canMarkNewsEntry']);

		return $permissions;
	}

	/**
	 * Returns the category with the given category id from cache.
	 *
	 * @param 	integer		$categoryID
	 * @return	Category
	 */
	public static function getCategory($categoryID) {
		if (self::$categories === null) {
			self::$categories = WCF::getCache()->get('category', 'categories');
		}

		if (!isset(self::$categories[$categoryID])) {
			throw new IllegalLinkException();
		}

		return self::$categories[$categoryID];
	}

	/**
	 * Creates the category select list.
	 *
	 * @param	string		$publicationType
	 * @param	array		$permissions
	 * @param	array		$ignoredCategories
	 * @return 	array
	 */
	public static function getCategorySelect($publicationType = '', $permissions = array('canViewCategory'), $ignoredCategories = array()) {
		self::$categorySelect = array();

		if (self::$categories === null) self::$categories = WCF::getCache()->get('category', 'categories');
		if (self::$categoryStructure === null) self::$categoryStructure = WCF::getCache()->get('category', 'categoryStructure');

		if (!empty($publicationType)) $publicationTypeObj = Publication::getPublicationTypeObject($publicationType);
		self::makeCategorySelect(0, 0, $publicationType, $permissions, $ignoredCategories);

		return self::$categorySelect;
	}

	/**
	 * Generates the category select list.
	 *
	 * @param	integer		$parentID
	 * @param	integer		$depth
	 * @param	string		$publicationType
	 * @param	array		$permissions
	 * @param	array		$ignoredCategories
	 */
	protected static function makeCategorySelect($parentID = 0, $depth = 0, $publicationType = '', $permissions = array('canViewCategory'), $ignoredCategories = array()) {
		if (!isset(self::$categoryStructure[$parentID])) return;

		foreach (self::$categoryStructure[$parentID] as $categoryID) {
			if (in_array($categoryID, $ignoredCategories)) continue;
			$category = self::$categories[$categoryID];
			if (!empty($publicationType) && !$category->isAvailablePublicationType($publicationType)) continue;

			$result = true;
			foreach ($permissions as $permission) {
				$result = $result && $category->getPermission($permission);
			}
			if (!$result) continue;

			$title = StringUtil::encodeHTML($category->getTitle());
			if ($depth > 0) $title = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $depth). ' '.$title;

			self::$categorySelect[$categoryID] = $title;
			self::makeCategorySelect($categoryID, $depth + 1, $publicationType, $permissions, $ignoredCategories);
		}
	}

	/**
	 * Returns a list of sub categories of the category with the given category id.
	 *
	 * @param	mixed		$categoryID
	 * @return	string
	 */
	public static function getSubCategories($categoryID) {
		return implode(',', self::getSubCategoryIDArray($categoryID));
	}

	/**
	 * Returns an array of sub categories of the category with the given category id.
	 *
	 * @param	mixed		$categoryID
	 * @return	array<integer>
	 */
	public static function getSubCategoryIDArray($categoryID) {
		$categoryIDArray = (is_array($categoryID) ? $categoryID : array($categoryID));
		$subCategoryIDArray = array();

		if (self::$categoryStructure === null) self::$categoryStructure = WCF::getCache()->get('category', 'categoryStructure');

		foreach ($categoryIDArray as $categoryID) {
			$subCategoryIDArray = array_merge($subCategoryIDArray, self::makeSubCategoryIDArray($categoryID, 0));
		}

		$subCategoryIDArray = array_unique($subCategoryIDArray);
		return $subCategoryIDArray;
	}

	/**
	 * Creates an array of sub categories of the category with the given category id.
	 *
	 * @param	integer		$parentCategoryID
	 * @param	integer		$depth
	 * @return	array<integer>
	 */
	public static function makeSubCategoryIDArray($parentCategoryID, $depth) {
		if (!isset(self::$categoryStructure[$parentCategoryID])) {
			return array();
		}

		$subCategoryIDArray = array();
		foreach (self::$categoryStructure[$parentCategoryID] as $categoryID) {
			$subCategoryIDArray = array_merge($subCategoryIDArray, self::makeSubCategoryIDArray($categoryID, $depth + 1));
			$subCategoryIDArray[] = $categoryID;
		}

		return $subCategoryIDArray;
	}

	/**
	 * Returns a list of accessible categories for the active user.
	 *
	 * @param	array		$permissions
	 * @return	string
	 */
	public static function getAccessibleCategories($permissions = array('canViewCategory', 'canEnterCategory')) {
		return implode(',', self::getAccessibleCategoryIDArray($permissions));
	}

	/**
	 * Returns a list of accessible categories.
	 *
	 * @param	array		$permissions
	 * @return	array<integer>
	 */
	public static function getAccessibleCategoryIDArray($permissions = array('canViewCategory', 'canEnterCategory')) {
		if (self::$categories === null) self::$categories = WCF::getCache()->get('category', 'categories');

		$categoryIDArray = array();
		foreach (self::$categories as $category) {
			$result = true;
			foreach ($permissions as $permission) {
				$result = $result && $category->getPermission($permission);
			}

			if ($result) {
				$categoryIDArray[] = $category->categoryID;
			}
		}

		return $categoryIDArray;
	}

	/**
	 * Returns a list of moderated categories for the active user.
	 *
	 * @param	array		$permissions
	 * @return	string
	 */
	public static function getModeratedCategories($permissions) {
		return implode(',', self::getModeratedCategoryIDArray($permissions));
	}

	/**
	 * Returns an array of moderated categories for the active user.
	 *
	 * @param	array		$permissions
	 * @return	array<integer>
	 */
	public static function getModeratedCategoryIDArray($permissions) {
		if (self::$categories === null) self::$categories = WCF::getCache()->get('category', 'categories');

		$categoryIDArray = array();
		foreach (self::$categories as $category) {
			$result = true;
			foreach ($permissions as $permission) {
				$result = $result && $category->getModeratorPermission($permission);
			}

			if ($result) {
				$categoryIDArray[] = $category->categoryID;
			}
		}

		return $categoryIDArray;
	}

	/**
	 * Inherits category permissions.
	 *
	 * @param 	integer 	$parentID
	 * @param 	array 		$permissions
	 */
	public static function inheritPermissions($parentID = 0, &$permissions) {
		if (self::$categories === null) self::$categories = WCF::getCache()->get('category', 'categories');
		if (self::$categoryStructure === null) self::$categoryStructure = WCF::getCache()->get('category', 'categoryStructure');

		if (isset(self::$categoryStructure[$parentID]) && is_array(self::$categoryStructure[$parentID])) {
			foreach (self::$categoryStructure[$parentID] as $categoryID) {
				$category = self::$categories[$categoryID];

				// inherit permissions from parent category
				if ($category->parentID) {
					if (isset($permissions[$category->parentID]) && !isset($permissions[$categoryID])) {
						$permissions[$categoryID] = $permissions[$category->parentID];
					}
				}

				self::inheritPermissions($categoryID, $permissions);
			}
		}
	}

	/**
	 * Searches for a category in the child tree of another category.
	 *
	 * @param	integer		$parentID
	 * @param	integer		$searchedCategoryID
	 */
	public static function searchChildren($parentID, $searchedCategoryID) {
		if (self::$categoryStructure === null) self::$categoryStructure = WCF::getCache()->get('category', 'categoryStructure');
		if (isset(self::$categoryStructure[$parentID])) {
			foreach (self::$categoryStructure[$parentID] as $categoryID) {
				if ($categoryID == $searchedCategoryID) return true;
				if (self::searchChildren($categoryID, $searchedCategoryID)) return true;
			}
		}
		return false;
	}

	/**
	 * Returns available permission settings.
	 *
	 * @return 	array
	 */
	public static function getPermissionSettings() {
		$sql = "SHOW COLUMNS FROM wsip".WSIP_N."_category_to_group";
		$result = WCF::getDB()->sendQuery($sql);
		$settings = array();
		while ($row = WCF::getDB()->fetchArray($result)) {
			if ($row['Field'] != 'categoryID' && $row['Field'] != 'groupID') {
				// check modules
				switch ($row['Field']) {
					// news
					case 'canReadNewsEntry':
					case 'canReadOwnNewsEntry':
					case 'canAddNewsEntry':
					case 'canEditOwnNewsEntry':
					case 'canDeleteOwnNewsEntry':
					case 'canAddNewsEntryWithoutModeration':
					case 'canSetNewsTags':
						if (!MODULE_NEWS) continue 2;
						break;
					case 'canDownloadNewsAttachment':
					case 'canViewNewsAttachmentPreview':
					case 'canUploadNewsAttachment':
						if (!MODULE_NEWS || !MODULE_ATTACHMENT) continue 2;
						break;
					case 'canStartNewsPoll':
					case 'canVoteNewsPoll':
						if (!MODULE_NEWS || !MODULE_POLL) continue 2;
						break;
					// article
					case 'canReadArticle':
					case 'canReadOwnArticle':
					case 'canAddArticle':
					case 'canEditOwnArticle':
					case 'canDeleteOwnArticle':
					case 'canSetArticleTags':
						if (!MODULE_ARTICLE) continue 2;
						break;
					case 'canDownloadArticleSectionAttachment':
					case 'canViewArticleSectionAttachmentPreview':
					case 'canUploadArticleSectionAttachment':
						if (!MODULE_ARTICLE || !MODULE_ATTACHMENT) continue 2;
						break;
				}

				$settings[] = $row['Field'];
			}
		}
		return $settings;
	}

	/**
	 * Returns available moderator settings.
	 *
	 * @return 	array
	 */
	public static function getModeratorSettings() {
		$sql = "SHOW COLUMNS FROM wsip".WSIP_N."_category_moderator";
		$result = WCF::getDB()->sendQuery($sql);
		$settings = array();
		while ($row = WCF::getDB()->fetchArray($result)) {
			if ($row['Field'] != 'categoryID' && $row['Field'] != 'userID' && $row['Field'] != 'groupID') {
				// check modules
				switch ($row['Field']) {
					case 'canEditNewsEntry':
					case 'canDeleteNewsEntry':
					case 'canReadDeletedNewsEntry':
					case 'canDeleteNewsEntryCompletely':
					case 'canEnableNewsEntry':
					case 'canMoveNewsEntry':
						if (!MODULE_NEWS) continue 2;
						break;
					case 'canEditArticle':
					case 'canDeleteArticle':
						if (!MODULE_ARTICLE) continue 2;
						break;
				}

				$settings[] = $row['Field'];
			}
		}
		return $settings;
	}

	/**
	 * Resets the category cache after changes.
	 */
	public static function resetCache() {
		// reset cache
		WCF::getCache()->clearResource('category');
		WCF::getCache()->clearResource('categoryData');

		// reset permissions cache
		WCF::getCache()->clear(WSIP_DIR.'cache/', 'cache.categoryPermissions-*', true);

		self::$categories = self::$categoryStructure = self::$categorySelect = null;
	}
}
?>