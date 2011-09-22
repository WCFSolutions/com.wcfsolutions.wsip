<?php
// wsip imports
require_once(WSIP_DIR.'lib/form/ArticleAddForm.class.php');

/**
 * Shows the article section edit form.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	form
 * @category	Infinite Portal
 */
class ArticleSectionEditForm extends ArticleAddForm {
	// system
	public $templateName = 'articleSectionAdd';
	
	/**
	 * section id
	 * 
	 * @var	integer
	 */
	public $sectionID = 0;
	
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
		
		if (isset($_POST['categoryID'])) $this->categoryID = intval($_POST['categoryID']);
		if (isset($_POST['parentSectionID'])) $this->parentSectionID = intval($_POST['parentSectionID']);
	}
	
	/**
	 * @see Form::submit()
	 */
	public function submit() {
		// call submit event
		EventHandler::fireAction($this, 'submit');
		
		$this->readFormParameters();
		
		try {
			if (isset($_POST['moveGallery']) && $this->article->firstSectionID == $this->section->sectionID) {
				// validate new category
				try {
					$newCategory = Category::getCategory($this->categoryID);
				}
				catch (IllegalLinkException $e) {
					throw new UserInputException('categoryID', 'invalid');
				}
				
				if (!$newCategory->isAvailablePublicationType('article')) {
					throw new UserInputException('categoryID', 'invalid');
				}
				
				if ($newCategory->categoryID == $this->category->categoryID) {
					throw new UserInputException('categoryID', 'invalid');
				}
				
				// move article
				$this->article->moveTo($this->categoryID);
				
				// reset stat cache
				WCF::getCache()->clearResource('stat');
				WCF::getCache()->clearResource('categoryData');
				
				// reset box cache
				Box::resetBoxCacheByBoxType('lastArticles');
				
				// forward
				HeaderUtil::redirect('index.php?page=Article&sectionID='.$this->sectionID.SID_ARG_2ND_NOT_ENCODED);
				exit;
			}
			
			// attachment handling
			if ($this->showAttachments) {
				$this->attachmentListEditor->handleRequest();
			}
				
			// preview
			if ($this->preview) {
				require_once(WCF_DIR.'lib/data/message/bbcode/AttachmentBBCode.class.php');
				AttachmentBBCode::setAttachments($this->attachmentListEditor->getSortedAttachments());
				WCF::getTPL()->assign('preview', ArticleSectionEditor::createPreview($this->subject, $this->text, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes));
			}
			
			// send message or save as draft
			if ($this->send) {
				$this->validate();
				// no errors
				$this->save();
			}
		}
		catch (UserInputException $e) {
			$this->errorField = $e->getField();
			$this->errorType = $e->getType();
		}
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		MessageForm::save();
		
		// update section
		$this->section->update($this->parentSectionID, $this->subject, $this->text, $this->getOptions(), $this->attachmentListEditor);
		
		// update article
		if ($this->article->firstSectionID == $this->section->sectionID) {
			$this->article->update($this->article->categoryID, $this->languageID, $this->subject, $this->teaser, $this->enableComments);
			
			// save tags
			if (MODULE_TAGGING && ARTICLE_ENABLE_TAGS && $this->category->getPermission('canSetArticleTags')) {
				$this->article->updateTags(TaggingUtil::splitString($this->tags));
			}
			
			// reset box tab cache
			BoxTab::resetBoxTabCacheByBoxTabType('articles');
		}
		$this->saved();
		
		// forward to article section
		HeaderUtil::redirect('index.php?page=Article&sectionID='.$this->sectionID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
	
	/**
	 * @see ArticleAddForm::validateTeaser()
	 */
	protected function validateTeaser() {
		if ($this->article->firstSectionID == $this->section->sectionID) {
			parent::validateTeaser();
		}
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		if (!count($_POST)) {
			$this->parentSectionID = $this->section->parentSectionID;
			$this->subject = $this->section->subject;
			$this->text = $this->section->message;
			$this->teaser = $this->article->teaser;
			$this->languageID = $this->article->languageID;			
			$this->enableSmilies =  $this->section->enableSmilies;
			$this->enableHtml = $this->section->enableHtml;
			$this->enableBBCodes = $this->section->enableBBCodes;
			$this->enableComments = $this->article->enableComments;
			
			// tags
			if (ARTICLE_ENABLE_TAGS && $this->article->firstSectionID == $this->sectionID) {
				$this->tags = TaggingUtil::buildString($this->article->getTags(array($this->languageID)));
			}
		}
		
		$this->sectionOptions = ArticleSection::getSectionSelect($this->article->articleID, array($this->sectionID));
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'action' => 'edit',
			'categoryOptions' => Category::getCategorySelect('article', array('canViewCategory', 'canEnterCategory')),
			'sectionID' =>  $this->sectionID,
			'section' => $this->section,
			'article' => $this->article,
			'parentSectionID' => $this->parentSectionID,
			'sectionOptions' => $this->sectionOptions
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {		
		$this->attachmentListEditor = new MessageAttachmentListEditor(array($this->sectionID), 'articleSection', PACKAGE_ID, WCF::getUser()->getPermission('user.portal.maxArticleSectionAttachmentSize'), WCF::getUser()->getPermission('user.portal.allowedArticleSectionAttachmentExtensions'),  WCF::getUser()->getPermission('user.portal.maxArticleSectionAttachmentCount'));
		
		parent::show();
	}
	
	/**
	 * @see ArticleAddForm::getAvailableLanguages()
	 */
	protected function getAvailableLanguages() {
		$visibleLanguages = explode(',', WCF::getUser()->languageIDs);
		$availableLanguages = Language::getAvailableContentLanguages(PACKAGE_ID);
		foreach ($availableLanguages as $key => $language) {
			if (!in_array($language['languageID'], $visibleLanguages) && !$this->category->getModeratorPermission('canEditArticle')) {
				unset($availableLanguages[$key]);
			}
		}
		
		return $availableLanguages;
	}
}
?>