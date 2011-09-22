<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/content/ContentItemEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/acp/form/ACPForm.class.php');
require_once(WCF_DIR.'lib/data/box/layout/BoxLayout.class.php');
require_once(WCF_DIR.'lib/system/style/StyleManager.class.php');

/**
 * Shows the content item add form.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	acp.form
 * @category	Infinite Portal
 */
class ContentItemAddForm extends ACPForm {
	// system
	public $templateName = 'contentItemAdd';
	public $activeMenuItem = 'wsip.acp.menu.link.content.contentItem.add';
	public $neededPermissions = 'admin.portal.canAddContentItem';
	
	/**
	 * content item editor object
	 * 
	 * @var	ContentItemEditor
	 */
	public $contentItem = null;
	
	/**
	 * list of available parent content items
	 * 
	 * @var	array
	 */
	public $contentItemOptions = array();
	
	/**
	 * list of available box layouts
	 * 
	 * @var	array
	 */
	public $boxLayoutOptions = array();
	
	/**
	 * list of available styles
	 * 
	 * @var	array
	 */
	public $availableStyles = array();
	
	/**
	 * list of available permisions
	 * 
	 * @var	array
	 */
	public $permissionSettings = array();
	
	/**
	 * publishing start time
	 * 
	 * @var	integer
	 */
	public $publishingStartTime = 0;
	
	/**
	 * publishing end time
	 * 
	 * @var	integer
	 */
	public $publishingEndTime = 0;
	
	// parameters
	public $parentID = 0;
	public $title = '';
	public $description = '';
	public $text = '';
	public $contentItemType = 0;
	public $externalURL = '';
	public $icon = '';
	public $metaDescription = '';
	public $metaKeywords = '';
	public $publishingStartTimeDay = '';
	public $publishingStartTimeMonth = '';
	public $publishingStartTimeYear = '';
	public $publishingStartTimeHour = '';
	public $publishingStartTimeMinutes = '';
	public $publishingEndTimeDay = '';
	public $publishingEndTimeMonth = '';
	public $publishingEndTimeYear = '';
	public $publishingEndTimeHour = '';
	public $publishingEndTimeMinutes = '';
	public $styleID = 0;
	public $enforceStyle = 0;
	public $boxLayoutID = 0;
	public $allowSpidersToIndexThisPage = 1;
	public $showOrder = 0;
	public $permissions = array();
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['parentID'])) $this->parentID = intval($_REQUEST['parentID']);
		
		// get permission settings
		$this->permissionSettings = ContentItem::getPermissionSettings();
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		$this->contentItemOptions = ContentItem::getContentItemSelect(array());
		$this->boxLayoutOptions = BoxLayout::getBoxLayouts();
		$this->availableStyles = StyleManager::getAvailableStyles();
	}
	
	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		$this->allowSpidersToIndexThisPage = 0;
		
		if (isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
		if (isset($_POST['description'])) $this->description = StringUtil::trim($_POST['description']);
		if (isset($_POST['text'])) $this->text = StringUtil::trim($_POST['text']);
		if (isset($_POST['contentItemType'])) $this->contentItemType = intval($_POST['contentItemType']);
		if (isset($_POST['externalURL'])) $this->externalURL = StringUtil::trim($_POST['externalURL']);
		if (isset($_POST['icon'])) $this->icon = StringUtil::trim($_POST['icon']);
		if (isset($_POST['metaDescription'])) $this->metaDescription = StringUtil::trim($_POST['metaDescription']);
		if (isset($_POST['metaKeywords'])) $this->metaKeywords = StringUtil::trim($_POST['metaKeywords']);
		if (isset($_POST['styleID'])) $this->styleID = intval($_POST['styleID']);
		if (isset($_POST['enforceStyle'])) $this->enforceStyle = intval($_POST['enforceStyle']);
		if (isset($_POST['boxLayoutID'])) $this->boxLayoutID = intval($_POST['boxLayoutID']);
		if (isset($_POST['allowSpidersToIndexThisPage'])) $this->allowSpidersToIndexThisPage = intval($_POST['allowSpidersToIndexThisPage']);
		if (isset($_POST['showOrder'])) $this->showOrder = intval($_POST['showOrder']);
		if (isset($_POST['permission']) && is_array($_POST['permission'])) $this->permissions = $_POST['permission'];
		
		// publishing start time
		if (isset($_POST['publishingStartTimeDay'])) $this->publishingStartTimeDay = intval($_POST['publishingStartTimeDay']);
		if (isset($_POST['publishingStartTimeMonth'])) $this->publishingStartTimeMonth = intval($_POST['publishingStartTimeMonth']);
		if (!empty($_POST['publishingStartTimeYear'])) $this->publishingStartTimeYear = intval($_POST['publishingStartTimeYear']);
		if (isset($_POST['publishingStartTimeHour'])) $this->publishingStartTimeHour = intval($_POST['publishingStartTimeHour']);
		if (isset($_POST['publishingStartTimeMinutes'])) $this->publishingStartTimeMinutes = intval($_POST['publishingStartTimeMinutes']);
		
		// publishing end time
		if (isset($_POST['publishingEndTimeDay'])) $this->publishingEndTimeDay = intval($_POST['publishingEndTimeDay']);
		if (isset($_POST['publishingEndTimeMonth'])) $this->publishingEndTimeMonth = intval($_POST['publishingEndTimeMonth']);
		if (!empty($_POST['publishingEndTimeYear'])) $this->publishingEndTimeYear = intval($_POST['publishingEndTimeYear']);
		if (isset($_POST['publishingEndTimeHour'])) $this->publishingEndTimeHour = intval($_POST['publishingEndTimeHour']);
		if (isset($_POST['publishingEndTimeMinutes'])) $this->publishingEndTimeMinutes = intval($_POST['publishingEndTimeMinutes']);
	}
	
	/**
	 * @see Form::validate()
	 */
	public function validate() {
		parent::validate();
		
		// content item type
		if ($this->contentItemType < 0 || $this->contentItemType > 2) {
			throw new UserInputException('contentItemType', 'invalid');
		}
		
		// parent id
		$this->validateParentID();
		
		// title
		if (empty($this->title)) {
			throw new UserInputException('title');
		}
		
		// text
		if ($this->contentItemType == 0 && empty($this->text)) {
			throw new UserInputException('text');
		}
		
		// external url
		if ($this->contentItemType == 1 && empty($this->externalURL)) {
			throw new UserInputException('externalURL');
		}
		
		// publishing start time
		$this->validatePublishingTime('publishingStartTime', array(
			'day' => $this->publishingStartTimeDay,
			'month' => $this->publishingStartTimeMonth,
			'year' => $this->publishingStartTimeYear,
			'hour' => $this->publishingStartTimeHour,
			'minutes' => $this->publishingStartTimeMinutes
		));
		
		// publishing end time
		$this->validatePublishingTime('publishingEndTime', array(
			'day' => $this->publishingEndTimeDay,
			'month' => $this->publishingEndTimeMonth,
			'year' => $this->publishingEndTimeYear,
			'hour' => $this->publishingEndTimeHour,
			'minutes' => $this->publishingEndTimeMinutes
		));
		
		// allowSpidersToIndexThisPage
		if ($this->allowSpidersToIndexThisPage < 0 || $this->allowSpidersToIndexThisPage > 1) {
			throw new UserInputException('allowSpidersToIndexThisPage');
		}
		
		// meta keywords
		$this->metaKeywords = implode(',', ArrayUtil::trim(explode(',', $this->metaKeywords)));
		
		// permissions
		$this->validatePermissions();
	}
	
	/**
	 * Validates the parent id.
	 */
	protected function validateParentID() {
		if ($this->parentID) {
			try {
				ContentItem::getContentItem($this->parentID);
			}
			catch (IllegalLinkException $e) {
				throw new UserInputException('parentID', 'invalid');
			}
		}
	}
	
	/**
	 * Validates the publishing time with the given name and the given time options.
	 * 
	 * @param	string		$name
	 * @param	array		$options
	 */
	protected function validatePublishingTime($name, $options) {
		if ($options['day'] || $options['month'] || $options['year'] || $options['hour'] || $options['minutes']) {
			$time = @gmmktime($options['hour'], $options['minutes'], 0, $options['month'], $options['day'], $options['year']);
			// since php5.1.0 mktime returns false on failure
			if ($time === false || $time === -1) {
				throw new UserInputException($name, 'invalid');
			}
			
			// get utc time
			$time = DateUtil::getUTC($time);
			if ($this->contentItem === null && $time <= TIME_NOW) {
				throw new UserInputException($name, 'invalid');
			}
			
			$this->$name = $time;
		}
	}
	
	/**
	 * Validates the permissions.
	 */
	public function validatePermissions() {
		$settings = array_flip($this->permissionSettings);
		foreach ($this->permissions as $permission) {
			// type
			if (!isset($permission['type']) || ($permission['type'] != 'user' && $permission['type'] != 'group')) {
				throw new UserInputException();
			}
			
			// id
			if (!isset($permission['id'])) {
				throw new UserInputException();
			}
			if ($permission['type'] == 'user') {
				$user = new User(intval($permission['id']));
				if (!$user->userID) throw new UserInputException();
			}
			else {
				$group = new Group(intval($permission['id']));
				if (!$group->groupID) throw new UserInputException();
			}
			
			// settings
			if (!isset($permission['settings']) || !is_array($permission['settings'])) {
				throw new UserInputException();
			}
			
			// find invalid settings
			foreach ($permission['settings'] as $key => $value) {
				if (!isset($settings[$key]) || ($value != -1 && $value != 0 && $value =! 1)) {
					throw new UserInputException();
				}
			}
			
			// find missing settings
			foreach ($settings as $key => $value) {
				if (!isset($permission['settings'][$key])) {
					throw new UserInputException();
				}
			}
		}
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();
		
		// save item
		$this->contentItem = ContentItemEditor::create($this->parentID, $this->title, $this->description, $this->text, $this->contentItemType, $this->externalURL, $this->icon, $this->metaDescription, $this->metaKeywords,
		$this->publishingStartTime, $this->publishingEndTime, $this->styleID, $this->enforceStyle, $this->boxLayoutID, $this->allowSpidersToIndexThisPage, $this->showOrder, WCF::getLanguage()->getLanguageID());
		
		// save permissions
		$this->permissions = ContentItemEditor::getCleanedPermissions($this->permissions);
		$this->contentItem->addPermissions($this->permissions, $this->permissionSettings);
		
		// reset cache
		ContentItemEditor::resetCache();
		
		// reset sessions
		Session::resetSessions(array(), true, false);
		$this->saved();
		
		// reset values
		$this->parentID = $this->contentItemType = $this->styleID = $this->enforceStyle = $this->showOrder = 0;
		$this->title = $this->description = $this->text = $this->externalURL = $this->icon = $this->metaDescription = $this->metaKeywords = 
		$this->publishingStartTimeDay = $this->publishingStartTimeMonth = $this->publishingStartTimeYear = $this->publishingStartTimeHour = $this->publishingStartTimeMinutes =
		$this->publishingEndTimeDay = $this->publishingEndTimeMonth = $this->publishingEndTimeYear = $this->publishingEndTimeHour = $this->publishingEndTimeMinutes = '';
		$this->allowSpidersToIndexThisPage = 1;
		$this->permissions = array();
		
		// show success message
		WCF::getTPL()->assign('success', true);
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		require_once(WCF_DIR.'lib/page/util/InlineCalendar.class.php');
		InlineCalendar::assignVariables();
		
		WCF::getTPL()->assign(array(
			'action' => 'add',
			'contentItemOptions' => $this->contentItemOptions,
			'boxLayoutOptions' => $this->boxLayoutOptions,
			'availableStyles' => $this->availableStyles,
			'permissions' => $this->permissions,
			'permissionSettings' => $this->permissionSettings,
			'parentID' => $this->parentID,
			'title' => $this->title,
			'description' => $this->description,
			'text' => $this->text,
			'contentItemType' => $this->contentItemType,
			'externalURL' => $this->externalURL,
			'icon' => $this->icon,
			'metaDescription' => $this->metaDescription,
			'metaKeywords' => $this->metaKeywords,
			'publishingStartTimeDay' => $this->publishingStartTimeDay,
			'publishingStartTimeMonth' => $this->publishingStartTimeMonth,
			'publishingStartTimeYear' => $this->publishingStartTimeYear,
			'publishingStartTimeHour' => $this->publishingStartTimeHour,
			'publishingStartTimeMinutes' => $this->publishingStartTimeMinutes,
			'publishingEndTimeDay' => $this->publishingEndTimeDay,
			'publishingEndTimeMonth' => $this->publishingEndTimeMonth,
			'publishingEndTimeYear' => $this->publishingEndTimeYear,
			'publishingEndTimeHour' => $this->publishingEndTimeHour,
			'publishingEndTimeMinutes' => $this->publishingEndTimeMinutes,
			'styleID' => $this->styleID,
			'enforceStyle' => $this->enforceStyle,
			'boxLayoutID' => $this->boxLayoutID,
			'allowSpidersToIndexThisPage' => $this->allowSpidersToIndexThisPage,
			'showOrder' => $this->showOrder
		));
	}
}
?>