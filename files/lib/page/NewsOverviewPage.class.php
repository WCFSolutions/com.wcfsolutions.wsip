<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/Category.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/SortablePage.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');

/**
 * Shows an overview of all news entries.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	page
 * @category	Infinite Portal
 */
class NewsOverviewPage extends SortablePage {
	// system
	public $templateName = 'newsOverview';
	public $defaultSortField = 'time';
	public $defaultSortOrder = 'DESC';
	public $itemsPerPage = NEWS_ENTRIES_PER_PAGE;
	
	/**
	 * list of news entries
	 * 
	 * @var NewsEntryList
	 */
	public $entryList = null;
	
	/**
	 * category id
	 * 
	 * @var integer
	 */
	public $categoryID = 0;
	
	/**
	 * category object
	 * 
	 * @var Category
	 */
	public $category = null;
	
	/**
	 * tag list object
	 * 
	 * @var TagList
	 */
	public $tagList = null;
	
	/**
	 * list of tags
	 * 
	 * @var	array
	 */
	public $tags = array();
	
	/**
	 * tag id
	 * 
	 * @var integer
	 */
	public $tagID = 0;
	
	/**
	 * tag object
	 * 
	 * @var Tag
	 */
	public $tag = null;
	
	/**
	 * number of marked entries
	 * 
	 * @var	integer
	 */
	public $markedEntries = 0;
	
	/**
	 * list of stats
	 * 
	 * @var	array
	 */
	public $stats = array();
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get category
		if (isset($_REQUEST['categoryID'])) $this->categoryID = intval($_REQUEST['categoryID']);
		
		// get tag
		if (isset($_REQUEST['tagID'])) $this->tagID = intval($_REQUEST['tagID']);
		
		// get category ids
		if ($this->categoryID) {
			// get category
			$this->category = new Category($this->categoryID);
			$this->category->enter('news');
			
			// get sub categories
			$categoryIDArray = Category::getSubCategoryIDArray($this->categoryID);
			$categoryIDArray = array_intersect($categoryIDArray, Category::getAccessibleCategoryIDArray());
			array_unshift($categoryIDArray, $this->categoryID);
		}
		else {
			$categoryIDArray = Category::getAccessibleCategoryIDArray();
		}
		
		if (count($categoryIDArray)) {
			// init entry list
			if (MODULE_TAGGING && $this->tagID) {
				require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
				$this->tag = TagEngine::getInstance()->getTagByID($this->tagID);
				if ($this->tag === null) {
					throw new IllegalLinkException();
				}
				require_once(WSIP_DIR.'lib/data/news/TaggedCategoryNewsEntryList.class.php');
				$this->entryList = new TaggedCategoryNewsEntryList($this->tagID, $categoryIDArray);
			}
			else {
				require_once(WSIP_DIR.'lib/data/news/CategoryNewsEntryList.class.php');
				$this->entryList = new CategoryNewsEntryList($categoryIDArray);
			}
			
			// init tag list
			if (MODULE_TAGGING) {
				require_once(WSIP_DIR.'lib/data/news/NewsCategoryTagList.class.php');
				$this->tagList = new NewsCategoryTagList($categoryIDArray, WCF::getSession()->getVisibleLanguageIDArray());
			}
		}
	}
	
	/**
	 * @see MultipleLinkPage::countItems()
	 */
	public function countItems() {
		parent::countItems();
		
		if ($this->entryList == null) return 0;
		return $this->entryList->countObjects();
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		if ($this->entryList != null) {
			// read entries
			$this->entryList->sqlOffset = ($this->pageNo - 1) * $this->itemsPerPage;
			$this->entryList->sqlLimit = $this->itemsPerPage;
			$this->entryList->sqlOrderBy = 'news_entry.'.$this->sortField." ".$this->sortOrder. 
							($this->sortField == 'rating' ? ", news_entry.ratings ".$this->sortOrder : '');
			$this->entryList->readObjects();
			
			// read tags
			if (MODULE_TAGGING) {
				$this->tagList->readObjects();
				$this->tags = $this->tagList->getObjects();
			}
		}
		
		// render stats
		if (NEWS_ENABLE_STATS) {
			$this->readStats();
		}
		
		// get marked entries
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedNewsEntries'])) {
			$this->markedEntries = count($sessionVars['markedNewsEntries']);
		}
	}
	
	/**
	 * Reads the stats.
	 */
	protected function readStats() {
		if ($this->categoryID) {
			$stats = WCF::getCache()->get('categoryData', 'stats');
			$this->stats = $stats[$this->categoryID];
		}
		else {
			$this->stats = WCF::getCache()->get('stat');
		}
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'metaDescription' => ($this->category != null ? StringUtil::stripHTML($this->category->getFormattedDescription()) : ''),
			'metaKeywords' => TaggingUtil::buildString($this->tags, ','),
			'url' => 'index.php?page=NewsOverview'.($this->categoryID ? '&categoryID='.$this->categoryID : '').'&pageNo='.$this->pageNo.SID_ARG_2ND_NOT_ENCODED,
			'pageType' => ($this->categoryID ? 'category' : 'overview'),
			'permissions' => ($this->categoryID ? $this->category->getModeratorPermissions() : Category::getGlobalModeratorPermissions()),
			'markedEntries' => $this->markedEntries,
			'entries' => ($this->entryList != null ? $this->entryList->getObjects() : array()),
			'tags' => ($this->entryList != null ? $this->entryList->getTags() : array()),
			'availableTags' => $this->tags,
			'tagID' => $this->tagID,
			'tag' => $this->tag,
			'categoryID' => $this->categoryID,
			'category' => $this->category,
			'categoryQuickJumpOptions' => Category::getCategorySelect('news'),
			'stats' => $this->stats,
			'allowSpidersToIndexThisPage' => true
		));
	}
	
	/**
	 * @see SortablePage::validateSortField()
	 */
	public function validateSortField() {
		parent::validateSortField();
		
		switch ($this->sortField) {
			case 'comments':
			case 'views':
			case 'rating':
			case 'time': break;
			default: $this->sortField = $this->defaultSortField;
		}
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// check module
		if (MODULE_NEWS != 1) {
			throw new IllegalLinkException();
		}
		
		// set active page menu item
		PageMenu::setActiveMenuItem('wsip.header.menu.news');
		
		// init publication type
		if ($this->category == null) {
			require_once(WSIP_DIR.'lib/data/publication/Publication.class.php');
			Publication::initPublicationType('news');
		}
		
		parent::show();
	}
}
?>