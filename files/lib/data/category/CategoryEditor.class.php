<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/Category.class.php');

// wcf imports
require_once(WCF_DIR.'lib/system/language/LanguageEditor.class.php');

/**
 * Provides functions to manage categories.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.category
 * @category	Infinite Portal
 */
class CategoryEditor extends Category {
	/**
	 * Creates a new CategoryEditor object.
	 */
	public function __construct($categoryID, $row = null, $cacheObject = null, $useCache = true) {
		if ($useCache) parent::__construct($categoryID, $row, $cacheObject);
		else {
			$sql = "SELECT	*
				FROM	wsip".WSIP_N."_category
				WHERE	categoryID = ".$categoryID;
			$row = WCF::getDB()->getFirstRow($sql);
			parent::__construct(null, $row);
		}
	}
	
	/**
	 * Updates the amount of news entries for this category.
	 * 
	 * @param	integer		$entries
	 */
	public function updateNewsEntries($entries) {
		$sql = "UPDATE	wsip".WSIP_N."_category
			SET	newsEntries = newsEntries + ".$entries."
			WHERE 	categoryID = ".$this->categoryID;
		WCF::getDB()->registerShutdownUpdate($sql);
	}
	
	/**
	 * Updates the amount of articles for this category.
	 * 
	 * @param	integer		$articles
	 */
	public function updateArticles($articles) {
		$sql = "UPDATE	wsip".WSIP_N."_category
			SET	articles = articles + ".$articles."
			WHERE 	categoryID = ".$this->categoryID;
		WCF::getDB()->registerShutdownUpdate($sql);
	}
	
	/**
	 * Updates the amount of links for this category.
	 * 
	 * @param	integer		$links
	 */
	public function updateLinks($links) {
		$sql = "UPDATE	wsip".WSIP_N."_category
			SET	links = links + ".$links."
			WHERE 	categoryID = ".$this->categoryID;
		WCF::getDB()->registerShutdownUpdate($sql);
	}
	
	/**
	 * Updates the stats for this category.
	 */
	public function refresh() {
		$this->refreshAll($this->categoryID);
	}
	
	/**
	 * Assigns this category to the given publication types.
	 * 
	 * @param	array		$publicationTypes
	 */
	public function assignPublicationTypes($publicationTypes) {
		$publicationTypes = array_unique($publicationTypes);
	
		$inserts = '';
		foreach ($publicationTypes as $publicationType) {
			if (!empty($inserts)) $inserts .= ',';
			$inserts .= "('".escapeString($publicationType)."', ".$this->categoryID.")";
		}
	
		// insert new publication types
		$sql = "INSERT IGNORE INTO 	wsip".WSIP_N."_category_to_publication_type
						(publicationType, categoryID)
			VALUES			".$inserts;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Removes assigned publication types.
	 */
	public function removeAssignedPublicationTypes() {
		$sql = "DELETE FROM 	wsip".WSIP_N."_category_to_publication_type
			WHERE		categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Returns the list of assigned publication types.
	 * 
	 * @return	array
	 */
	public function getAssignedPublicationTypes() {
		$publicationTypes = array();
		$sql = "SELECT	publicationType
			FROM	wsip".WSIP_N."_category_to_publication_type
			WHERE	categoryID = ".$this->categoryID;
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$publicationTypes[] = $row['publicationType'];
		}
		return $publicationTypes;
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
	 * Removes the user and group permissions of this category.
	 */
	public function removePermissions() {
		// user
		$sql = "DELETE FROM	wsip".WSIP_N."_category_to_user
			WHERE		categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
		
		// group
		$sql = "DELETE FROM	wsip".WSIP_N."_category_to_group
			WHERE		categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Adds the given permissions to this category.
	 * 
	 * @param	array		$permissions
	 * @param	array		$permissionSettings
	 */
	public function addPermissions($permissions, $permissionSettings) {
		$userInserts = $groupInserts = '';
		foreach ($permissions as $key => $permission) {
			if ($permission['type'] == 'user') {
				if (!empty($userInserts)) $userInserts .= ',';
				$userInserts .= '('.$this->categoryID.',
						 '.intval($permission['id']).',
						 '.(implode(', ', ArrayUtil::toIntegerArray($permission['settings']))).')';
			
			}
			else {
				if (!empty($groupInserts)) $groupInserts .= ',';
				$groupInserts .= '('.$this->categoryID.',
						 '.intval($permission['id']).',
						 '.(implode(', ', ArrayUtil::toIntegerArray($permission['settings']))).')';
			}
		}
	
		if (!empty($userInserts)) {
			$sql = "INSERT INTO	wsip".WSIP_N."_category_to_user
						(categoryID, userID, ".implode(', ', $permissionSettings).")
				VALUES		".$userInserts;
			WCF::getDB()->sendQuery($sql);
		}
		
		if (!empty($groupInserts)) {
			$sql = "INSERT INTO	wsip".WSIP_N."_category_to_group
						(categoryID, groupID, ".implode(', ', $permissionSettings).")
				VALUES		".$groupInserts;
			WCF::getDB()->sendQuery($sql);
		}
		
		return $permissions;
	}
	
	/**
	 * Removes the moderator permissions of this category.
	 */
	public function removeModerators() {
		$sql = "DELETE FROM	wsip".WSIP_N."_category_moderator
			WHERE		categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Adds the given moderators to this category.
	 * 
	 * @param	array		$moderators
	 * @param	array		$moderatorSettings
	 */
	public function addModerators($moderators, $moderatorSettings) {
		$inserts = '';
		foreach ($moderators as $moderator) {
			if (!empty($inserts)) $inserts .= ',';
			$inserts .= '	('.$this->categoryID.',
					'.($moderator['type'] == 'user' ? intval($moderator['id']) : 0).',
					'.($moderator['type'] == 'group' ? intval($moderator['id']) : 0).',
					'.(implode(', ', ArrayUtil::toIntegerArray($moderator['settings']))).')';
		}
	
		if (!empty($inserts)) {
			$sql = "INSERT INTO	wsip".WSIP_N."_category_moderator
						(categoryID, userID, groupID, ".implode(', ', $moderatorSettings).")
				VALUES		".$inserts;
			WCF::getDB()->sendQuery($sql);
		}
	}
	
	/**
	 * Updates this category.
	 * 
	 * @param	integer		$parentID
	 * @param	string		$title
	 * @param	string		$description
	 * @param	integer		$allowDescriptionHtml
	 * @param	integer		$showOrder
	 * @param	integer		$languageID
	 */
	public function update($parentID, $title, $description = '', $allowDescriptionHtml = 0, $showOrder = 0, $languageID = 0) {
		// update show order
		if ($this->showOrder != $showOrder) {
			if ($showOrder < $this->showOrder) {
				$sql = "UPDATE	wsip".WSIP_N."_category
					SET 	showOrder = showOrder + 1
					WHERE 	showOrder >= ".$showOrder."
						AND showOrder < ".$this->showOrder."
						AND parentID = ".$parentID;
				WCF::getDB()->sendQuery($sql);
			}
			else if ($showOrder > $this->showOrder) {
				$sql = "UPDATE	wsip".WSIP_N."_category
					SET	showOrder = showOrder - 1
					WHERE	showOrder <= ".$showOrder."
						AND showOrder > ".$this->showOrder."
						AND parentID = ".$parentID;
				WCF::getDB()->sendQuery($sql);
			}
		}
		
		// update category
		$sql = "UPDATE	wsip".WSIP_N."_category
			SET	parentID = ".$parentID.",
				".($languageID == 0 ? "category = '".escapeString($title)."'," : '')."
				allowDescriptionHtml = '".$allowDescriptionHtml."',
				showOrder = ".$showOrder."
			WHERE	categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
		
		// update language items
		if ($languageID != 0) {
			// save language variables
			$language = new LanguageEditor($languageID);
			$language->updateItems(array('wsip.category.'.$this->category => $title, 'wsip.category.'.$this->category.'.description' => $description), 0, PACKAGE_ID, array('wsip.category.'.$this->category => 1, 'wsip.category.'.$this->category.'.description' => 1));
			LanguageEditor::deleteLanguageFiles($languageID, 'wsip.category', PACKAGE_ID);
		}
	}
	
	/**
	 * Deletes this category.
	 */
	public function delete() {
		// get all news entry ids
		$entryIDs = '';
		$sql = "SELECT	entryID
			FROM	wsip".WSIP_N."_news_entry
			WHERE	categoryID = ".$this->categoryID;
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($entryIDs)) $entryIDs .= ',';
			$entryIDs .= $row['entryID'];
		}
		if (!empty($entryIDs)) {
			// delete news entries
			require_once(WSIP_DIR.'lib/data/news/NewsEntryEditor.class.php');
			NewsEntryEditor::deleteAllCompletely($entryIDs);
		}
		
		// get all article ids
		$articleIDs = '';
		$sql = "SELECT	articleID
			FROM	wsip".WSIP_N."_article
			WHERE	categoryID = ".$this->categoryID;
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($articleIDs)) $articleIDs .= ',';
			$articleIDs .= $row['articleID'];
		}
		if (!empty($articleIDs)) {
			// delete articles
			require_once(WSIP_DIR.'lib/data/article/ArticleEditor.class.php');
			ArticleEditor::deleteAll($articleIDs);
		}
		
		// remove publication types
		$this->removeAssignedPublicationTypes();
		
		// update sub categories
		$sql = "UPDATE	wsip".WSIP_N."_category
			SET	parentID = ".$this->parentID."
			WHERE	parentID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
		
		// delete category moderator options
		$sql = "DELETE FROM	wsip".WSIP_N."_category_moderator
			WHERE		categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
		
		// delete category group options
		$sql = "DELETE FROM	wsip".WSIP_N."_category_to_group
			WHERE		categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
		
		// delete category user options
		$sql = "DELETE FROM	wsip".WSIP_N."_category_to_user
			WHERE		categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
		
		// delete category
		$sql = "DELETE FROM	wsip".WSIP_N."_category
			WHERE		categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
			
		// delete language variables
		LanguageEditor::deleteVariable('wsip.category.'.$this->category);
		LanguageEditor::deleteVariable('wsip.category.'.$this->category.'.description');
	}
	
	/**
	 * Creates a new category.
	 * 
	 * @param	integer		$parentID
	 * @param	string		$title
	 * @param	string		$description
	 * @param	integer		$allowDescriptionHtml
	 * @param	integer		$showOrder
	 * @param	integer		$languageID
	 */
	public static function create($parentID, $title, $description = '', $allowDescriptionHtml = 0, $showOrder = 0, $languageID = 0) {
		// get show order
		if ($showOrder == 0) {
			// get next number in row
			$sql = "SELECT	MAX(showOrder) AS showOrder
				FROM	wsip".WSIP_N."_category
				WHERE	parentID = ".$parentID;
			$row = WCF::getDB()->getFirstRow($sql);
			if (!empty($row)) $showOrder = intval($row['showOrder']) + 1;
			else $showOrder = 1;
		}
		else {
			$sql = "UPDATE	wsip".WSIP_N."_category
				SET 	showOrder = showOrder + 1
				WHERE 	showOrder >= ".$showOrder."
					AND parentID = ".$parentID;
			WCF::getDB()->sendQuery($sql);
		}
		
		// get title
		$category = '';
		if ($languageID == 0) $category = $title;

		// save category
		$sql = "INSERT INTO	wsip".WSIP_N."_category
					(parentID, category, allowDescriptionHtml, time, showOrder)
			VALUES		(".$parentID.", '".escapeString($category)."', ".$allowDescriptionHtml.", ".TIME_NOW.", ".$showOrder.")";
		WCF::getDB()->sendQuery($sql);
		
		// get category id
		$categoryID = WCF::getDB()->getInsertID("wsip".WSIP_N."_category", 'categoryID');
		
		// update language items
		if ($languageID != 0) {
			// set name
			$category = "category".$categoryID;
			$sql = "UPDATE	wsip".WSIP_N."_category
				SET	category = '".escapeString($category)."'
				WHERE	categoryID = ".$categoryID;
			WCF::getDB()->sendQuery($sql);
			
			// save language variables
			$language = new LanguageEditor($languageID);
			$language->updateItems(array('wsip.category.'.$category => $title, 'wsip.category.'.$category.'.description' => $description));
			LanguageEditor::deleteLanguageFiles($languageID, 'wsip.category', PACKAGE_ID);
		}
		
		// return new category
		return $category = new CategoryEditor($categoryID, null, null, false);
	}
	
	/**
	 * Updates the position of a specific category.
	 * 
	 * @param	integer		$categoryID
	 * @param	integer		$parentID
	 * @param	integer		$position
	 */
	public static function updatePosition($categoryID, $parentID, $position) {		
		$sql = "UPDATE	wsip".WSIP_N."_category
			SET	parentID = ".$parentID.",
				showOrder = ".$position."
			WHERE 	categoryID = ".$categoryID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Updates the stats for the given categories.
	 * 
	 * @param	string		$categoryIDs
	 */
	public static function refreshAll($categoryIDs) {
		if (empty($categoryIDs)) return;
		
		$sql = "UPDATE	wsip".WSIP_N."_category category
			SET	newsEntries = (
					SELECT	COUNT(*)
					FROM	wsip".WSIP_N."_news_entry
					WHERE	categoryID = category.categoryID
						AND isDeleted = 0
						AND isDisabled = 0
				)
			WHERE	categoryID IN (".$categoryIDs.")";
		WCF::getDB()->registerShutdownUpdate($sql);
	}
}
?>