<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/CategoryEditor.class.php');
require_once(WSIP_DIR.'lib/data/news/NewsEntryEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/attachment/MessageAttachmentListEditor.class.php');
require_once(WCF_DIR.'lib/data/box/Box.class.php');
require_once(WCF_DIR.'lib/data/message/poll/PollEditor.class.php');
require_once(WCF_DIR.'lib/form/MessageForm.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
require_once(WCF_DIR.'lib/system/language/Language.class.php');

/**
 * Shows the news entry add form.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	form
 * @category	Infinite Portal
 */
class NewsEntryAddForm extends MessageForm {
	// system
	public $templateName = 'newsEntryAdd';
	public $useCaptcha = NEWS_ENTRY_ADD_USE_CAPTCHA;
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
	 * poll editor object
	 *
	 * @var	PollEditor
	 */
	public $pollEditor = null;

	/**
	 * entry editor object
	 *
	 * @var	NewsEntryEditor
	 */
	public $entry = null;

	/**
	 * list of available languages
	 *
	 * @var	array
	 */
	public $availableLanguages = array();

	/**
	 * publishing time
	 *
	 * @var	integer
	 */
	public $publishingTime = 0;

	// form parameters
	public $username = '';
	public $teaser = '';
	public $preview, $send;
	public $languageID = 0;
	public $tags = '';
	public $publishingTimeDay = '';
	public $publishingTimeMonth = '';
	public $publishingTimeYear = '';
	public $publishingTimeHour = '';
	public $disableEntry = 0;
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
			$this->category->enter('news');

			// check permission
			if (!$this->category->getPermission('canAddNewsEntry')) {
				throw new PermissionDeniedException();
			}
		}

		// flood control
		$this->messageTable = "wsip".WSIP_N."_news_entry";
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
		if ($this->category->getModeratorPermission('canEnableNewsEntry')) {
			if (isset($_POST['publishingTimeDay'])) $this->publishingTimeDay = intval($_POST['publishingTimeDay']);
			if (isset($_POST['publishingTimeMonth'])) $this->publishingTimeMonth = intval($_POST['publishingTimeMonth']);
			if (!empty($_POST['publishingTimeYear'])) $this->publishingTimeYear = intval($_POST['publishingTimeYear']);
			if (isset($_POST['publishingTimeHour'])) $this->publishingTimeHour = intval($_POST['publishingTimeHour']);
			if (isset($_POST['disableEntry'])) $this->disableEntry = intval($_POST['disableEntry']);
		}
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

			// poll handling
			if ($this->showPoll) {
				$this->pollEditor->readParams();
			}

			// preview
			if ($this->preview) {
				require_once(WCF_DIR.'lib/data/message/bbcode/AttachmentBBCode.class.php');
				AttachmentBBCode::setAttachments($this->attachmentListEditor->getSortedAttachments());
				WCF::getTPL()->assign('preview', NewsEntryEditor::createPreview($this->subject, $this->text, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes));
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

		// publishing time
		$this->validatePublishingTime();

		// poll
		if ($this->showPoll) $this->pollEditor->checkParams();
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
	 * Validates the publishing time.
	 */
	protected function validatePublishingTime() {
		if ($this->publishingTimeDay || $this->publishingTimeMonth || $this->publishingTimeYear || $this->publishingTimeHour) {
			$time = @gmmktime($this->publishingTimeHour, 0, 0, $this->publishingTimeMonth, $this->publishingTimeDay, $this->publishingTimeYear);
			// since php5.1.0 mktime returns false on failure
			if ($time === false || $time === -1) {
				throw new UserInputException('publishingTime', 'invalid');
			}

			// get utc time
			$time = DateUtil::getUTC($time);
			if ($time <= TIME_NOW) {
				throw new UserInputException('publishingTime', 'invalid');
			}

			$this->publishingTime = $time;
			$this->disableEntry = 1;
		}
	}

	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();

		// save poll
		if ($this->showPoll) {
			$this->pollEditor->save();
		}

		// save entry
		$this->entry = NewsEntryEditor::create($this->categoryID, $this->languageID, $this->subject, $this->text, $this->teaser, WCF::getUser()->userID, $this->username, $this->publishingTime, $this->enableComments, $this->getOptions(), $this->attachmentListEditor, $this->pollEditor, intval(($this->disableEntry || !$this->category->getPermission('canAddNewsEntryWithoutModeration'))));

		// save tags
		if (MODULE_TAGGING && NEWS_ENTRY_ENABLE_TAGS && $this->category->getPermission('canSetNewsTags')) {
			$tagArray = TaggingUtil::splitString($this->tags);
			if (count($tagArray)) $this->entry->updateTags($tagArray);
		}

		if (!$this->disableEntry && $this->category->getPermission('canAddNewsEntryWithoutModeration')) {
			// update user news entries
			if (WCF::getUser()->userID) {
				require_once(WSIP_DIR.'lib/data/user/WSIPUser.class.php');
				WSIPUser::updateUserNewsEntries(WCF::getUser()->userID, 1);
				if (ACTIVITY_POINTS_PER_NEWS_ENTRY) {
					require_once(WCF_DIR.'lib/data/user/rank/UserRank.class.php');
					UserRank::updateActivityPoints(ACTIVITY_POINTS_PER_NEWS_ENTRY);
				}
			}

			// refresh counter
			$this->category = new CategoryEditor($this->categoryID);
			$this->category->updateNewsEntries(1); // maybe use $category->refresh() here..

			// reset stat cache
			WCF::getCache()->clearResource('stat');
			WCF::getCache()->clearResource('categoryData');

			// reset box tab cache
			BoxTab::resetBoxTabCacheByBoxTabType('newsEntries');
			$this->saved();

			// forward to entry
			HeaderUtil::redirect('index.php?page=NewsEntry&entryID='.$this->entry->entryID.SID_ARG_2ND_NOT_ENCODED);
		}
		else {
			$this->saved();
			if ($this->disableEntry) {
				// forward to entry
				HeaderUtil::redirect('index.php?page=NewsEntry&entryID='.$this->entry->entryID.SID_ARG_2ND_NOT_ENCODED);
			}
			else {
				WCF::getTPL()->assign(array(
					'url' => 'index.php?page=NewsOverview&categoryID='.$this->categoryID.SID_ARG_2ND_NOT_ENCODED,
					'message' => WCF::getLanguage()->get('wsip.news.entry.add.moderation.redirect'),
					'wait' => 5
				));
				WCF::getTPL()->display('redirect');
			}
		}
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
			'action' => 'add',
			'username' => $this->username,
			'teaser' => $this->teaser,
			'categoryID' => $this->categoryID,
			'category' => $this->category,
			'languageID' => $this->languageID,
			'availableLanguages' => $this->availableLanguages,
			'tags' => $this->tags,
			'publishingTimeDay' => $this->publishingTimeDay,
			'publishingTimeMonth' => $this->publishingTimeMonth,
			'publishingTimeYear' => $this->publishingTimeYear,
			'publishingTimeHour' => $this->publishingTimeHour,
			'disableEntry' => $this->disableEntry,
			'enableComments' => $this->enableComments
		));
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

		// show category select
		if ($this->category == null) {
			// check permission
			WCF::getUser()->checkPermission('user.portal.canAddNewsEntry');

			// assign variables
			WCF::getTPL()->assign(array(
				'categoryOptions' => Category::getCategorySelect('news', array('canViewCategory', 'canEnterCategory', 'canAddNewsEntry'))
			));
			WCF::getTPL()->display('newsEntryAddCategorySelect');
			exit;
		}

		// load available languages
		$this->loadAvailableLanguages();

		if (MODULE_POLL != 1 || !$this->category->getPermission('canStartNewsPoll')) {
			$this->showPoll = false;
		}

		if (MODULE_ATTACHMENT != 1 || !$this->category->getPermission('canUploadNewsAttachment')) {
			$this->showAttachments = false;
		}

		// get attachments editor
		if ($this->attachmentListEditor == null) {
			$this->attachmentListEditor = new MessageAttachmentListEditor(array(), 'newsEntry', PACKAGE_ID, WCF::getUser()->getPermission('user.portal.maxNewsAttachmentSize'), WCF::getUser()->getPermission('user.portal.allowedNewsAttachmentExtensions'), WCF::getUser()->getPermission('user.portal.maxNewsAttachmentCount'));
		}

		// get poll editor
		if ($this->pollEditor == null) {
			$this->pollEditor = new PollEditor(0, 0, 'newsEntry', WCF::getUser()->getPermission('user.portal.canStartPublicNewsPoll'));
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