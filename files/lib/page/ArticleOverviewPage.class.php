<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/Category.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/SortablePage.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');

/**
 * Shows an overview of all articles.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	page
 * @category	Infinite Portal
 */
class ArticleOverviewPage extends SortablePage {
	// system
	public $templateName = 'articleOverview';
	public $defaultSortField = 'time';
	public $defaultSortOrder = 'DESC';
	public $itemsPerPage = ARTICLES_PER_PAGE;

	/**
	 * list of articles
	 *
	 * @var ArticleList
	 */
	public $articleList = null;

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
			$this->category->enter('article');

			// get sub categories
			$categoryIDArray = Category::getSubCategoryIDArray($this->categoryID);
			$categoryIDArray = array_intersect($categoryIDArray, Category::getAccessibleCategoryIDArray());
			array_unshift($categoryIDArray, $this->categoryID);
		}
		else {
			$categoryIDArray = Category::getAccessibleCategoryIDArray();
		}

		if (count($categoryIDArray)) {
			// init article list
			if (MODULE_TAGGING && $this->tagID) {
				require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
				$this->tag = TagEngine::getInstance()->getTagByID($this->tagID);
				if ($this->tag === null) {
					throw new IllegalLinkException();
				}
				require_once(WSIP_DIR.'lib/data/article/TaggedArticleList.class.php');
				$this->articleList = new TaggedArticleList($this->tagID, $categoryIDArray);
			}
			else {
				require_once(WSIP_DIR.'lib/data/article/ViewableArticleList.class.php');
				$this->articleList = new ViewableArticleList($categoryIDArray);
			}

			// init tag list
			if (MODULE_TAGGING) {
				require_once(WSIP_DIR.'lib/data/article/CategoryArticleTagList.class.php');
				$this->tagList = new CategoryArticleTagList($categoryIDArray, WCF::getSession()->getVisibleLanguageIDArray());
			}
		}
	}

	/**
	 * @see MultipleLinkPage::countItems()
	 */
	public function countItems() {
		parent::countItems();

		if ($this->articleList == null) return 0;
		return $this->articleList->countObjects();
	}

	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		if ($this->articleList != null) {
			// read entries
			$this->articleList->sqlOffset = ($this->pageNo - 1) * $this->itemsPerPage;
			$this->articleList->sqlLimit = $this->itemsPerPage;
			$this->articleList->sqlOrderBy = 'article.'.$this->sortField." ".$this->sortOrder.
							($this->sortField == 'rating' ? ", article.ratings ".$this->sortOrder : '');
			$this->articleList->readObjects();

			// read tags
			if (MODULE_TAGGING) {
				$this->tagList->readObjects();
				$this->tags = $this->tagList->getObjects();
			}
		}

		// render stats
		if (ARTICLE_ENABLE_STATS) {
			$this->readStats();
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
			'articles' => ($this->articleList != null ? $this->articleList->getObjects() : array()),
			'tags' => ($this->articleList != null ? $this->articleList->getTags() : array()),
			'availableTags' => $this->tags,
			'tagID' => $this->tagID,
			'tag' => $this->tag,
			'categoryID' => $this->categoryID,
			'category' => $this->category,
			'categoryQuickJumpOptions' => Category::getCategorySelect('article'),
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
		if (MODULE_ARTICLE != 1) {
			throw new IllegalLinkException();
		}

		// set active page menu item
		PageMenu::setActiveMenuItem('wsip.header.menu.article');

		// init publication type
		if ($this->category == null) {
			require_once(WSIP_DIR.'lib/data/publication/Publication.class.php');
			Publication::initPublicationType('article');
		}

		parent::show();
	}
}
?>