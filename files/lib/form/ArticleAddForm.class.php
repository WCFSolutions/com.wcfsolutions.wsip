<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/article/ArticleEditor.class.php');
require_once(WSIP_DIR.'lib/data/article/section/ArticleSectionEditor.class.php');
require_once(WSIP_DIR.'lib/data/category/CategoryEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/attachment/MessageAttachmentListEditor.class.php');
require_once(WCF_DIR.'lib/data/box/Box.class.php');
require_once(WCF_DIR.'lib/form/MessageForm.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
require_once(WCF_DIR.'lib/system/language/Language.class.php');

/**
 * Shows the article add form.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	form
 * @category	Infinite Portal
 */
class ArticleAddForm extends MessageForm {
	// system
	public $templateName = 'articleAdd';
	public $useCaptcha = true;
	public $showPoll = false;
	public $showSignatureSetting = false;

	/**
	 * category id
	 *
	 * @var	integer
	 */
	public $categoryID = 0;

	/**
	 * category editor object
	 *
	 * @var	CategoryEditor
	 */
	public $category = null;

	/**
	 * attachment list editor object
	 *
	 * @var	MessageAttachmentListEditor
	 */
	public $attachmentListEditor = null;

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
	 * list of available languages
	 *
	 * @var	array
	 */
	public $availableLanguages = array();

	// form parameters
	public $username = '';
	public $teaser = '';
	public $preview, $send;
	public $languageID = 0;
	public $tags = '';
	public $enableComments = 1;

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// get category
		if (isset($_REQUEST['categoryID'])) {
			$this->categoryID = intval($_REQUEST['categoryID']);
			$this->category = new CategoryEditor($this->categoryID);
			$this->category->enter('article');

			// check permission
			if (!$this->category->getPermission('canAddArticle')) {
				throw new PermissionDeniedException();
			}
		}

		// flood control
		$this->messageTable = "wsip".WSIP_N."_article";
	}

	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		$this->enableComments = 0;

		if (isset($_POST['username'])) $this->username = StringUtil::trim($_POST['username']);
		if (isset($_POST['teaser'])) $this->teaser = StringUtil::trim($_POST['teaser']);
		if (isset($_POST['preview'])) $this->preview = (boolean) $_POST['preview'];
		if (isset($_POST['send'])) $this->send = (boolean) $_POST['send'];
		if (isset($_POST['languageID'])) $this->languageID = intval($_POST['languageID']);
		if (isset($_POST['tags'])) $this->tags = StringUtil::trim($_POST['tags']);
		if (isset($_POST['enableComments'])) $this->enableComments = intval($_POST['enableComments']);
	}

	/**
	 * @see Form::submit()
	 */
	public function submit() {
		// call submit event
		EventHandler::fireAction($this, 'submit');

		$this->readFormParameters();

		try {
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
			// send message
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
	 * @see Form::validate()
	 */
	public function validate() {
		parent::validate();

		// username
		$this->validateUsername();

		// teaser
		$this->validateTeaser();

		// language
		$this->validateLanguage();
	}

	/**
	 * Validates the language.
	 */
	protected function validateLanguage() {
		// language
		$availableLanguages = Language::getAvailableContentLanguages(PACKAGE_ID);
		if (count($availableLanguages) > 0) {
			if (!isset($availableLanguages[$this->languageID])) {
				$this->languageID = WCF::getLanguage()->getLanguageID();
				if (!isset($availableLanguages[$this->languageID])) {
					$languageIDs = array_keys($availableLanguages);
					$this->languageID = array_shift($languageIDs);
				}
			}
		}
		else {
			$this->languageID = 0;
		}
	}

	/**
	 * Validates the username.
	 */
	protected function validateUsername() {
		// only for guests
		if (WCF::getUser()->userID == 0) {
			// username
			if (empty($this->username)) {
				throw new UserInputException('username');
			}
			if (!UserUtil::isValidUsername($this->username)) {
				throw new UserInputException('username', 'notValid');
			}
			if (!UserUtil::isAvailableUsername($this->username)) {
				throw new UserInputException('username', 'notAvailable');
			}

			WCF::getSession()->setUsername($this->username);
		}
		else {
			$this->username = WCF::getUser()->username;
		}
	}

	/**
	 * Validates the teaser.
	 */
	protected function validateTeaser() {
		if (empty($this->teaser)) {
			throw new UserInputException('teaser');
		}

		// check teaser length
		if (StringUtil::length($this->teaser) > 255) {
			throw new UserInputException('teaser', 'tooLong');
		}
	}

	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();

		// save article
		list($this->article, $this->section) = ArticleEditor::create($this->categoryID, $this->languageID, $this->subject, $this->text, $this->teaser, WCF::getUser()->userID, $this->username, $this->enableComments, $this->getOptions(), $this->attachmentListEditor);

		// save tags
		if (MODULE_TAGGING && ARTICLE_ENABLE_TAGS && $this->category->getPermission('canSetArticleTags')) {
			$tagArray = TaggingUtil::splitString($this->tags);
			if (count($tagArray)) $this->article->updateTags($tagArray);
		}

		// refresh counter
		$this->category->updateArticles(1); // maybe use $category->refresh() here..

		// reset stat cache
		WCF::getCache()->clearResource('stat');
		WCF::getCache()->clearResource('categoryData');

		// reset box tab cache
		BoxTab::resetBoxTabCacheByBoxTabType('articles');
		$this->saved();

		// forward to article
		HeaderUtil::redirect('index.php?page=Article&sectionID='.$this->section->sectionID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}

	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		// get username
		if (!count($_POST)) {
			$this->username = WCF::getSession()->username;
		}
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'username' => $this->username,
			'teaser' => $this->teaser,
			'categoryID' => $this->categoryID,
			'category' => $this->category,
			'languageID' => $this->languageID,
			'availableLanguages' => $this->availableLanguages,
			'tags' => $this->tags,
			'enableComments' => $this->enableComments
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

		// set active page menu item
		PageMenu::setActiveMenuItem('wsip.header.menu.article');

		// show category select
		if ($this->category == null) {
			// check permission
			WCF::getUser()->checkPermission('user.portal.canAddArticle');

			// assign variables
			WCF::getTPL()->assign(array(
				'categoryOptions' => Category::getCategorySelect('article', array('canViewCategory', 'canEnterCategory', 'canAddArticle'))
			));
			WCF::getTPL()->display('articleAddCategorySelect');
			exit;
		}

		// load available languages
		$this->loadAvailableLanguages();

		if (MODULE_ATTACHMENT != 1 || !$this->category->getPermission('canUploadArticleSectionAttachment')) {
			$this->showAttachments = false;
		}

		// get attachments editor
		if ($this->attachmentListEditor == null) {
			$this->attachmentListEditor = new MessageAttachmentListEditor(array(), 'articleSection', PACKAGE_ID, WCF::getUser()->getPermission('user.portal.maxArticleSectionAttachmentSize'), WCF::getUser()->getPermission('user.portal.allowedArticleSectionAttachmentExtensions'), WCF::getUser()->getPermission('user.portal.maxArticleSectionAttachmentCount'));
		}

		// show form
		parent::show();
	}

	/**
	 * Gets the available content languages.
	 */
	protected function loadAvailableLanguages() {
		if ($this->languageID == 0) $this->languageID = WCF::getLanguage()->getLanguageID();
		$this->availableLanguages = $this->getAvailableLanguages();

		if (!isset($this->availableLanguages[$this->languageID]) && count($this->availableLanguages) > 0) {
			$languageIDs = array_keys($this->availableLanguages);
			$this->languageID = array_shift($languageIDs);
		}
	}

	/**
	 * Returns a list of available languages.
	 *
	 * @return	array
	 */
	protected function getAvailableLanguages() {
		$visibleLanguages = explode(',', WCF::getUser()->languageIDs);
		$availableLanguages = Language::getAvailableContentLanguages(PACKAGE_ID);
		foreach ($availableLanguages as $key => $language) {
			if (!in_array($language['languageID'], $visibleLanguages)) {
				unset($availableLanguages[$key]);
			}
		}

		return $availableLanguages;
	}
}
?>