<?php
// wsip imports
require_once(WSIP_DIR.'lib/acp/form/ContentItemAddForm.class.php');

/**
 * Shows the content item edit form.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	acp.form
 * @category	Infinite Portal
 */
class ContentItemEditForm extends ContentItemAddForm {
	// system
	public $activeMenuItem = 'wsip.acp.menu.link.content.contentItem';
	public $neededPermissions = 'admin.portal.canEditContentItem';

	/**
	 * content item id
	 *
	 * @var	integer
	 */
	public $contentItemID = 0;

	/**
	 * language id
	 *
	 * @var	integer
	 */
	public $languageID = 0;

	/**
	 * list of available languages
	 *
	 * @var	array
	 */
	public $languages = array();

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// get language id
		if (isset($_REQUEST['languageID'])) $this->languageID = intval($_REQUEST['languageID']);
		else $this->languageID = WCF::getLanguage()->getLanguageID();

		// get content item
		if (isset($_REQUEST['contentItemID'])) $this->contentItemID = intval($_REQUEST['contentItemID']);
		$this->contentItem = new ContentItemEditor($this->contentItemID);
	}

	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		// get all available languages
		$this->languages = Language::getLanguageCodes();

		if (!count($_POST)) {
			// get values
			$this->parentID = $this->contentItem->parentID;
			$this->contentItemType = $this->contentItem->contentItemType;
			$this->externalURL = $this->contentItem->externalURL;
			$this->icon = $this->contentItem->icon;
			$this->styleID = $this->contentItem->styleID;
			$this->enforceStyle = $this->contentItem->enforceStyle;
			$this->boxLayoutID = $this->contentItem->boxLayoutID;
			$this->allowSpidersToIndexThisPage = $this->contentItem->allowSpidersToIndexThisPage;
			$this->showOrder = $this->contentItem->showOrder;

			// get title and description
			if (WCF::getLanguage()->getLanguageID() != $this->languageID) $language = new Language($this->languageID);
			else $language = WCF::getLanguage();
			$this->title = $language->get('wsip.contentItem.'.$this->contentItem->contentItem);
			if ($this->title == 'wsip.contentItem.'.$this->contentItem->contentItem) $this->title = '';
			$this->description = $language->get('wsip.contentItem.'.$this->contentItem->contentItem.'.description');
			if ($this->description == 'wsip.contentItem.'.$this->contentItem->contentItem.'.description') $this->description = '';
			$this->text = $language->get('wsip.contentItem.'.$this->contentItem->contentItem.'.text');
			if ($this->text == 'wsip.contentItem.'.$this->contentItem->contentItem.'.text') $this->text = '';
			$this->metaDescription = $language->get('wsip.contentItem.'.$this->contentItem->contentItem.'.metaDescription');
			if ($this->metaDescription == 'wsip.contentItem.'.$this->contentItem->contentItem.'.metaDescription') $this->metaDescription = '';
			$this->metaKeywords = $language->get('wsip.contentItem.'.$this->contentItem->contentItem.'.metaKeywords');
			if ($this->metaKeywords == 'wsip.contentItem.'.$this->contentItem->contentItem.'.metaKeywords') $this->metaKeywords = '';

			// publishing start time
			if ($this->contentItem->publishingStartTime) {
				$this->publishingStartTimeDay = intval(DateUtil::formatDate('%e', $this->contentItem->publishingStartTime, false, true));
				$this->publishingStartTimeMonth = intval(DateUtil::formatDate('%m', $this->contentItem->publishingStartTime, false, true));
				$this->publishingStartTimeYear = DateUtil::formatDate('%Y', $this->contentItem->publishingStartTime, false, true);
				$this->publishingStartTimeHour = DateUtil::formatDate('%H', $this->contentItem->publishingStartTime, false, true);
				$this->publishingStartTimeMinutes = DateUtil::formatDate('%M', $this->contentItem->publishingStartTime, false, true);
			}

			// publishing end time
			if ($this->contentItem->publishingEndTime) {
				$this->publishingEndTimeDay = intval(DateUtil::formatDate('%e', $this->contentItem->publishingEndTime, false, true));
				$this->publishingEndTimeMonth = intval(DateUtil::formatDate('%m', $this->contentItem->publishingEndTime, false, true));
				$this->publishingEndTimeYear = DateUtil::formatDate('%Y', $this->contentItem->publishingEndTime, false, true);
				$this->publishingEndTimeHour = DateUtil::formatDate('%H', $this->contentItem->publishingEndTime, false, true);
				$this->publishingEndTimeMinutes = DateUtil::formatDate('%M', $this->contentItem->publishingEndTime, false, true);
			}

			// get permissions
			$sql = "		(SELECT		user_permission.*, user.userID AS id, 'user' AS type, user.username AS name
						FROM		wsip".WSIP_N."_content_item_to_user user_permission
						LEFT JOIN	wcf".WCF_N."_user user
						ON		(user.userID = user_permission.userID)
						WHERE		contentItemID = ".$this->contentItemID.")
				UNION
						(SELECT		group_permission.*, usergroup.groupID AS id, 'group' AS type, usergroup.groupName AS name
						FROM		wsip".WSIP_N."_content_item_to_group group_permission
						LEFT JOIN	wcf".WCF_N."_group usergroup
						ON		(usergroup.groupID = group_permission.groupID)
						WHERE		contentItemID = ".$this->contentItemID.")
				ORDER BY	name";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if (empty($row['id'])) continue;
				$permission = array('name' => $row['name'], 'type' => $row['type'], 'id' => $row['id']);
				unset($row['name'], $row['userID'], $row['groupID'], $row['contentItemID'], $row['id'], $row['type']);
				foreach ($row as $key => $value) {
					if (!in_array($key, $this->permissionSettings)) unset($row[$key]);
				}
				$permission['settings'] = $row;
				$this->permissions[] = $permission;
			}
		}

		// get content item options
		$this->contentItemOptions = ContentItem::getContentItemSelect(array(), array($this->contentItemID));
	}

	/**
	 * @see ContentItemAddForm::validateParentID()
	 */
	protected function validateParentID() {
		parent::validateParentID();

		if ($this->parentID) {
			if ($this->contentItemID == $this->parentID || ContentItem::searchChildren($this->contentItemID, $this->parentID)) {
				throw new UserInputException('parentID', 'invalid');
			}
		}
	}

	/**
	 * @see Form::save()
	 */
	public function save() {
		AbstractForm::save();

		// update content item
		$this->contentItem->update($this->parentID, $this->title, $this->description, $this->text, $this->contentItemType, $this->externalURL, $this->icon, $this->metaDescription, $this->metaKeywords,
		$this->publishingStartTime, $this->publishingEndTime, $this->styleID, $this->enforceStyle, $this->boxLayoutID, $this->allowSpidersToIndexThisPage, $this->showOrder, $this->languageID);

		// save permissions
		$this->permissions = ContentItemEditor::getCleanedPermissions($this->permissions);
		$this->contentItem->removePermissions();
		$this->contentItem->addPermissions($this->permissions, $this->permissionSettings);

		// reset cache
		ContentItemEditor::resetCache();

		// reset sessions
		Session::resetSessions(array(), true, false);
		$this->saved();

		// show success message
		WCF::getTPL()->assign('success', true);
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'action' => 'edit',
			'contentItemID' => $this->contentItemID,
			'contentItem' => $this->contentItem,
			'languageID' => $this->languageID,
			'languages' => $this->languages,
			'contentItemQuickJumpOptions' => ContentItem::getContentItemSelect(array())
		));
	}
}
?>