<?php
// wsip imports
require_once(WSIP_DIR.'lib/form/ArticleAddForm.class.php');

/**
 * Shows the article section add form.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	form
 * @category	Infinite Portal
 */
class ArticleSectionAddForm extends ArticleAddForm {	
	// system
	public $templateName = 'articleSectionAdd';
	
	/**
	 * list of available parent sections
	 * 
	 * @var	array
	 */
	public $sectionOptions = array();
	
	// parameters
	public $parentSectionID = 0;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		MessageForm::readParameters();
		
		// get article
		if (isset($_REQUEST['articleID'])) $this->articleID = intval($_REQUEST['articleID']);
		$this->article = new ArticleEditor($this->articleID);
		if (!$this->article->articleID) {
			throw new IllegalLinkException();
		}
		
		// get category
		$this->category = new CategoryEditor($this->article->categoryID);
		$this->article->enter($this->category);
		
		// check permission
		if (!$this->article->isEditable($this->category)) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['parentSectionID'])) $this->parentSectionID = intval($_POST['parentSectionID']);
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		MessageForm::save();
		
		// update section
		$this->section = ArticleSectionEditor::create($this->parentSectionID, $this->articleID, $this->subject, $this->text, $this->getOptions(), $this->attachmentListEditor);
		$this->saved();
		
		// forward to article section
		HeaderUtil::redirect('index.php?page=Article&sectionID='.$this->section->sectionID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
	
	/**
	 * Does nothing.
	 */
	protected function validateTeaser() {}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// get section options
		$this->sectionOptions = ArticleSection::getSectionSelect($this->article->articleID);
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'action' => 'add',
			'articleID' =>  $this->articleID,
			'article' => $this->article,
			'parentSectionID' => $this->parentSectionID,
			'sectionOptions' => $this->sectionOptions
		));
	}
}
?>