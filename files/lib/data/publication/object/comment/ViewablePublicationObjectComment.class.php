<?php
// wsip imports
require_once(WSIP_DIR.'lib/data/publication/object/comment/PublicationObjectComment.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/user/UserProfile.class.php');
require_once(WCF_DIR.'lib/data/message/bbcode/SimpleMessageParser.class.php');

/**
 * Represents a viewable publication object comment.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.publication.object.comment
 * @category	Infinite Portal
 */
class ViewablePublicationObjectComment extends PublicationObjectComment {
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
	 * Returns the formatted comment.
	 *
	 * @return	string
	 */
	public function getFormattedComment() {
		return SimpleMessageParser::getInstance()->parse($this->comment);
	}

	/**
	 * Returns an excerpt of the comment.
	 *
	 * @return	string
	 */
	public function getExcerpt() {
		$comment = $this->comment;

		// get abstract
		if (StringUtil::length($comment) > 50) {
			$comment = StringUtil::substring($comment, 0, 47).'...';
		}

		return $comment;
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