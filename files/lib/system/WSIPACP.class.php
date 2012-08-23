<?php
// wcf imports
require_once(WCF_DIR.'lib/system/WCFACP.class.php');

/**
 * This class extends the main WCFACP class by filebase specific functions.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	system
 * @category	Infinite Portal
 */
class WSIPACP extends WCFACP {
	/**
	 * @see WCF::getOptionsFilename()
	 */
	protected function getOptionsFilename() {
		return WSIP_DIR.'options.inc.php';
	}

	/**
	 * Initialises the template engine.
	 */
	protected function initTPL() {
		global $packageDirs;

		self::$tplObj = new ACPTemplate(self::getLanguage()->getLanguageID(), ArrayUtil::appendSuffix($packageDirs, 'acp/templates/'));
		$this->assignDefaultTemplateVariables();
	}

	/**
	 * Does the user authentication.
	 */
	protected function initAuth() {
		parent::initAuth();

		// user ban
		if (self::getUser()->banned) {
			throw new PermissionDeniedException();
		}
	}

	/**
	 * @see WCF::assignDefaultTemplateVariables()
	 */
	protected function assignDefaultTemplateVariables() {
		parent::assignDefaultTemplateVariables();

		self::getTPL()->assign(array(
			// add jump to cms link
			'additionalHeaderButtons' => '<li><a href="'.RELATIVE_WSIP_DIR.'index.php?page=Index"><img src="'.RELATIVE_WSIP_DIR.'icon/indexS.png" alt="" /> <span>'.WCF::getLanguage()->get('wsip.acp.jumpToPortal').'</span></a></li>',
			// individual page title
			'pageTitle' => WCF::getLanguage()->get(StringUtil::encodeHTML(PAGE_TITLE)).' - '.StringUtil::encodeHTML(PACKAGE_NAME.' '.PACKAGE_VERSION)
		));
	}

	/**
	 * @see WCF::loadDefaultCacheResources()
	 */
	protected function loadDefaultCacheResources() {
		parent::loadDefaultCacheResources();
		$this->loadDefaultWSIPCacheResources();
	}

	/**
	 * Loads default cache resources of content management system acp.
	 * Can be called statically from other applications or plugins.
	 */
	public static function loadDefaultWSIPCacheResources() {
		WCF::getCache()->addResource('category', WSIP_DIR.'cache/cache.category.php', WSIP_DIR.'lib/system/cache/CacheBuilderCategory.class.php');
		WCF::getCache()->addResource('categoryData', WSIP_DIR.'cache/cache.categoryData.php', WSIP_DIR.'lib/system/cache/CacheBuilderCategoryData.class.php', 0, 300);
		WCF::getCache()->addResource('contentItem', WSIP_DIR.'cache/cache.contentItem.php', WSIP_DIR.'lib/system/cache/CacheBuilderContentItem.class.php');

		// boxes
		WCF::getCache()->addResource('box-'.PACKAGE_ID, WCF_DIR.'cache/cache.box-'.PACKAGE_ID.'.php', WCF_DIR.'lib/system/cache/CacheBuilderBox.class.php');
		WCF::getCache()->addResource('boxTab-'.PACKAGE_ID, WCF_DIR.'cache/cache.boxTab-'.PACKAGE_ID.'.php', WCF_DIR.'lib/system/cache/CacheBuilderBoxTab.class.php');
		WCF::getCache()->addResource('boxTypes-'.PACKAGE_ID, WCF_DIR.'cache/cache.boxType-'.PACKAGE_ID.'.php', WCF_DIR.'lib/system/cache/CacheBuilderBoxTypes.class.php');
		WCF::getCache()->addResource('boxLayout-'.PACKAGE_ID, WCF_DIR.'cache/cache.boxLayout-'.PACKAGE_ID.'.php', WCF_DIR.'lib/system/cache/CacheBuilderBoxLayout.class.php');
		WCF::getCache()->addResource('boxPosition-'.PACKAGE_ID, WCF_DIR.'cache/cache.boxPosition-'.PACKAGE_ID.'.php', WCF_DIR.'lib/system/cache/CacheBuilderBoxPosition.class.php');

		// navigations
		WCF::getCache()->addResource('navigation-'.PACKAGE_ID, WCF_DIR.'cache/cache.navigation-'.PACKAGE_ID.'.php', WCF_DIR.'lib/system/cache/CacheBuilderNavigation.class.php');
		WCF::getCache()->addResource('navigationItem-'.PACKAGE_ID, WCF_DIR.'cache/cache.navigationItem-'.PACKAGE_ID.'.php', WCF_DIR.'lib/system/cache/CacheBuilderNavigationItem.class.php');
	}
}
?>