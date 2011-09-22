<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/Category.class.php');
require_once(WSIP_DIR.'lib/data/news/ViewableNewsEntry.class.php');
require_once(WSIP_DIR.'lib/data/publication/object/comment/PublicationObjectCommentList.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/rating/Rating.class.php');
require_once(WCF_DIR.'lib/data/socialBookmark/SocialBookmarks.class.php');
require_once(WCF_DIR.'lib/page/MultipleLinkPage.class.php');

/**
 * Shows a news entry.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	page
 * @category	Infinite Portal
 */
class NewsEntryPage extends MultipleLinkPage {
	// system
	public $templateName = 'newsEntry';
	
	/**
	 * entry id
	 * 
	 * @var	integer
	 */
	public $entryID = 0;
	
	/**
	 * entry object
	 * 
	 * @var	NewsEntry
	 */
	public $entry = null;
	
	/**
	 * list of comments
	 * 
	 * @var PublicationObjectCommentList
	 */
	public $commentList = null;
	
	/**
	 * comment id
	 * 
	 * @var	integer
	 */
	public $commentID = 0;
	
	/**
	 * comment object
	 * 
	 * @var	PublicationObjectComment
	 */
	public $comment = null;
	
	/**
	 * attachment list object
	 * 
	 * @var	MessageAttachmentList
	 */
	public $attachmentList = null;
	
	/**
	 * list of attachments
	 * 
	 * @var	array<Attachment>
	 */
	public $attachments = array();
	
	/**
	 * polls object
	 * 
	 * @var	Polls
	 */
	public $polls = null;
	
	/**
	 * list of tags
	 * 
	 * @var	array<Tag>
	 */
	public $tags = array();
	
	/**
	 * number of marked entries
	 * 
	 * @var	integer
	 */
	public $markedEntries = 0;
	
	/**
	 * rating object
	 * 
	 * @var	Rating
	 */
	public $rating = null;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get entry
		if (isset($_REQUEST['entryID'])) $this->entryID = intval($_REQUEST['entryID']);
		$this->entry = new ViewableNewsEntry($this->entryID);
		if (!$this->entry->entryID) {
			throw new IllegalLinkException();
		}
		
		// get category
		$this->category = new Category($this->entry->categoryID);
		$this->entry->enter($this->category);
		
		// init comments
		if (MODULE_COMMENT && NEWS_ENTRY_ENABLE_COMMENTS && $this->entry->enableComments) {
			// get comment
			if (isset($_REQUEST['commentID'])) $this->commentID = intval($_REQUEST['commentID']);
			if ($this->commentID != 0) {
				$this->comment = new PublicationObjectComment($this->commentID);
				if (!$this->comment->commentID || $this->comment->publicationObjectID != $this->entryID || $this->comment->publicationType != 'news') {
					throw new IllegalLinkException();
				}
				
				// check permissions
				if ($this->action == 'edit' && !$this->comment->isEditable($this->entry)) {
					throw new PermissionDeniedException();
				}
				
				// get page number
				$sql = "SELECT	COUNT(*) AS comments
					FROM 	wsip".WSIP_N."_publication_object_comment
					WHERE 	publicationObjectID = ".$this->entryID."
						AND publicationType = 'news'
						AND time >= ".$this->comment->time;
				$result = WCF::getDB()->getFirstRow($sql);
				$this->pageNo = intval(ceil($result['comments'] / $this->itemsPerPage));
			}
			
			// get comment list
			$this->commentList = new PublicationObjectCommentList();
			$this->commentList->sqlConditions .= "publication_object_comment.publicationObjectID = ".$this->entryID." AND publicationType = 'news'";
			$this->commentList->sqlOrderBy = 'publication_object_comment.time DESC';
		}
		
		// init poll
		if (MODULE_POLL == 1 && $this->entry->pollID) {
			require_once(WCF_DIR.'lib/data/message/poll/Polls.class.php');
			$this->polls = new Polls($this->entry->pollID, $this->category->getPermission('canVoteNewsPoll'));
		}
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// get marked entries
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedNewsEntries'])) {
			$this->markedEntries = count($sessionVars['markedNewsEntries']);
		}
		
		// read comments
		if ($this->commentList != null) {
			$this->commentList->sqlOffset = ($this->pageNo - 1) * $this->itemsPerPage;
			$this->commentList->sqlLimit = $this->itemsPerPage;
			$this->commentList->readObjects();
		}
		
		// read attachments
		if (MODULE_ATTACHMENT == 1 && $this->entry->attachments > 0) {
			require_once(WCF_DIR.'lib/data/attachment/MessageAttachmentList.class.php');
			$this->attachmentList = new MessageAttachmentList($this->entryID, 'newsEntry', '');
			$this->attachmentList->readObjects();
			$this->attachments = $this->attachmentList->getSortedAttachments($this->category->getPermission('canViewNewsAttachmentPreview'));
			
			// set embedded attachments
			if ($this->category->getPermission('canViewNewsAttachmentPreview')) {
				require_once(WCF_DIR.'lib/data/message/bbcode/AttachmentBBCode.class.php');
				AttachmentBBCode::setAttachments($this->attachments);
			}
			
			// remove embedded attachments from list
			if (count($this->attachments) > 0) {
				MessageAttachmentList::removeEmbeddedAttachments($this->attachments);
			}
		}
		
		// get tags
		if (MODULE_TAGGING) {
			$this->tags = $this->entry->getTags(WCF::getSession()->getVisibleLanguageIDArray());
		}
		
		// init rating
		if (NEWS_ENTRY_ENABLE_RATING) {
			$this->rating = new Rating('com.wcfsolutions.wsip.news.entry', $this->entryID, PACKAGE_ID);
		}
		
		// update views
		if (!WCF::getSession()->spiderID) {
			$this->updateViews();
		}
	}
	
	/**
	 * @see MultipleLinkPage::countItems()
	 */
	public function countItems() {
		parent::countItems();
		
		if ($this->commentList == null) return 0;
		return $this->commentList->countObjects();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// init comment form
		if (MODULE_COMMENT && NEWS_ENTRY_ENABLE_COMMENTS && $this->entry->enableComments && $this->entry->isCommentable()) {
			if ($this->action == 'edit') {
				require_once(WSIP_DIR.'lib/form/PublicationObjectCommentEditForm.class.php');
				new PublicationObjectCommentEditForm($this->comment);
			}
			else {
				require_once(WSIP_DIR.'lib/form/PublicationObjectCommentAddForm.class.php');
				new PublicationObjectCommentAddForm(Publication::getPublicationTypeObject('news'), $this->entry->getEditor());
			}
		}
		
		WCF::getTPL()->assign(array(
			'templateName' => $this->templateName,
			'metaDescription' => $this->entry->teaser,
			'metaKeywords' => TaggingUtil::buildString($this->tags, ','),
			'category' => $this->category,
			'entry' => $this->entry,
			'entryID' => $this->entryID,
			'comments' => ($this->commentList != null ? $this->commentList->getObjects() : array()),
			'commentID' => $this->commentID,
			'tags' => $this->tags,
			'permissions' => $this->category->getModeratorPermissions(),
			'markedEntries' => $this->markedEntries,
			'url' => '',
			'attachments' => $this->attachments,
			'polls' => $this->polls,
			'rating' => $this->rating,
			'socialBookmarks' => SocialBookmarks::getInstance()->getSocialBookmarks(PAGE_URL.'/'.$this->entry->getURL(), $this->entry->subject),
			'allowSpidersToIndexThisPage' => true
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
		
		// set active menu item
		WSIPCore::getPageMenu()->setActiveMenuItem('wsip.header.menu.news');
		
		parent::show();
	}
	
	/**
	 * Updates the views of this entry.
	 */
	public function updateViews() {
		$sql = "UPDATE	wsip".WSIP_N."_news_entry
			SET	views = views + 1
			WHERE	entryID = ".$this->entryID;
		WCF::getDB()->registerShutdownUpdate($sql);
	}
}
?>