<?php
// wsip imports
require_once(WSIP_DIR.'lib/form/NewsEntryAddForm.class.php');

/**
 * Shows the news entry edit form.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	form
 * @category	Infinite Portal
 */
class NewsEntryEditForm extends NewsEntryAddForm {
	/**
	 * entry id
	 * 
	 * @var	integer
	 */
	public $entryID = 0;
	
	// parameters
	public $deleteReason = '';
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		MessageForm::readParameters();
		
		// get entry
		if (isset($_REQUEST['entryID'])) $this->entryID = intval($_REQUEST['entryID']);
		$this->entry = new NewsEntryEditor($this->entryID);
		if (!$this->entry->entryID) {
			throw new IllegalLinkException();
		}
		
		// get category
		$this->category = new CategoryEditor($this->entry->categoryID);
		$this->entry->enter($this->category);
		
		// check permission
		if (!$this->entry->isEditable($this->category)) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		if (isset($_POST['deleteReason'])) $this->deleteReason = StringUtil::trim($_POST['deleteReason']);
		if ($this->entry->everEnabled) {
			$this->publishingTimeDay = $this->publishingTimeMonth = $this->publishingTimeYear = $this->publishingTimeHour = '';
		}
	}
	
	/**
	 * @see Form::submit()
	 */
	public function submit() {
		parent::submit();
		
		try {
			if (isset($_POST['deleteEntry'])) {
				if (!$this->entry->isDeletable($this->category)) {
					throw new PermissionDeniedException();
				}
				
				if (isset($_POST['sure'])) {
					if (NEWS_ENTRY_ENABLE_RECYCLE_BIN && !$this->entry->isDeleted) {
						$this->entry->trash($this->deleteReason);
					}
					else {
						$this->entry->delete();
					}
					
					// refresh entries
					$this->category->refresh();
					
					// reset cache
					WCF::getCache()->clearResource('categoryData', true);
					WCF::getCache()->clearResource('stat');
					
					// reset box cache
					Box::resetBoxCacheByBoxType('lastNewsEntries');
					
					if ($this->entry->isDeleted) HeaderUtil::redirect('index.php?page=NewsOverview&categoryID='.$this->entry->categoryID.SID_ARG_2ND_NOT_ENCODED);
					else HeaderUtil::redirect('index.php?page=NewsEntry&entryID='.$this->entry->entryID.SID_ARG_2ND_NOT_ENCODED);
					exit;
				}
				else {
					throw new UserInputException('sure');
				}
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
		
		// save poll
		if ($this->showPoll) {
			$this->pollEditor->save();
		}
		
		// update entry
		$this->entry->update($this->entry->categoryID, $this->languageID, $this->subject, $this->text, $this->teaser, $this->publishingTime, $this->enableComments, $this->getOptions(), $this->attachmentListEditor, $this->pollEditor);

		// save tags
		if (MODULE_TAGGING && NEWS_ENTRY_ENABLE_TAGS && $this->category->getPermission('canSetNewsTags')) {
			$this->entry->updateTags(TaggingUtil::splitString($this->tags));
		}
		
		// reset box tab cache
		BoxTab::resetBoxTabCacheByBoxTabType('newsEntries');
		$this->saved();
		
		// forward to entry
		HeaderUtil::redirect('index.php?page=NewsEntry&entryID='.$this->entryID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		if (!count($_POST)) {
			$this->subject = $this->entry->subject;
			$this->text = $this->entry->message;
			$this->teaser = $this->entry->teaser;
			$this->languageID = $this->entry->languageID;			
			$this->enableSmilies =  $this->entry->enableSmilies;
			$this->enableHtml = $this->entry->enableHtml;
			$this->enableBBCodes = $this->entry->enableBBCodes;
			$this->enableComments = $this->entry->enableComments;
			
			// publishing time
			if ($this->entry->publishingTime) {
				$this->publishingTimeDay = intval(DateUtil::formatDate('%e', $this->entry->publishingTime, false, true));
				$this->publishingTimeMonth = intval(DateUtil::formatDate('%m', $this->entry->publishingTime, false, true));
				$this->publishingTimeYear = DateUtil::formatDate('%Y', $this->entry->publishingTime, false, true);
				$this->publishingTimeHour = DateUtil::formatDate('%H', $this->entry->publishingTime, false, true);
			}
			
			// tags
			if (NEWS_ENTRY_ENABLE_TAGS) {
				$this->tags = TaggingUtil::buildString($this->entry->getTags(array($this->languageID)));
			}
		}
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'action' => 'edit',
			'entryID' =>  $this->entryID,
			'entry' => $this->entry,
			'deleteReason' => $this->deleteReason
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {		
		$this->attachmentListEditor = new MessageAttachmentListEditor(array($this->entryID), 'newsEntry', PACKAGE_ID, WCF::getUser()->getPermission('user.portal.maxNewsAttachmentSize'), WCF::getUser()->getPermission('user.portal.allowedNewsAttachmentExtensions'),  WCF::getUser()->getPermission('user.portal.maxNewsAttachmentCount'));
		$this->pollEditor = new PollEditor($this->entry->pollID, 0, 'newsEntry', WCF::getUser()->getPermission('user.portal.canStartPublicNewsPoll'));
		
		parent::show();
	}
	
	/**
	 * @see NewsEntryAddForm::getAvailableLanguages()
	 */
	protected function getAvailableLanguages() {
		$visibleLanguages = explode(',', WCF::getUser()->languageIDs);
		$availableLanguages = Language::getAvailableContentLanguages(PACKAGE_ID);
		foreach ($availableLanguages as $key => $language) {
			if (!in_array($language['languageID'], $visibleLanguages) && !$this->category->getModeratorPermission('canEditNewsEntry')) {
				unset($availableLanguages[$key]);
			}
		}
		
		return $availableLanguages;
	}
}
?>