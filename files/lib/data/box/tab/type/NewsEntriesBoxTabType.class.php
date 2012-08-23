<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/news/NewsEntry.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/box/tab/type/AbstractBoxTabType.class.php');

/**
 * Represents the news entries box tab type.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.box.tab.type
 * @category	Infinite Portal
 */
class NewsEntriesBoxTabType extends AbstractBoxTabType {
	/**
	 * list of box tab ids
	 *
	 * @var	array
	 */
	public $boxTabIDArray = array();

	/**
	 * language cache name
	 *
	 * @var	string
	 */
	public $languageCacheName = null;

	/**
	 * language filename
	 *
	 * @var	string
	 */
	public $languageFilename = '';

	/**
	 * @see	BoxTabType::cache()
	 */
	public function cache(BoxTab $boxTab) {
		if ($this->languageCacheName === null) {
			$languageIDArray = WCF::getSession()->getVisibleLanguageIDArray();
			if (count($languageIDArray)) {
				$this->languageCacheName = '-'.implode(',', $languageIDArray);
				$this->languageFilename = '-'.StringUtil::getHash(implode('-', $languageIDArray));
			}
		}
		if (!in_array($boxTab->boxTabID, $this->boxTabIDArray)) {
			$this->boxTabIDArray[] = $boxTab->boxTabID;

			// add cache resource
			WCF::getCache()->addResource('newsEntriesBoxTabType-'.$boxTab->boxTabID.$this->languageCacheName, WSIP_DIR.'cache/cache.newsEntriesBoxTabType-'.$boxTab->boxTabID.$this->languageFilename.'.php', WSIP_DIR.'lib/system/cache/CacheBuilderNewsEntriesBoxTabType.class.php', 0, 3600);
		}
	}

	/**
	 * @see	BoxTabType::getData()
	 */
	public function getData(BoxTab $boxTab) {
		return WCF::getCache()->get('newsEntriesBoxTabType-'.$boxTab->boxTabID.$this->languageCacheName);
	}

	/**
	 * @see	BoxTabType::resetCache()
	 */
	public function resetCache(BoxTab $boxTab) {
		WCF::getCache()->clear(WSIP_DIR.'cache/', 'cache.newsEntriesBoxTabType-'.$boxTab->boxTabID.'(-*)?.php', true);
	}

	/**
	 * @see	BoxTabType::isAccessible()
	 */
	public function isAccessible(BoxTab $boxTab) {
		return MODULE_NEWS;
	}

	/**
	 * @see	BoxTabType::getTemplateName()
	 */
	public function getTemplateName() {
		return 'newsEntriesBoxTabType';
	}
}
?>