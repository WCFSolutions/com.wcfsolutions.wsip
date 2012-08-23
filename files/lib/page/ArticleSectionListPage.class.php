<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/Category.class.php');
require_once(WSIP_DIR.'lib/data/article/Article.class.php');
require_once(WSIP_DIR.'lib/data/article/section/ArticleSectionList.class.php');
require_once(WSIP_DIR.'lib/data/article/section/ViewableArticleSection.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Shows a list of all article sections.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	page
 * @category	Infinite Portal
 */
class ArticleSectionListPage extends AbstractPage {
	// system
	public $templateName = 'articleSectionList';

	/**
	 * True, if the list was sorted successfully.
	 *
	 * @var boolean
	 */
	public $successfullSorting = false;

	/**
	 * article id
	 *
	 * @var	integer
	 */
	public $articleID = 0;

	/**
	 * article object
	 *
	 * @var	Article
	 */
	public $article = null;

	/**
	 * category object
	 *
	 * @var	Category
	 */
	public $category = null;

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['successfullSorting'])) $this->successfullSorting = true;

		// get article
		if (isset($_REQUEST['articleID'])) $this->articleID = intval($_REQUEST['articleID']);
		$this->article = new Article($this->articleID);
		if (!$this->article->articleID) {
			throw new IllegalLinkException();
		}

		// get category
		$this->category = new Category($this->article->categoryID);
		$this->article->enter($this->category);
	}

	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		// get section list
		$this->sectionList = new ArticleSectionList($this->article->articleID);
		$this->sectionList->readSections();
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'metaDescription' => $this->article->teaser,
			'metaKeywords' => '',
			'articleID' => $this->articleID,
			'article' => $this->article,
			'category' => $this->category,
			'sections' => $this->sectionList->getSectionList(),
			'successfullSorting' => $this->successfullSorting,
			'allowSpidersToIndexThisPage' => true
		));
	}

	/**
	 * @see Page::show()
	 */
	public function show() {
		// check module
		if (MODULE_ARTICLE != 1) {
			throw new IllegalLinkException();
		}

		// set active menu item
		WSIPCore::getPageMenu()->setActiveMenuItem('wsip.header.menu.article');

		parent::show();
	}
}
?>