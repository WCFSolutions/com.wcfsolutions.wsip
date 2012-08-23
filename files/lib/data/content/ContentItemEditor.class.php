<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/content/ContentItem.class.php');

// wcf imports
require_once(WCF_DIR.'lib/system/language/LanguageEditor.class.php');

/**
 * Provides functions to manage content items.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.content
 * @category	Infinite Portal
 */
class ContentItemEditor extends ContentItem {
	/**
	 * Creates a new ContentItemEditor object.
	 */
	public function __construct($contentItemID, $row = null, $cacheObject = null, $useCache = true) {
		if ($useCache) parent::__construct($contentItemID, $row, $cacheObject);
		else {
			$sql = "SELECT	*
				FROM	wsip".WSIP_N."_content_item
				WHERE 	contentItemID = ".$contentItemID;
			$row = WCF::getDB()->getFirstRow($sql);
			parent::__construct(null, $row);
		}
	}

	/**
	 * Updates this content item.
	 *
	 * @param	integer		$parentID
	 * @param	string		$title
	 * @param	string		$description
	 * @param	string		$text
	 * @param	integer		$contentItemType
	 * @param	string		$externalURL
	 * @param	string		$icon
	 * @param	string		$metaDescription
	 * @param	string		$metaKeywords
	 * @param	integer		$publishingStartTime
	 * @param	integer		$publishingEndTime
	 * @param	integer		$styleID
	 * @param	integer		$enforceStyle
	 * @param	integer		$boxLayoutID
	 * @param	integer		$allowSpidersToIndexThisPage
	 * @param	integer		$showOrder
	 * @param	integer		$languageID
	 */
	public function update($parentID, $title, $description = '', $text = '', $contentItemType = 0, $externalURL = '', $icon = '', $metaDescription = '', $metaKeywords = '', $publishingStartTime = 0, $publishingEndTime = 0, $styleID = 0, $enforceStyle = 0, $boxLayoutID = 0, $allowSpidersToIndexThisPage = 1, $showOrder = 0, $languageID = 0) {
		// update show order
		if ($this->showOrder != $showOrder) {
			if ($showOrder < $this->showOrder) {
				$sql = "UPDATE	wsip".WSIP_N."_content_item
					SET 	showOrder = showOrder + 1
					WHERE 	showOrder >= ".$showOrder."
						AND showOrder < ".$this->showOrder."
						AND parentID = ".$parentID;
				WCF::getDB()->sendQuery($sql);
			}
			else if ($showOrder > $this->showOrder) {
				$sql = "UPDATE	wsip".WSIP_N."_content_item
					SET	showOrder = showOrder - 1
					WHERE	showOrder <= ".$showOrder."
						AND showOrder > ".$this->showOrder."
						AND parentID = ".$parentID;
				WCF::getDB()->sendQuery($sql);
			}
		}

		// update item
		$sql = "UPDATE	wsip".WSIP_N."_content_item
			SET	parentID = ".$parentID.",
				".($languageID == 0 ? "contentItem = '".escapeString($title)."'," : '')."
				contentItemType = ".$contentItemType.",
				externalURL = '".escapeString($externalURL)."',
				icon = '".escapeString($icon)."',
				publishingStartTime = ".$publishingStartTime.",
				publishingEndTime = ".$publishingEndTime.",
				styleID = ".$styleID.",
				enforceStyle = ".$enforceStyle.",
				boxLayoutID = ".$boxLayoutID.",
				allowSpidersToIndexThisPage = ".$allowSpidersToIndexThisPage.",
				showOrder = ".$showOrder."
			WHERE	contentItemID = ".$this->contentItemID;
		WCF::getDB()->sendQuery($sql);

		// update language items
		if ($languageID != 0) {
			// save language variables
			$language = new LanguageEditor($languageID);
			$language->updateItems(array('wsip.contentItem.'.$this->contentItem => $title, 'wsip.contentItem.'.$this->contentItem.'.description' => $description, 'wsip.contentItem.'.$this->contentItem.'.text' => $text, 'wsip.contentItem.'.$this->contentItem.'.metaDescription' => $metaDescription, 'wsip.contentItem.'.$this->contentItem.'.metaKeywords' => $metaKeywords), 0, PACKAGE_ID, array('wsip.contentItem.'.$this->contentItem => 1, 'wsip.contentItem.'.$this->contentItem.'.description' => 1, 'wsip.contentItem.'.$this->contentItem.'.text' => 1, 'wsip.contentItem.'.$this->contentItem.'.metaDescription' => 1, 'wsip.contentItem.'.$this->contentItem.'.metaKeywords' => 1));
			LanguageEditor::deleteLanguageFiles($languageID, 'wsip.contentItem', PACKAGE_ID);
		}
	}

	/**
	 * Returns the cleaned permission list.
	 * Removes default permissions from the given permission list.
	 *
	 * @param	array		$permissions
	 * @return	array
	 */
	public static function getCleanedPermissions($permissions) {
		$noDefaultValue = false;
		foreach ($permissions as $key => $permission) {
			foreach ($permission['settings'] as $value) {
				if ($value != -1) $noDefaultValue = true;
			}
			if (!$noDefaultValue) {
				unset($permissions[$key]);
				continue;
			}
		}
		return $permissions;
	}

	/**
	 * Removes the user and group permissions of this content item.
	 */
	public function removePermissions() {
		// user
		$sql = "DELETE FROM	wsip".WSIP_N."_content_item_to_user
			WHERE		contentItemID = ".$this->contentItemID;
		WCF::getDB()->sendQuery($sql);

		// group
		$sql = "DELETE FROM	wsip".WSIP_N."_content_item_to_group
			WHERE		contentItemID = ".$this->contentItemID;
		WCF::getDB()->sendQuery($sql);
	}

	/**
	 * Adds the given permissions to this content item.
	 *
	 * @param	array		$permissions
	 * @param	array		$permissionSettings
	 */
	public function addPermissions($permissions, $permissionSettings) {
		$userInserts = $groupInserts = '';
		foreach ($permissions as $key => $permission) {
			if ($permission['type'] == 'user') {
				if (!empty($userInserts)) $userInserts .= ',';
				$userInserts .= '('.$this->contentItemID.',
						 '.intval($permission['id']).',
						 '.(implode(', ', ArrayUtil::toIntegerArray($permission['settings']))).')';

			}
			else {
				if (!empty($groupInserts)) $groupInserts .= ',';
				$groupInserts .= '('.$this->contentItemID.',
						 '.intval($permission['id']).',
						 '.(implode(', ', ArrayUtil::toIntegerArray($permission['settings']))).')';
			}
		}

		if (!empty($userInserts)) {
			$sql = "INSERT INTO	wsip".WSIP_N."_content_item_to_user
						(contentItemID, userID, ".implode(', ', $permissionSettings).")
				VALUES		".$userInserts;
			WCF::getDB()->sendQuery($sql);
		}

		if (!empty($groupInserts)) {
			$sql = "INSERT INTO	wsip".WSIP_N."_content_item_to_group
						(contentItemID, groupID, ".implode(', ', $permissionSettings).")
				VALUES		".$groupInserts;
			WCF::getDB()->sendQuery($sql);
		}

		return $permissions;
	}

	/**
	 * Deletes this content item.
	 */
	public function delete() {
		// update show order
		$sql = "UPDATE	wsip".WSIP_N."_content_item
			SET	showOrder = showOrder - 1
			WHERE	showOrder >= ".$this->showOrder."
				AND parentID = ".$this->parentID;
		WCF::getDB()->sendQuery($sql);

		// update neighbour content items
		$sql = "UPDATE	wsip".WSIP_N."_content_item
			SET	showOrder = showOrder - 1
			WHERE	showOrder > ".$this->showOrder."
				AND parentID = ".$this->parentID;
		WCF::getDB()->sendQuery($sql);

		// update sub content items
		$sql = "UPDATE	wsip".WSIP_N."_content_item
			SET	parentID = ".$this->parentID."
			WHERE	parentID = ".$this->contentItemID;
		WCF::getDB()->sendQuery($sql);

		// delete category group options
		$sql = "DELETE FROM	wsip".WSIP_N."_content_item_to_group
			WHERE		contentItemID = ".$this->contentItemID;
		WCF::getDB()->sendQuery($sql);

		// delete category user options
		$sql = "DELETE FROM	wsip".WSIP_N."_content_item_to_user
			WHERE		contentItemID = ".$this->contentItemID;
		WCF::getDB()->sendQuery($sql);

		// delete box assignments
		$sql = "DELETE FROM	wsip".WSIP_N."_content_item_box
			WHERE		contentItemID = ".$this->contentItemID;
		WCF::getDB()->sendQuery($sql);

		// delete content item
		$sql = "DELETE FROM	wsip".WSIP_N."_content_item
			WHERE		contentItemID = ".$this->contentItemID;
		WCF::getDB()->sendQuery($sql);

		// delete language variables
		LanguageEditor::deleteVariable('wsip.contentItem.'.$this->contentItem);
		LanguageEditor::deleteVariable('wsip.contentItem.'.$this->contentItem.'.description');
		LanguageEditor::deleteVariable('wsip.contentItem.'.$this->contentItem.'.text');
		LanguageEditor::deleteVariable('wsip.contentItem.'.$this->contentItem.'.metaDescription');
		LanguageEditor::deleteVariable('wsip.contentItem.'.$this->contentItem.'.metaKeywords');
	}

	/**
	 * Adds the box with the given box id to this content item.
	 *
	 * @param	integer		$boxID
	 */
	public function addBox($boxID) {
		// get next number in row
		$sql = "SELECT	MAX(showOrder) AS showOrder
			FROM	wsip".WSIP_N."_content_item_box
			WHERE	contentItemID = ".$this->contentItemID;
		$row = WCF::getDB()->getFirstRow($sql);
		if (!empty($row)) $showOrder = intval($row['showOrder']) + 1;
		else $showOrder = 1;

		// add box
		$sql = "REPLACE INTO	wsip".WSIP_N."_content_item_box
					(boxID, contentItemID, showOrder)
			VALUES		(".$boxID.", ".$this->contentItemID.", ".$showOrder.")";
		WCF::getDB()->sendQuery($sql);
	}

	/**
	 * Updates the position of a box directly.
	 *
	 * @param	integer		$boxID
	 * @param	integer		$showOrder
	 */
	public function updateBoxShowOrder($boxID, $showOrder) {
		$sql = "UPDATE	wsip".WSIP_N."_content_item_box
			SET	showOrder = ".$showOrder."
			WHERE 	contentItemID = ".$this->contentItemID."
				AND boxID = ".$boxID;
		WCF::getDB()->sendQuery($sql);
	}

	/**
	 * Removes the box with the given box id from this content item.
	 *
	 * @param	integer		$boxID
	 */
	public function removeBox($boxID) {
		$sql = "DELETE FROM	wsip".WSIP_N."_content_item_box
			WHERE		contentItemID = ".$this->contentItemID."
					AND boxID = ".$boxID;
		WCF::getDB()->sendQuery($sql);
	}

	/**
	 * Creates a new content item.
	 *
	 * @param	integer		$parentID
	 * @param	string		$title
	 * @param	string		$description
	 * @param	string		$text
	 * @param	integer		$contentItemType
	 * @param	string		$externalURL
	 * @param	string		$icon
	 * @param	string		$metaDescription
	 * @param	string		$metaKeywords
	 * @param	integer		$publishingStartTime
	 * @param	integer		$publishingEndTime
	 * @param	integer		$styleID
	 * @param	integer		$enforceStyle
	 * @param	integer		$boxLayoutID
	 * @param	integer		$allowSpidersToIndexThisPage
	 * @param	integer		$showOrder
	 * @param	integer		$languageID
	 * @return	ContentItemEditor
	 */
	public static function create($parentID, $title, $description = '', $text = '', $contentItemType = 0, $externalURL = '', $icon = '', $metaDescription = '', $metaKeywords = '', $publishingStartTime = 0, $publishingEndTime = 0, $styleID = 0, $enforceStyle = 0, $boxLayoutID = 0, $allowSpidersToIndexThisPage = 1, $showOrder = 0, $languageID = 0) {
		// get show order
		if ($showOrder == 0) {
			// get next number in row
			$sql = "SELECT	MAX(showOrder) AS showOrder
				FROM	wsip".WSIP_N."_content_item
				WHERE	parentID = ".$parentID;
			$row = WCF::getDB()->getFirstRow($sql);
			if (!empty($row)) $showOrder = intval($row['showOrder']) + 1;
			else $showOrder = 1;
		}
		else {
			$sql = "UPDATE	wsip".WSIP_N."_content_item
				SET 	showOrder = showOrder + 1
				WHERE 	showOrder >= ".$showOrder."
					AND parentID = ".$parentID;
			WCF::getDB()->sendQuery($sql);
		}

		// get title
		$contentItem = '';
		if ($languageID == 0) $contentItem = $title;

		// insert item
		$sql = "INSERT INTO	wsip".WSIP_N."_content_item
					(parentID, contentItem, contentItemType, externalURL, icon, publishingStartTime, publishingEndTime, styleID, enforceStyle, boxLayoutID, allowSpidersToIndexThisPage, showOrder)
			VALUES		(".$parentID.", '".escapeString($contentItem)."', ".$contentItemType.", '".escapeString($externalURL)."', '".escapeString($icon)."', ".$publishingStartTime.", ".$publishingEndTime.", ".$styleID.", ".$enforceStyle.", ".$boxLayoutID.", ".$allowSpidersToIndexThisPage.", ".$showOrder.")";
		WCF::getDB()->sendQuery($sql);

		// get item id
		$contentItemID = WCF::getDB()->getInsertID("wsip".WSIP_N."_content_item", 'contentItemID');

		// update language items
		if ($languageID != 0) {
			// set name
			$contentItem = "contentItem".$contentItemID;
			$sql = "UPDATE	wsip".WSIP_N."_content_item
				SET	contentItem = '".escapeString($contentItem)."'
				WHERE	contentItemID = ".$contentItemID;
			WCF::getDB()->sendQuery($sql);

			// save language variables
			$language = new LanguageEditor($languageID);
			$language->updateItems(array('wsip.contentItem.'.$contentItem => $title, 'wsip.contentItem.'.$contentItem.'.description' => $description, 'wsip.contentItem.'.$contentItem.'.text' => $text, 'wsip.contentItem.'.$contentItem.'.metaDescription' => $metaDescription, 'wsip.contentItem.'.$contentItem.'.metaKeywords' => $metaKeywords));
			LanguageEditor::deleteLanguageFiles($languageID, 'wsip.contentItem', PACKAGE_ID);
		}

		// return new content item
		return new ContentItemEditor($contentItemID, null, null, false);
	}

	/**
	 * Updates the position of the content item with the given content item id.
	 *
	 * @param	integer		$contentItemID
	 * @param	integer		$parentID
	 * @param	integer		$position
	 */
	public static function updatePosition($contentItemID, $parentID, $position) {
		$sql = "UPDATE	wsip".WSIP_N."_content_item
			SET	parentID = ".$parentID.",
				showOrder = ".$position."
			WHERE 	contentItemID = ".$contentItemID;
		WCF::getDB()->sendQuery($sql);
	}
}
?>