<?php
// wcf imports
require_once(WCF_DIR.'lib/data/cronjobs/Cronjob.class.php');
require_once(WCF_DIR.'lib/system/session/Session.class.php');

/**
 * Cronjob for a hourly system cleanup for the portal.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	system.cronjob
 * @category	Infinite Portal
 */
class CleanupCronjob implements Cronjob {
	/**
	 * @see Cronjob::execute()
	 */
	public function execute($data) {
		// delete old sessions
		Session::deleteExpiredSessions((TIME_NOW - SESSION_TIMEOUT));

		// delete old captchas
		$sql = "DELETE FROM	wcf".WCF_N."_captcha
			WHERE		captchaDate < ".(TIME_NOW - 3600);
		WCF::getDB()->registerShutdownUpdate($sql);

		// delete searches
		$sql = "DELETE FROM	wcf".WCF_N."_search
			WHERE		searchDate < ".(TIME_NOW - 7200);
		WCF::getDB()->registerShutdownUpdate($sql);

		// delete orphaned attachments
		$sql = "SELECT	attachmentID
			FROM	wcf".WCF_N."_attachment
			WHERE	containerID = 0
				AND uploadTime < ".(TIME_NOW - 43200);
		$result = WCF::getDB()->sendQuery($sql);
		if (WCF::getDB()->countRows($result) > 0) {
			require_once(WCF_DIR.'lib/data/message/attachment/AttachmentsEditor.class.php');
			$attachmentIDs = '';
			while ($row = WCF::getDB()->fetchArray($result)) {
				if (!empty($attachmentIDs)) $attachmentIDs .= ',';
				$attachmentIDs .= $row['attachmentID'];

				// delete files
				AttachmentsEditor::deleteFile($row['attachmentID']);
			}

			if (!empty($attachmentIDs)) {
				$sql = "DELETE FROM	wcf".WCF_N."_attachment
					WHERE		attachmentID IN (".$attachmentIDs.")";
				WCF::getDB()->registerShutdownUpdate($sql);
			}
		}

		// delete bad user data
		$sql = "DELETE FROM	wsip".WSIP_N."_user
			WHERE		userID NOT IN (
						SELECT	userID
						FROM	wcf".WCF_N."_user
					)";
		WCF::getDB()->registerShutdownUpdate($sql);

		// optimize tables to save some memory (mysql only)
		if (WCF::getDB()->getDBType() == 'MySQLDatabase' || WCF::getDB()->getDBType() == 'MySQLiDatabase' || WCF::getDB()->getDBType() == 'MySQLPDODatabase') {
			$sql = "OPTIMIZE TABLE	wcf".WCF_N."_session_data, wcf".WCF_N."_acp_session_data, wcf".WCF_N."_search";
			WCF::getDB()->registerShutdownUpdate($sql);
		}
	}
}
?>