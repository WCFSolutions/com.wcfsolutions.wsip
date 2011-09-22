<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/CategoryEditor.class.php');
require_once(WSIP_DIR.'lib/data/article/ArticleEditor.class.php');
require_once(WSIP_DIR.'lib/data/article/section/ArticleSectionEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');

/**
 * Provides default implementations for article section actions.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	action
 * @category	Infinite Portal
 */
abstract class AbstractArticleSectionAction extends AbstractSecureAction {
	/**
	 * section id
	 * 
	 * @var	integer
	 */
	public $sectionID = 0;
	
	/**
	 * section editor object
	 * 
	 * @var	ArticleSectionEditor
	 */
	public $section = null;
	
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
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// check module
		if (MODULE_ARTICLE != 1) {
			throw new IllegalLinkException();
		}
		
		// get section
		if (isset($_REQUEST['sectionID'])) $this->sectionID = intval($_REQUEST['sectionID']);
		$this->section = new ArticleSectionEditor($this->sectionID);
		if (!$this->section->sectionID) {
			throw new IllegalLinkException();
		}
		
		// get article
		$this->article = new ArticleEditor($this->section->articleID);
		
		// get category
		$this->category = new CategoryEditor($this->article->categoryID);
		$this->article->enter($this->category);
	}
}
?>