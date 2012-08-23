<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/Category.class.php');
require_once(WSIP_DIR.'lib/data/news/NewsFeedEntryList.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/AbstractFeedPage.class.php');

/**
 * Prints a list of news entries as a rss or an atom feed.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	page
 * @category	Infinite Portal
 */
class NewsFeedPage extends AbstractFeedPage {
	/**
	 * list of category ids
	 *
	 * @var	array<integer>
	 */
	public $categoryIDArray = array();

	/**
	 * list of news entries
	 *
	 * @var	array<NewsFeedEntry>
	 */
	public $entryList = null;

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// get category ids
		if (isset($_REQUEST['categoryID'])) $this->categoryIDArray = ArrayUtil::toIntegerArray(explode(',', $_REQUEST['categoryID']));
		$accessibleCategoryIDArray = Category::getAccessibleCategoryIDArray(array('canViewCategory', 'canEnterCategory', 'canReadNewsEntry'));
		if (count($this->categoryIDArray)) {
			$this->categoryIDArray = array_merge($this->categoryIDArray, Category::getSubCategoryIDArray($this->categoryIDArray));
			$this->categoryIDArray = array_intersect($this->categoryIDArray, $accessibleCategoryIDArray);
		}
		else {
			$this->categoryIDArray = $accessibleCategoryIDArray;
		}

		// get entries
		$this->entryList = new NewsFeedEntryList();
		$this->entryList->sqlConditions .= 'news_entry.categoryID IN ('.implode(',', $this->categoryIDArray).')';
		$this->entryList->sqlConditions .= '	AND isDisabled = 0
							AND news_entry.time > '.($this->hours ? (TIME_NOW - $this->hours * 3600) : (TIME_NOW - 30 * 86400));
	}

	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		$this->entryList->sqlLimit = $this->limit;
		$this->entryList->readObjects();
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign('entries', $this->entryList->getObjects());
	}

	/**
	 * @see Page::show()
	 */
	public function show() {
		parent::show();

		// check module
		if (MODULE_NEWS != 1) {
			throw new IllegalLinkException();
		}

		// send content
		WCF::getTPL()->display(($this->format == 'atom' ? 'newsFeedAtom' : 'newsFeedRss2'), false);
	}
}
?>