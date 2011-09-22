<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/publication/Publication.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/message/search/AbstractSearchableMessageType.class.php');

/**
 * An implementation of SearchableMessageType for searching in publication objects.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.publication.object
 * @category	Infinite Portal
 */
abstract class AbstractPublicationObjectSearch extends AbstractSearchableMessageType {
	public $publicationType = '';
	public $publicationTypeObj = null;
	public $searchableMessageType = '';
	public $neededCategoryPermissions = array('canViewCategory', 'canEnterCategory');
	public $messageCache = array();
	public $categoryIDs = array();
	public $categories = array();
	public $categoryStructure = array();
	public $selectedCategories = array();
	
	/**
	 * Creates a new AbstractPublicationObjectSearch object.
	 */
	public function __construct() {
		$this->publicationTypeObj = Publication::getPublicationTypeObject($this->publicationType);
	}
	
	/**
	 * @see SearchableMessageType::getMessageData()
	 */
	public function getMessageData($messageID, $additionalData = null) {
		if (isset($this->messageCache[$messageID])) return $this->messageCache[$messageID];
		return null;
	}
	
	/**
	 * @see SearchableMessageType::show()
	 */
	public function show($form = null) {
		if (!$this->publicationTypeObj->enableCategorizing()) return;
		
		// get existing values
		if ($form !== null && isset($form->searchData['additionalData'][$this->searchableMessageType])) {
			$this->categoryIDs = $form->searchData['additionalData'][$this->searchableMessageType]['categoryIDs'];
		}
		
		WCF::getTPL()->assign(array(
			$this->publicationType.'CategoryOptions' => Category::getCategorySelect($this->publicationType, $this->neededCategoryPermissions),
			$this->publicationType.'CategoryIDs' => $this->categoryIDs,
			$this->publicationType.'SelectAllCategories' => count($this->categoryIDs) == 0 || $this->categoryIDs[0] == '*'
		));
	}
	
	/**
	 * Reads the given form parameters.
	 *
	 * @param	Form		$form
	 */
	protected function readFormParameters($form = null) {
		if (!$this->publicationTypeObj->enableCategorizing()) return;
		
		// get existing values
		if ($form !== null && isset($form->searchData['additionalData'][$this->searchableMessageType])) {
			$this->categoryIDs = $form->searchData['additionalData'][$this->searchableMessageType]['categoryIDs'];
		}
		
		// get new values
		if (isset($_POST[$this->publicationType.'CategoryIDs']) && is_array($_POST[$this->publicationType.'CategoryIDs'])) {
			$this->categoryIDs = ArrayUtil::toIntegerArray($_POST[$this->publicationType.'CategoryIDs']);
		}
	}
	
	/**
	 * Returns the selected categories.
	 *
	 * @param	Form		$form
	 * @return	string
	 */
	protected function getSelectedCategories($form = null) {
		$this->readFormParameters($form);
		
		$categoryIDs = $this->categoryIDs;
		if (count($categoryIDs) && $categoryIDs[0] == '*') $categoryIDs = array();
		
		// remove empty elements
		foreach ($categoryIDs as $key => $categoryID) {
			if ($categoryID == '-') unset($categoryIDs[$key]);
		}
		
		// get categories
		require_once(WSIP_DIR.'lib/data/category/Category.class.php');
		$this->categories = WCF::getCache()->get('category', 'categories');
		$this->categoryStructure = WCF::getCache()->get('category', 'categoryStructure');
		$this->selectedCategories = array();
		
		// check whether the selected category does exist
		foreach ($categoryIDs as $categoryID) {
			if (!isset($this->categories[$categoryID])) {
				throw new UserInputException('categoryIDs', 'notValid');
			}
			
			if (!isset($this->selectedCategories[$categoryID])) {
				$this->selectedCategories[$categoryID] = $this->categories[$categoryID];
				
				// include children
				$this->includeSubCategories($categoryID);
			}
		}
		if (count($this->selectedCategories) == 0) $this->selectedCategories = $this->categories;
		
		// check permission of the active user
		foreach ($this->selectedCategories as $category) {
			$result = true;
			foreach ($this->neededCategoryPermissions as $permission) {
				$result = $result && $category->getPermission($permission);
			}
			if (!$result) {
				unset($this->selectedCategories[$category->categoryID]);
			}
		}
		
		if (count($this->selectedCategories) == 0) {
			throw new PermissionDeniedException();
		}
		
		// get selected category ids
		$selectedCategoryIDs = '';
		if (count($this->selectedCategories) != count($this->categories)) {
			foreach ($this->selectedCategories as $category) {
				if (!empty($selectedCategoryIDs)) $selectedCategoryIDs .= ',';
				$selectedCategoryIDs .= $category->categoryID;
			}
		}
		
		// return selected category ids
		return $selectedCategoryIDs;
	}
	
	/**
	 * Includes the sub categories of the given category id to the selected category list.
	 * 
	 * @param	integer		$categoryID
	 */
	private function includeSubCategories($categoryID) {
		if (isset($this->categoryStructure[$categoryID])) {
			foreach ($this->categoryStructure[$categoryID] as $childCategoryID) {
				if (!isset($this->selectedCategories[$childCategoryID])) {
					$this->selectedCategories[$childCategoryID] = $this->categories[$childCategoryID];
					
					// include children
					$this->includeSubCategories($childCategoryID);
				}
			}
		}
	}
	
	/**
	 * @see SearchableMessageType::getAdditionalData()
	 */
	public function getAdditionalData() {
		if (!$this->publicationTypeObj->enableCategorizing()) return array();
		return array(
			'categoryIDs' => $this->categoryIDs
		);
	}
	
	/**
	 * @see SearchableMessageType::isAccessible()
	 */
	public function isAccessible() {
		if (!$this->publicationTypeObj->enableCategorizing()) return true;
		return count(Category::getCategorySelect($this->publicationType, $this->neededCategoryPermissions)) > 0;
	}
	
	/**
	 * @see SearchableMessageType::getFormTemplateName()
	 */
	public function getFormTemplateName() {
		if (!$this->publicationTypeObj->enableCategorizing()) return '';
		return 'search'.ucfirst($this->searchableMessageType);
	}
}
?>