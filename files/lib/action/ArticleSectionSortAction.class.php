<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/CategoryEditor.class.php');
require_once(WSIP_DIR.'lib/data/article/ArticleEditor.class.php');
require_once(WSIP_DIR.'lib/data/article/section/ArticleSectionEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');

/**
 * Sorts the structure of article sections.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	action
 * @category	Infinite Portal
 */
class ArticleSectionSortAction extends AbstractSecureAction {
	/**
	 * article id
	 * 
	 * @var	integer
	 */
	public $articleID = 0;
	
	/**
	 * article editor object
	 * 
	 * @var	ArticleEditor
	 */
	public $article = null;
	
	/**
	 * category object
	 * 
	 * @var	Category
	 */
	public $category = null;
	
	/**
	 * new positions
	 * 
	 * @var	array
	 */
	public $positions = array();
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// check module
		if (MODULE_ARTICLE != 1) {
			throw new IllegalLinkException();
		}
		
		// get article
		if (isset($_REQUEST['articleID'])) $this->articleID = intval($_REQUEST['articleID']);
		$this->article = new ArticleEditor($this->articleID);
		if (!$this->article->articleID) {
			throw new IllegalLinkException();
		}
		
		// get category
		$this->category = new Category($this->article->categoryID);
		$this->article->enter($this->category);
		
		// get positions
		if (isset($_POST['articleSectionListPositions']) && is_array($_POST['articleSectionListPositions'])) $this->positions = ArrayUtil::toIntegerArray($_POST['articleSectionListPositions']);
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// check permissions
		if (!$this->article->isEditable($this->category)) {
			throw new PermissionDeniedException();
		}
		
		// update postions
		foreach ($this->positions as $sectionID => $data) {
			foreach ($data as $parentSectionID => $position) {
				ArticleSectionEditor::updateShowOrder(intval($sectionID), intval($parentSectionID), $position);
			}
		}
		
		// update article
		$newFirstSectionID = $this->article->refreshFirstSectionID();
		if ($newFirstSectionID != $this->article->firstSectionID) {
			$section = new ArticleSection($newFirstSectionID);
			$this->article->update($this->article->categoryID, $this->article->languageID, $section->subject, $this->article->teaser, $this->article->enableComments);
			
			// reset box tab cache
			require_once(WCF_DIR.'lib/data/box/tab/BoxTab.class.php');
			BoxTab::resetBoxTabCacheByBoxTabType('articles');
		}
		$this->executed();
		
		// forward to list page
		HeaderUtil::redirect('index.php?page=ArticleSectionList&articleID='.$this->articleID.'&successfullSorting=1'.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>