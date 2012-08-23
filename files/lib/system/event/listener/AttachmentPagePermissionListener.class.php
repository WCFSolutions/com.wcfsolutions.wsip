<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Checks the download permission for attachments.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	system.event.listener
 * @category	Infinite Portal
 */
class AttachmentPagePermissionListener implements EventListener {
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		$attachment = $eventObj->attachment;
		if ($attachment['containerID']) {
			if ($attachment['containerType'] == 'newsEntry') {
				// get news entry
				require_once(WSIP_DIR.'lib/data/news/NewsEntry.class.php');
				$entry = new NewsEntry($attachment['containerID']);

				// get category
				require_once(WSIP_DIR.'lib/data/category/Category.class.php');
				$category = new Category($entry->categoryID);
				$entry->enter($category);

				// check download permission
				if (!$category->getPermission('canDownloadNewsAttachment') && (!$eventObj->thumbnail || !$category->getPermission('canViewNewsAttachmentPreview'))) {
					throw new PermissionDeniedException();
				}
			}
			else if ($attachment['containerType'] == 'articleSection') {
				// get article section
				require_once(WSIP_DIR.'lib/data/article/section/ArticleSection.class.php');
				$section = new ArticleSection($attachment['containerID']);

				// get article
				require_once(WSIP_DIR.'lib/data/article/Article.class.php');
				$article = new Article($section->articleID);

				// get category
				require_once(WSIP_DIR.'lib/data/category/Category.class.php');
				$category = new Category($article->categoryID);
				$article->enter($category);

				// check download permission
				if (!$category->getPermission('canDownloadArticleSectionAttachment') && (!$eventObj->thumbnail || !$category->getPermission('canViewArticleSectionAttachmentPreview'))) {
					throw new PermissionDeniedException();
				}
			}
		}
	}
}
?>