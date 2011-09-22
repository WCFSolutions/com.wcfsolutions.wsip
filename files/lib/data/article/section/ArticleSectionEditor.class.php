<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/article/section/ArticleSection.class.php');

/**
 * Provides functions to manage article sections.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.article.section
 * @category	Infinite Portal
 */
class ArticleSectionEditor extends ArticleSection {	
	/**
	 * Updates this section.
	 * 
	 * @param	integer				$parentSectionID			
	 * @param	string				$subject
	 * @param	string				$message
	 * @param	integer				$showOrder
	 * @param	array				$options
	 * @param	MessageAttachmentListEditor	$attachmentList
	 */
	public function update($parentSectionID, $subject, $message, $options = array(), $attachmentList = null) {
		// get number of attachments
		$attachmentsAmount = ($attachmentList !== null ? count($attachmentList->getAttachments($this->sectionID)) : 0);

		// update show order
		/*if ($this->showOrder != $showOrder) {
			if ($showOrder < $this->showOrder) {
				$sql = "UPDATE	wsip".WSIP_N."_article_section
					SET 	showOrder = showOrder + 1
					WHERE 	showOrder >= ".$showOrder."
						AND showOrder < ".$this->showOrder."
						AND articleID = ".$this->articleID;
				WCF::getDB()->sendQuery($sql);
			}
			else if ($showOrder > $this->showOrder) {
				$sql = "UPDATE	wsip".WSIP_N."_article_section
					SET	showOrder = showOrder - 1
					WHERE	showOrder <= ".$showOrder."
						AND showOrder > ".$this->showOrder."
						AND articleID = ".$this->articleID;
				WCF::getDB()->sendQuery($sql);
			}
		}
		*/

		// update section
		$sql = "UPDATE 	wsip".WSIP_N."_article_section
			SET	parentSectionID = ".$parentSectionID.",
				subject = '".escapeString($subject)."',
				message = '".escapeString($message)."',
				attachments = ".$attachmentsAmount.",
				enableSmilies = ".(isset($options['enableSmilies']) ? $options['enableSmilies'] : 1).",
				enableHtml = ".(isset($options['enableHtml']) ? $options['enableHtml'] : 0).",
				enableBBCodes = ".(isset($options['enableBBCodes']) ? $options['enableBBCodes'] : 1)."
			WHERE 	sectionID = ".$this->sectionID;
		WCF::getDB()->sendQuery($sql);
		
		// update attachments
		if ($attachmentList != null) {
			$attachmentList->findEmbeddedAttachments($message);
		}
	}
	
	/**
	 * Deletes this section.
	 */
	public function delete() {
		self::deleteAll($this->sectionID);
	}
	
	/**
	 * Creates a new article section.
	 * 
	 * @param	integer				$parentSectionID
	 * @param	integer				$articleID
	 * @param	string				$subject
	 * @param	string				$message
	 * @param	integer				$showOrder
	 * @param	array				$options
	 * @param	MessageAttachmentListEditor	$attachmentList
	 * @return	ArticleSectionEditor
	 */
	public static function create($parentSectionID, $articleID, $subject, $message, $options = array(), $attachmentList = null) {
		// get number of attachments
		$attachmentsAmount = $attachmentList != null ? count($attachmentList->getAttachments()) : 0;
		
		// get show order
		/*if ($showOrder == 0) {
			// get next number in row
			$sql = "SELECT	MAX(showOrder) AS showOrder
				FROM	wsip".WSIP_N."_article_section
				WHERE	articleID = ".$articleID;
			$row = WCF::getDB()->getFirstRow($sql);
			if (!empty($row)) $showOrder = intval($row['showOrder']) + 1;
			else $showOrder = 1;
		}
		else {
			$sql = "UPDATE	wsip".WSIP_N."_article_section
				SET 	showOrder = showOrder + 1
				WHERE 	showOrder >= ".$showOrder."
					AND articleID = ".$articleID;
			WCF::getDB()->sendQuery($sql);
		}*/
		
		// insert section
		$sql = "INSERT INTO	wsip".WSIP_N."_article_section
					(parentSectionID, articleID, subject, message, attachments, enableSmilies, enableHtml, enableBBCodes)
			VALUES		(".$parentSectionID.", ".$articleID.", '".escapeString($subject)."', '".escapeString($message)."', ".$attachmentsAmount.",
					".(isset($options['enableSmilies']) ? $options['enableSmilies'] : 1).",
					".(isset($options['enableHtml']) ? $options['enableHtml'] : 0).",
					".(isset($options['enableBBCodes']) ? $options['enableBBCodes'] : 1).")";
		WCF::getDB()->sendQuery($sql);
		
		// get section id
		$sectionID = WCF::getDB()->getInsertID("wsip".WSIP_N."_article_section", 'sectionID');
		
		// update attachments
		if ($attachmentList !== null) {
			$attachmentList->updateContainerID($sectionID);
			$attachmentList->findEmbeddedAttachments($message);
		}
		
		// return new section
		return new ArticleSectionEditor($sectionID);
	}
	
	/**
	 * Deletes all sections with the given section ids.
	 *
	 * @param	string		$sectionIDs
	 */
	public static function deleteAll($sectionIDs) {
		if (empty($sectionIDs)) return;
		
		// update subsections
		$sql = "SELECT 	sectionID, parentSectionID
			FROM 	wsip".WSIP_N."_article_section
			WHERE 	sectionID IN (".$sectionIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$sql = "UPDATE	wsip".WSIP_N."_article_section
				SET	parentSectionID = ".$row['parentSectionID']."
				WHERE	parentSectionID = ".$row['sectionID'];
			WCF::getDB()->sendQuery($sql);
		}

		// delete article sections
		$sql = "DELETE FROM	wsip".WSIP_N."_article_section
			WHERE		sectionID IN (".$sectionIDs.")";
		WCF::getDB()->sendQuery($sql);
		
		// delete attachments
		require_once(WCF_DIR.'lib/data/attachment/MessageAttachmentListEditor.class.php');
		$attachmentList = new MessageAttachmentListEditor(explode(',', $sectionIDs), 'articleSection');
		$attachmentList->deleteAll();
	}
	
	/**
	 * Creates a preview of an article section.
	 *
	 * @param	string		$subject
	 * @param 	string		$message
	 * @param 	boolean		$enableSmilies
	 * @param 	boolean		$enableHtml
	 * @param 	boolean		$enableBBCodes
	 * @return	string
	 */
	public static function createPreview($subject, $message, $enableSmilies = 1, $enableHtml = 0, $enableBBCodes = 1) {
		$row = array(
			'sectionID' => 0,
			'subject' => $subject,
			'message' => $message,
			'enableSmilies' => $enableSmilies,
			'enableHtml' => $enableHtml,
			'enableBBCodes' => $enableBBCodes,
			'messagePreview' => true
		);
		
		// get section
		require_once(WSIP_DIR.'lib/data/article/section/ViewableArticleSection.class.php');
		$section = new ViewableArticleSection(null, $row);
		return $section->getFormattedMessage();
	}
	
	/**
	 * Updates the positions of an article section directly.
	 *
	 * @param	integer		$sectionID
	 * @param	integer		$parentSectionID
	 * @param	integer		$position
	 */
	public static function updateShowOrder($sectionID, $parentSectionID, $position) {
		$sql = "UPDATE	wsip".WSIP_N."_article_section
			SET	parentSectionID = ".$parentSectionID.",
				showOrder = ".$position."
			WHERE 	sectionID = ".$sectionID;
		WCF::getDB()->sendQuery($sql);
	}
}
?>