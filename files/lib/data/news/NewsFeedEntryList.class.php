<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/news/NewsFeedEntry.class.php');
require_once(WSIP_DIR.'lib/data/news/NewsEntryList.class.php');

/**
 * Represents a list of viewable news entries in a rss or an atom feed.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.news
 * @category	Infinite Portal
 */
class NewsFeedEntryList extends NewsEntryList {
	/**
	 * @see DatabaseObjectList::readObjects()
	 */
	public function readObjects() {
		// read objects
		$attachmentEntryIDArray = array();
		$sql = "SELECT		".(!empty($this->sqlSelects) ? $this->sqlSelects.',' : '')."
					news_entry.*
			FROM		wsip".WSIP_N."_news_entry news_entry
			".$this->sqlJoins."
			".(!empty($this->sqlConditions) ? "WHERE ".$this->sqlConditions : '')."
			".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
		$result = WCF::getDB()->sendQuery($sql, $this->sqlLimit, $this->sqlOffset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->entries[] = new NewsFeedEntry(null, $row);

			// attachments
			if ($row['attachments'] != 0) {
				$attachmentEntryIDArray[] = $row['entryID'];
			}
		}

		// read attachments
		if (MODULE_ATTACHMENT == 1 && count($attachmentEntryIDArray) > 0 && (WCF::getUser()->getPermission('user.portal.canViewNewsAttachmentPreview') || WCF::getUser()->getPermission('user.portal.canDownloadNewsAttachment'))) {
			require_once(WCF_DIR.'lib/data/attachment/MessageAttachmentList.class.php');
			$attachmentList = new MessageAttachmentList($attachmentEntryIDArray, 'newsEntry');
			$attachmentList->readObjects();
			$attachments = $attachmentList->getSortedAttachments();

			// set embedded attachments
			require_once(WCF_DIR.'lib/data/message/bbcode/AttachmentBBCode.class.php');
			AttachmentBBCode::setAttachments($attachments);
		}
	}
}
?>