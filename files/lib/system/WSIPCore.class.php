<?php
// wcf imports
require_once(WCF_DIR.'lib/page/util/menu/ModerationCPMenuContainer.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenuContainer.class.php');
require_once(WCF_DIR.'lib/page/util/menu/UserCPMenuContainer.class.php');
require_once(WCF_DIR.'lib/page/util/menu/UserProfileMenuContainer.class.php');
require_once(WCF_DIR.'lib/system/box/BoxLayoutManager.class.php');
require_once(WCF_DIR.'lib/system/style/StyleManager.class.php');

/**
 * This class extends the main WCF class by portal specific functions.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	system
 * @category	Infinite Portal
 */
class WSIPCore extends WCF implements ModerationCPMenuContainer, PageMenuContainer, UserCPMenuContainer, UserProfileMenuContainer {
	protected static $moderationCPMenuObj = null;
	protected static $pageMenuObj = null;
	protected static $userCPMenuObj = null;
	protected static $userProfileMenuObj = null;
	public static $availablePagesDuringOfflineMode = array(
		'page' => array('Captcha', 'LegalNotice'),
		'form' => array('UserLogin'),
		'action' => array('UserLogout')
	);
	public static $defaultBoxPositions = array('left', 'right', 'top', 'bottom', 'userMessages');
	
	/**
	 * @see WCF::initTPL()
	 */
	protected function initTPL() {
		// init style to get template pack id
		$this->initStyle();
		
		// init box layout
		$this->initBoxLayout();
		
		global $packageDirs;
		require_once(WCF_DIR.'lib/system/template/StructuredTemplate.class.php');
		self::$tplObj = new StructuredTemplate(self::getStyle()->templatePackID, self::getLanguage()->getLanguageID(), ArrayUtil::appendSuffix($packageDirs, 'templates/'));
		$this->assignDefaultTemplateVariables();
		
		// init cronjobs
		$this->initCronjobs();
		
		// check offline mode
		if (OFFLINE && !self::getUser()->getPermission('user.portal.canViewPortalOffline')) {
			$showOfflineError = true;
			foreach (self::$availablePagesDuringOfflineMode as $type => $names) {
				if (isset($_REQUEST[$type])) {
					foreach ($names as $name) {
						if ($_REQUEST[$type] == $name) {
							$showOfflineError = false;
							break 2;
						}
					}
					
					break;
				}
			}
			
			if ($showOfflineError) {
				$this->disableBoxLayout();
				self::getTPL()->display('offline');
				exit;
			}
		}
		
		// user ban
		if (self::getUser()->banned && (!isset($_REQUEST['page']) || $_REQUEST['page'] != 'LegalNotice')) {
			$this->disableBoxLayout();
			throw new NamedUserException(WCF::getLanguage()->getDynamicVariable('wcf.user.banned'));
		}
	}
	
	/**
	 * Initialises the cronjobs.
	 */
	protected function initCronjobs() {
		self::getTPL()->assign('executeCronjobs', WCF::getCache()->get('cronjobs-'.PACKAGE_ID, 'nextExec') < TIME_NOW);
	}
	
	/**
	 * @see WCF::loadDefaultCacheResources()
	 */
	protected function loadDefaultCacheResources() {
		parent::loadDefaultCacheResources();
		self::loadDefaultWSIPCacheResources();
	}
	
	/**
	 * Loads default cache resources of content management system.
	 * Can be called statically from other applications or plugins.
	 */
	public static function loadDefaultWSIPCacheResources() {
		WCF::getCache()->addResource('category', WSIP_DIR.'cache/cache.category.php', WSIP_DIR.'lib/system/cache/CacheBuilderCategory.class.php');
		WCF::getCache()->addResource('categoryData', WSIP_DIR.'cache/cache.categoryData.php', WSIP_DIR.'lib/system/cache/CacheBuilderCategoryData.class.php', 0, 300);
		WCF::getCache()->addResource('contentItem', WSIP_DIR.'cache/cache.contentItem.php', WSIP_DIR.'lib/system/cache/CacheBuilderContentItem.class.php');
		WCF::getCache()->addResource('stat', WSIP_DIR.'cache/cache.stat.php', WSIP_DIR.'lib/system/cache/CacheBuilderStat.class.php', 0, 300);
		WCF::getCache()->addResource('newsStat', WSIP_DIR.'cache/cache.newsStat.php', WSIP_DIR.'lib/system/cache/CacheBuilderNewsStat.class.php', 0, 300);
		WCF::getCache()->addResource('pageLocations-'.PACKAGE_ID, WCF_DIR.'cache/cache.pageLocations-'.PACKAGE_ID.'.php', WCF_DIR.'lib/system/cache/CacheBuilderPageLocations.class.php');
		WCF::getCache()->addResource('bbcodes', WCF_DIR.'cache/cache.bbcodes.php', WCF_DIR.'lib/system/cache/CacheBuilderBBCodes.class.php');
		WCF::getCache()->addResource('smileys', WCF_DIR.'cache/cache.smileys.php', WCF_DIR.'lib/system/cache/CacheBuilderSmileys.class.php');
		WCF::getCache()->addResource('cronjobs-'.PACKAGE_ID, WCF_DIR.'cache/cache.cronjobs-'.PACKAGE_ID.'.php', WCF_DIR.'lib/system/cache/CacheBuilderCronjobs.class.php');
		WCF::getCache()->addResource('help-'.PACKAGE_ID, WCF_DIR.'cache/cache.help-'.PACKAGE_ID.'.php', WCF_DIR.'lib/system/cache/CacheBuilderHelp.class.php');
		WCF::getCache()->addResource('box-'.PACKAGE_ID, WCF_DIR.'cache/cache.box-'.PACKAGE_ID.'.php', WCF_DIR.'lib/system/cache/CacheBuilderBox.class.php');
		WCF::getCache()->addResource('boxTab-'.PACKAGE_ID, WCF_DIR.'cache/cache.boxTab-'.PACKAGE_ID.'.php', WCF_DIR.'lib/system/cache/CacheBuilderBoxTab.class.php');
		WCF::getCache()->addResource('boxTypes-'.PACKAGE_ID, WCF_DIR.'cache/cache.boxType-'.PACKAGE_ID.'.php', WCF_DIR.'lib/system/cache/CacheBuilderBoxTypes.class.php');
		WCF::getCache()->addResource('boxLayout-'.PACKAGE_ID, WCF_DIR.'cache/cache.boxLayout-'.PACKAGE_ID.'.php', WCF_DIR.'lib/system/cache/CacheBuilderBoxLayout.class.php');
		WCF::getCache()->addResource('boxPosition-'.PACKAGE_ID, WCF_DIR.'cache/cache.boxPosition-'.PACKAGE_ID.'.php', WCF_DIR.'lib/system/cache/CacheBuilderBoxPosition.class.php');
	}
	
	/**
	 * Initialises the moderationcp menu.
	 */
	protected static function initModerationCPMenu() {
		require_once(WCF_DIR.'lib/page/util/menu/ModerationCPMenu.class.php');
		self::$moderationCPMenuObj = ModerationCPMenu::getInstance();
	}
	
	/**
	 * Initialises the page header menu.
	 */
	protected static function initPageMenu() {
		require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
		self::$pageMenuObj = new PageMenu();
		if (PageMenu::getActiveMenuItem() == '') PageMenu::setActiveMenuItem('wsip.header.menu.portal');
	}
	
	/**
	 * Initialises the user cp menu.
	 */
	protected static function initUserCPMenu() {
		require_once(WCF_DIR.'lib/page/util/menu/UserCPMenu.class.php');
		self::$userCPMenuObj = UserCPMenu::getInstance();
	}
	
	/**
	 * Initialises the user profile menu.
	 */
	protected static function initUserProfileMenu() {
		require_once(WCF_DIR.'lib/page/util/menu/UserProfileMenu.class.php');
		self::$userProfileMenuObj = UserProfileMenu::getInstance();
	}
	
	/**
	 * @see WCF::getOptionsFilename()
	 */
	protected function getOptionsFilename() {
		return WSIP_DIR.'options.inc.php';
	}
	
	/**
	 * Initialises the style system.
	 */
	protected function initStyle() {
		if (isset($_GET['styleID'])) {
			self::getSession()->setStyleID(intval($_GET['styleID']));
		}
		
		StyleManager::changeStyle(self::getSession()->getStyleID());
	}
	
	/**
	 * Initialises the box layout system.
	 */
	protected function initBoxLayout() {
		// register default positions
		BoxLayout::registerPositions(self::$defaultBoxPositions);
		
		// change box layout to default
		BoxLayoutManager::changeBoxLayout();
	}
	
	/**
	 * Disables the box layout.
	 */
	protected function disableBoxLayout() {
		// set empty box layout
		$emptyBoxLayout = new BoxLayout(null, array('boxLayoutID' => 0));
		BoxLayoutManager::setBoxLayout($emptyBoxLayout);
	}
	
	/**
	 * @see ModerationCPMenuContainer::getModerationCPMenu()
	 */
	public static final function getModerationCPMenu() {
		if (self::$moderationCPMenuObj === null) {
			self::initModerationCPMenu();
		}		
		return self::$moderationCPMenuObj;
	}
	
	/**
	 * @see PageMenuContainer::getPageMenu()
	 */
	public static final function getPageMenu() {
		if (self::$pageMenuObj === null) {
			self::initPageMenu();
		}
		
		return self::$pageMenuObj;
	}
	
	/**
	 * @see UserCPMenuContainer::getUserCPMenu()
	 */
	public static final function getUserCPMenu() {
		if (self::$userCPMenuObj === null) {
			self::initUserCPMenu();
		}
		
		return self::$userCPMenuObj;
	}
	
	/**
	 * @see UserProfileMenuContainer::getUserProfileMenu()
	 */
	public static final function getUserProfileMenu() {
		if (self::$userProfileMenuObj === null) {
			self::initUserProfileMenu();
		}
		
		return self::$userProfileMenuObj;
	}
	
	/**
	 * Returns the active style object.
	 * 
	 * @return	ActiveStyle
	 */
	public static final function getStyle() {
		return StyleManager::getStyle();
	}
	
	/**
	 * Returns the active style object.
	 * 
	 * @return	BoxLayout
	 */
	public static final function getBoxLayout() {
		return BoxLayoutManager::getBoxLayout();
	}
	
	/**
	 * @see WCF::initSession()
	 */
	protected function initSession() {
		// start session
		require_once(WSIP_DIR.'lib/system/session/WSIPSessionFactory.class.php');
		$factory = new WSIPSessionFactory();
		self::$sessionObj = $factory->get();
		self::$userObj = self::getSession()->getUser();
	}
	
	/**
	 * @see	WCF::assignDefaultTemplateVariables()
	 */
	protected function assignDefaultTemplateVariables() {
		parent::assignDefaultTemplateVariables();
		self::getTPL()->registerPrefilter('icon');
		self::getTPL()->assign(array(
			'metaDescription' => META_DESCRIPTION,
			'metaKeywords' => META_KEYWORDS,
			'timezone' => DateUtil::getTimezone(),
			'stylePickerOptions' => (SHOW_STYLE_CHOOSER ? StyleManager::getAvailableStyles() : array())
		));
	}
}
?>