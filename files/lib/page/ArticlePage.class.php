<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/category/Category.class.php');
require_once(WSIP_DIR.'lib/data/article/Article.class.php');
require_once(WSIP_DIR.'lib/data/article/section/ArticleSectionList.class.php');
require_once(WSIP_DIR.'lib/data/article/section/ViewableArticleSection.class.php');
require_once(WSIP_DIR.'lib/data/publication/object/comment/PublicationObjectCommentList.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/rating/Rating.class.php');
require_once(WCF_DIR.'lib/data/socialBookmark/SocialBookmarks.class.php');
require_once(WCF_DIR.'lib/page/MultipleLinkPage.class.php');

/**
 * Shows an article section.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	page
 * @category	Infinite Portal
 */
class ArticlePage extends MultipleLinkPage {
	// system
	public $templateName = 'article';
	
	/**
	 * section id
	 * 
	 * @var	integer
	 */
	public $sectionID = 0;
	
	/**
	 * section object
	 * 
	 * @var	ViewableArticleSection
	 */
	public $section = null;
	
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
	 * list of tags
	 * 
	 * @var	array<Tag>
	 */
	public $tags = array();
	
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

		// get section
		if (isset($_REQUEST['sectionID'])) $this->sectionID = intval($_REQUEST['sectionID']);
		$this->section = new ViewableArticleSection($this->sectionID);
		if (!$this->section->sectionID) {
			throw new IllegalLinkException();
		}
		
		// get article
		$this->article = new Article($this->section->articleID);
		
		// get category
		$this->category = new Category($this->article->categoryID);
		$this->article->enter($this->category);
		
		// init comments
		if (MODULE_COMMENT && ARTICLE_ENABLE_COMMENTS && $this->article->enableComments) {
			// get comment
			if (isset($_REQUEST['commentID'])) $this->commentID = intval($_REQUEST['commentID']);
			if ($this->commentID != 0) {
				$this->comment = new PublicationObjectComment($this->commentID);
				if (!$this->comment->commentID || $this->comment->publicationObjectID != $this->article->articleID || $this->comment->publicationType != 'article') {
					throw new IllegalLinkException();
				}
				
				// check permissions
				if ($this->action == 'edit' && !$this->comment->isEditable($this->article)) {
					throw new PermissionDeniedException();
				}
				
				// get page number
				$sql = "SELECT	COUNT(*) AS comments
					FROM 	wsip".WSIP_N."_publication_object_comment
					WHERE 	publicationObjectID = ".$this->article->articleID."
						AND publicationType = 'article'
						AND time >= ".$this->comment->time;
				$result = WCF::getDB()->getFirstRow($sql);
				$this->pageNo = intval(ceil($result['comments'] / $this->itemsPerPage));
			}
			
			// get comment list
			$this->commentList = new PublicationObjectCommentList();
			$this->commentList->sqlConditions .= "publication_object_comment.publicationObjectID = ".$this->article->articleID." AND publicationType = 'article'";
			$this->commentList->sqlOrderBy = 'publication_object_comment.time DESC';
		}
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// get section list
		$this->sectionList = new ArticleSectionList($this->article->articleID);
		$this->sectionList->readSections();
		
		// read comments
		if ($this->commentList != null) {
			$this->commentList->sqlOffset = ($this->pageNo - 1) * $this->itemsPerPage;
			$this->commentList->sqlLimit = $this->itemsPerPage;
			$this->commentList->readObjects();
		}
		
		// read attachments
		if (MODULE_ATTACHMENT == 1 && $this->section->attachments > 0) {
			require_once(WCF_DIR.'lib/data/attachment/MessageAttachmentList.class.php');
			$this->attachmentList = new MessageAttachmentList($this->sectionID, 'articleSection', '');
			$this->attachmentList->readObjects();
			$this->attachments = $this->attachmentList->getSortedAttachments($this->category->getPermission('canViewArticleSectionAttachmentPreview'));
			
			// set embedded attachments
			if ($this->category->getPermission('canViewArticleSectionAttachmentPreview')) {
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
			$this->tags = $this->article->getTags(WCF::getSession()->getVisibleLanguageIDArray());
		}
		
		// init rating
		if (ARTICLE_ENABLE_RATING) {
			$this->rating = new Rating('com.wcfsolutions.wsip.article', $this->article->articleID, PACKAGE_ID);
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
		if (MODULE_COMMENT && ARTICLE_ENABLE_COMMENTS && $this->article->enableComments && $this->article->isCommentable()) {
			if ($this->action == 'edit') {
				require_once(WSIP_DIR.'lib/form/PublicationObjectCommentEditForm.class.php');
				new PublicationObjectCommentEditForm($this->comment);
			}
			else {
				require_once(WSIP_DIR.'lib/form/PublicationObjectCommentAddForm.class.php');
				new PublicationObjectCommentAddForm(Publication::getPublicationTypeObject('article'), $this->article->getEditor());
			}
		}

		WCF::getTPL()->assign(array(
			'templateName' => $this->templateName,
			'metaDescription' => $this->article->teaser,
			'metaKeywords' => TaggingUtil::buildString($this->tags, ','),
			'category' => $this->category,
			'article' => $this->article,
			'section' => $this->section,
			'sectionID' => $this->sectionID,
			'sections' => $this->sectionList->getSectionList(),
			'comments' => ($this->commentList != null ? $this->commentList->getObjects() : array()),
			'commentID' => $this->commentID,
			'tags' => $this->tags,
			'attachments' => $this->attachments,
			'rating' => $this->rating,
			'socialBookmarks' => SocialBookmarks::getInstance()->getSocialBookmarks(PAGE_URL.'/'.$this->article->getURL(), $this->article->subject),
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
	
	/**
	 * Updates the views of this article.
	 */
	public function updateViews() {
		$sql = "UPDATE	wsip".WSIP_N."_article
			SET	views = views + 1
			WHERE	articleID = ".$this->article->articleID;
		WCF::getDB()->registerShutdownUpdate($sql);
	}
}
?>