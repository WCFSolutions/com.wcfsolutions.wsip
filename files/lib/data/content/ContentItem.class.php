<?php
// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');

/**
 * Represents a content item.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.content
 * @category	Infinite Portal
 */
class ContentItem extends DatabaseObject {
	/**
	 * list of all content items
	 *
	 * @var	array<ContentItem>
	 */
	protected static $contentItems = null;

	/**
	 * content item structure
	 *
	 * @var	array
	 */
	protected static $contentItemStructure = null;

	/**
	 * content item options
	 *
	 * @var	array
	 */
	protected static $contentItemSelect;

	/**
	 * Defines that a content item acts as a page.
	 */
	const TYPE_PAGE = 0;

	/**
	 * Defines that a content item acts as an external link.
	 */
	const TYPE_LINK = 1;

	/**
	 * Defines that a content item acts as a container for boxes.
	 */
	const TYPE_BOX = 2;

	/**
	 * Creates a new ContentItem object.
	 *
	 * @param 	integer		$contentItemID
	 * @param 	array		$row
	 * @param 	ContentItem	$cacheObject
	 */
	public function __construct($contentItemID, $row = null, $cacheObject = null) {
		if ($contentItemID !== null) $cacheObject = self::getContentItem($contentItemID);
		if ($row != null) parent::__construct($row);
		if ($cacheObject != null) parent::__construct($cacheObject->data);
	}

	/**
	 * Enters this content item.
	 */
	public function enter() {
		// check permissions
		if (!$this->getPermission('canViewContentItem') || !$this->getPermission('canEnterContentItem') || (!$this->isPublished() && !$this->getPermission('canViewHiddenContentItem'))) {
			throw new PermissionDeniedException();
		}

		// change style if necessary
		require_once(WCF_DIR.'lib/system/style/StyleManager.class.php');
		if ($this->styleID && (!WCF::getSession()->getStyleID() || $this->enforceStyle) && StyleManager::getStyle()->styleID != $this->styleID) {
			StyleManager::changeStyle($this->styleID, true);
		}
	}

	/**
	 * Returns true, if the content item is published.
	 *
	 * @return	boolean
	 */
	public function isPublished() {
		if ($this->publishingStartTime && $this->publishingStartTime > TIME_NOW) {
			return false;
		}
		if ($this->publishingEndTime && $this->publishingEndTime <= TIME_NOW) {
			return false;
		}
		return true;
	}

	/**
	 * Returns the title of this item.
	 *
	 * @return	string
	 */
	public function getTitle() {
		return WCF::getLanguage()->get('wsip.contentItem.'.$this->contentItem);
	}

	/**
	 * Returns the formatted description of this item.
	 *
	 * @return	string
	 */
	public function getFormattedDescription() {
		return nl2br(StringUtil::encodeHTML(WCF::getLanguage()->get('wsip.contentItem.'.$this->contentItem.'.description')));
	}

	/**
	 * Returns the text of this item.
	 *
	 * @return	string
	 */
	public function getText() {
		return WCF::getLanguage()->get('wsip.contentItem.'.$this->contentItem.'.text');
	}

	/**
	 * Returns the meta description of this content item.
	 *
	 * @return	string
	 */
	public function getMetaDescription() {
		return WCF::getLanguage()->get('wsip.contentItem.'.$this->contentItem.'.metaDescription');
	}

	/**
	 * Returns the meta keywords of this content item.
	 *
	 * @return	string
	 */
	public function getMetaKeywords() {
		return WCF::getLanguage()->get('wsip.contentItem.'.$this->contentItem.'.metaKeywords');
	}

	/**
	 * Returns true, if this content item is a page.
	 *
	 * @return	boolean
	 */
	public function isPage() {
		if ($this->contentItemType == self::TYPE_PAGE) {
			return true;
		}
		return false;
	}

	/**
	 * Returns true, if this content item is an external link.
	 *
	 * @return	boolean
	 */
	public function isExternalLink() {
		if ($this->contentItemType == self::TYPE_LINK) {
			return true;
		}
		return false;
	}

	/**
	 * Returns true, if this content item is a box container.
	 *
	 * @return	boolean
	 */
	public function isBoxContainer() {
		if ($this->contentItemType == self::TYPE_BOX) {
			return true;
		}
		return false;
	}

	/**
	 * Returns the icon of this content item.
	 *
	 * @return	string
	 */
	public function getIcon() {
		if ($this->icon) {
			return $this->icon;
		}
		if ($this->isExternalLink()) {
			return 'contentItemRedirect';
		}
		else if ($this->isBoxContainer()) {
			return 'contentItemBox';
		}
		return 'contentItem';
	}

	/**
	 * Returns a list of the parent content items of this item.
	 *
	 * @return	array
	 */
	public function getParentContentItems() {
		$parentContentItems = array();
		$contentItems = WCF::getCache()->get('contentItem', 'contentItems');

		$parentContentItem = $this;
		while ($parentContentItem->parentID != 0) {
			$parentContentItem = $contentItems[$parentContentItem->parentID];
			array_unshift($parentContentItems, $parentContentItem);
		}

		return $parentContentItems;
	}


	/**
	 * Returns the boxes of this content item.
	 *
	 * @return	array<Box>
	 */
	public function getBoxes() {
		if (!$this->isBoxContainer()) return array();

		// read cache
		$boxes = WCF::getCache()->get('box-'.PACKAGE_ID);
		$boxToContentItems = WCF::getCache()->get('contentItem', 'boxes');
		if (!isset($boxToContentItems[$this->contentItemID])) return array();
		$boxIDArray = $boxToContentItems[$this->contentItemID];

		// cache boxes
		$cachedBoxes = array();
		foreach ($boxIDArray as $boxID => $showOrder) {
			if (!isset($boxes[$boxID])) continue;
			$box = $boxes[$boxID];

			// cache box tabs
			if (!$box->hasBoxTabs()) continue;
			foreach ($box->getBoxTabs() as $boxTab) {
				$boxTab->getBoxTabType()->cache($boxTab);
			}

			// save box
			$cachedBoxes[$boxID] = $box;
		}

		// get boxes
		$boxes = array();
		if (isset($boxToContentItems[$this->contentItemID])) {
			$boxIDArray = $boxToContentItems[$this->contentItemID];
			foreach ($boxIDArray as $boxID => $showOrder) {
				if (isset($cachedBoxes[$boxID])) {
					$boxes[$boxID] = $cachedBoxes[$boxID];
				}
			}
		}

		return $boxes;
	}

	/**
	 * Returns true, if the active user has the permission with the given name on this content item.
	 *
	 * @param	string		$permission
	 * @return	boolean
	 */
	public function getPermission($permission = 'canViewContentItem') {
		return (boolean) WCF::getUser()->getContentItemPermission($permission, $this->contentItemID);
	}

	/**
	 * Checks the requested permissions of the active user with the given names on this content item.
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
	 * Returns a list of direct sub content items of this content item.
	 *
	 * @return	array<ContentItem>
	 */
	public function getSubContentItems() {
		if (self::$contentItemStructure === null) self::$contentItemStructure = WCF::getCache()->get('contentItem', 'contentItemStructure');
		$subContentItems = array();

		if (isset(self::$contentItemStructure[$this->contentItemID])) {
			foreach (self::$contentItemStructure[$this->contentItemID] as $contentItemID) {
				$contentItem = self::getContentItem($contentItemID);

				if ($contentItem->getPermission() && ($contentItem->isPublished() || $contentItem->getPermission('canViewHiddenContentItem')))  {
					$subContentItems[$contentItemID] = $contentItem;
				}
			}
		}

		return $subContentItems;
	}

	/**
	 * Returns a list of all content items.
	 *
	 * @return 	array<ContentItem>
	 */
	public static function getContentItems() {
		if (self::$contentItems === null) {
			self::$contentItems = WCF::getCache()->get('contentItem', 'contentItems');
		}

		return self::$contentItems;
	}

	/**
	 * Returns the content item with the given content item id from cache.
	 *
	 * @param 	integer		$contentItemID
	 * @return	ContentItem
	 */
	public static function getContentItem($contentItemID) {
		if (self::$contentItems === null) {
			self::$contentItems = WCF::getCache()->get('contentItem', 'contentItems');
		}

		if (!isset(self::$contentItems[$contentItemID])) {
			throw new IllegalLinkException();
		}

		return self::$contentItems[$contentItemID];
	}

	/**
	 * Creates the content item select list.
	 *
	 * @param	array		$permissions
	 * @param	array		$ignoredContentItems
	 * @return 	array
	 */
	public static function getContentItemSelect($permissions = array('canViewContentItem'), $ignoredTypes = array(), $ignoredContentItems = array()) {
		self::$contentItemSelect = array();

		if (self::$contentItems === null) self::$contentItems = WCF::getCache()->get('contentItem', 'contentItems');
		if (self::$contentItemStructure === null) self::$contentItemStructure = WCF::getCache()->get('contentItem', 'contentItemStructure');

		self::makeContentItemSelect(0, 0, $permissions, $ignoredTypes, $ignoredContentItems);

		return self::$contentItemSelect;
	}

	/**
	 * Generates the content item select list.
	 *
	 * @param	integer		$parentID
	 * @param	integer		$depth
	 * @param	array		$permissions
	 * @param	array		$ignoredContentItems
	 */
	protected static function makeContentItemSelect($parentID = 0, $depth = 0, $permissions = array('canViewContentItem'), $ignoredTypes = array(), $ignoredContentItems = array()) {
		if (!isset(self::$contentItemStructure[$parentID])) return;

		foreach (self::$contentItemStructure[$parentID] as $contentItemID) {
			if (in_array($contentItemID, $ignoredContentItems)) continue;
			$contentItem = self::$contentItems[$contentItemID];
			if (in_array($contentItem->contentItemType, $ignoredTypes)) continue;

			$result = true;
			foreach ($permissions as $permission) {
				$result = $result && $contentItem->getPermission($permission);
			}
			if (!$result) continue;

			$title = StringUtil::encodeHTML($contentItem->getTitle());
			if ($depth > 0) $title = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $depth). ' '.$title;

			self::$contentItemSelect[$contentItemID] = $title;
			self::makeContentItemSelect($contentItemID, $depth + 1, $permissions, $ignoredTypes, $ignoredContentItems);
		}
	}

	/**
	 * Searches for a content item in the child tree of another content item.
	 *
	 * @param	integer		$parentID
	 * @param	integer		$searchedContentItemID
	 */
	public static function searchChildren($parentID, $searchedContentItemID) {
		if (self::$contentItemStructure === null) self::$contentItemStructure = WCF::getCache()->get('contentItem', 'contentItemStructure');
		if (isset(self::$contentItemStructure[$parentID])) {
			foreach (self::$contentItemStructure[$parentID] as $contentItemID) {
				if ($contentItemID == $searchedContentItemID) return true;
				if (self::searchChildren($contentItemID, $searchedContentItemID)) return true;
			}
		}
		return false;
	}

	/**
	 * Inherits content item permissions.
	 *
	 * @param 	integer 	$parentID
	 * @param 	array 		$permissions
	 */
	public static function inheritPermissions($parentID = 0, &$permissions) {
		self::$contentItems = WCF::getCache()->get('contentItem', 'contentItems');
		if (self::$contentItemStructure === null) self::$contentItemStructure = WCF::getCache()->get('contentItem', 'contentItemStructure');

		if (isset(self::$contentItemStructure[$parentID]) && is_array(self::$contentItemStructure[$parentID])) {
			foreach (self::$contentItemStructure[$parentID] as $contentItemID) {
				$contentItem = self::$contentItems[$contentItemID];

				// inherit permissions from parent content item
				if ($contentItem->parentID) {
					if (isset($permissions[$contentItem->parentID]) && !isset($permissions[$contentItemID])) {
						$permissions[$contentItemID] = $permissions[$contentItem->parentID];
					}
				}

				self::inheritPermissions($contentItemID, $permissions);
			}
		}
	}

	/**
	 * Searches in content items.
	 *
	 * @param	string		$query
	 * @return	array
	 */
	public static function search($query) {
		$result = array();

		$contentItems = self::getContentItems();
		$boxToContentItems = WCF::getCache()->get('contentItem', 'boxes');
		foreach ($contentItems as $contentItemID => $contentItem) {
			if (!$contentItem->getPermission('canViewContentItem', 'canEnterContentItem') || !$contentItem->isPublished()) continue;

			// title
			$contents = WCF::getLanguage()->get('wsip.contentItem.'.$contentItem->contentItem);

			// description
			if (WCF::getLanguage()->get('wsip.contentItem.'.$contentItem->contentItem.'.description')) {
				$contents .= "\n\n".WCF::getLanguage()->get('wsip.contentItem.'.$contentItem->contentItem.'.description');
			}

			// text
			if ($contentItem->isPage()) {
				$contents .= "\n\n".WCF::getLanguage()->get('wsip.contentItem.'.$contentItem->contentItem.'.text');
			}
			// box tab content
			else {
				if (isset($boxToContentItems[$contentItemID])) {
					$boxIDArray = $boxToContentItems[$contentItemID];
					foreach ($boxIDArray as $boxID => $showOrder) {
						try {
							$box = new Box($boxID);
							foreach ($box->getBoxTabs() as $boxTab) {
								if ($boxTab->boxTabType == 'content') {
									$contents .= "\n\n".WCF::getLanguage()->get($boxTab->text);
								}
							}
						}
						catch (IllegalLinkException $e) {}
					}
				}
			}

			// search
			if (preg_match('!'.preg_quote($query).'!i', $contents)) {
				$result[] = $contentItemID;
			}
		}

		return $result;
	}

	/**
	 * Returns available permission settings.
	 *
	 * @return 	array
	 */
	public static function getPermissionSettings() {
		$sql = "SHOW COLUMNS FROM wsip".WSIP_N."_content_item_to_group";
		$result = WCF::getDB()->sendQuery($sql);
		$settings = array();
		while ($row = WCF::getDB()->fetchArray($result)) {
			if ($row['Field'] != 'contentItemID' && $row['Field'] != 'groupID') {
				$settings[] = $row['Field'];
			}
		}
		return $settings;
	}

	/**
	 * Resets the content item chache.
	 */
	public static function resetCache() {
		// reset cache
		WCF::getCache()->clearResource('contentItem');

		// reset permissions cache
		WCF::getCache()->clear(WSIP_DIR.'cache/', 'cache.contentItemPermissions-*', true);

		self::$contentItems = self::$contentItemStructure = self::$contentItemSelect = null;
	}
}
?>