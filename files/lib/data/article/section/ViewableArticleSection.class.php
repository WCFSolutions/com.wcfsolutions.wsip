<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/article/section/ArticleSection.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/user/UserProfile.class.php');

/**
 * Represents a viewable article section in the portal.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.article.section
 * @category	Infinite Portal
 */
class ViewableArticleSection extends ArticleSection {
	/**
	 * user object
	 *
	 * @var UserProfile
	 */
	protected $user = null;

	/**
	 * @see DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		parent::handleData($data);
		$this->user = new UserProfile(null, $data);
	}

	/**
	 * Returns the formatted message.
	 * 
	 * @return	string
	 */
	public function getFormattedMessage() {
		require_once(WCF_DIR.'lib/data/message/bbcode/MessageParser.class.php');
		MessageParser::getInstance()->setOutputType('text/html');
		require_once(WCF_DIR.'lib/data/message/bbcode/AttachmentBBCode.class.php');
		AttachmentBBCode::setMessageID($this->sectionID);
		return MessageParser::getInstance()->parse($this->message, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes, !$this->messagePreview);
	}
	
	/**
	 * Returns the user object.
	 * 
	 * @return	UserProfile
	 */
	public function getUser() {
		return $this->user;
	}
}
?>