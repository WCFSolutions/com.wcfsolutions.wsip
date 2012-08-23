<?php
// wsip imports
require_once(WSIP_DIR.'lib/acp/form/CategoryAddForm.class.php');

/**
 * Shows the category edit form.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	acp.form
 * @category	Infinite Portal
 */
class CategoryEditForm extends CategoryAddForm {
	// system
	public $activeMenuItem = 'wsip.acp.menu.link.content.category';
	public $neededPermissions = 'admin.portal.canEditCategory';

	/**
	 * category id
	 *
	 * @var	integer
	 */
	public $categoryID = 0;

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

		// get category
		if (isset($_REQUEST['categoryID'])) $this->categoryID = intval($_REQUEST['categoryID']);
		$this->category = new CategoryEditor($this->categoryID);
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
			$this->parentID = $this->category->parentID;
			$this->allowDescriptionHtml = $this->category->allowDescriptionHtml;
			$this->publicationTypes = $this->category->getAssignedPublicationTypes();
			$this->showOrder = $this->category->showOrder;

			// get permissions
			$sql = "		(SELECT		user_permission.*, user.userID AS id, 'user' AS type, user.username AS name
						FROM		wsip".WSIP_N."_category_to_user user_permission
						LEFT JOIN	wcf".WCF_N."_user user
						ON		(user.userID = user_permission.userID)
						WHERE		categoryID = ".$this->categoryID.")
				UNION
						(SELECT		group_permission.*, usergroup.groupID AS id, 'group' AS type, usergroup.groupName AS name
						FROM		wsip".WSIP_N."_category_to_group group_permission
						LEFT JOIN	wcf".WCF_N."_group usergroup
						ON		(usergroup.groupID = group_permission.groupID)
						WHERE		categoryID = ".$this->categoryID.")
				ORDER BY	name";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if (empty($row['id'])) continue;
				$permission = array('name' => $row['name'], 'type' => $row['type'], 'id' => $row['id']);
				unset($row['name'], $row['userID'], $row['groupID'], $row['categoryID'], $row['id'], $row['type']);
				foreach ($row as $key => $value) {
					if (!in_array($key, $this->permissionSettings)) unset($row[$key]);
				}
				$permission['settings'] = $row;
				$this->permissions[] = $permission;
			}

			// get moderators
			$sql = "SELECT		moderator.*, IFNULL(user.username, usergroup.groupName) AS name, user.userID, usergroup.groupID
				FROM		wsip".WSIP_N."_category_moderator moderator
				LEFT JOIN	wcf".WCF_N."_user user
				ON		(user.userID = moderator.userID)
				LEFT JOIN	wcf".WCF_N."_group usergroup
				ON		(usergroup.groupID = moderator.groupID)
				WHERE		categoryID = ".$this->categoryID."
				ORDER BY	name";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if (empty($row['userID']) && empty($row['groupID'])) continue;
				$moderator = array('name' => $row['name'], 'type' => ($row['userID'] ? 'user' : 'group'), 'id' => ($row['userID'] ? $row['userID'] : $row['groupID']));
				unset($row['name'], $row['userID'], $row['groupID'], $row['categoryID']);
				foreach ($row as $key => $value) {
					if (!in_array($key, $this->moderatorSettings)) unset($row[$key]);
				}
				$moderator['settings'] = $row;
				$this->moderators[] = $moderator;
			}

			// get title and description
			if (WCF::getLanguage()->getLanguageID() != $this->languageID) $language = new Language($this->languageID);
			else $language = WCF::getLanguage();
			$this->title = $language->get('wsip.category.'.$this->category->category);
			if ($this->title == 'wsip.category.'.$this->category->category) $this->title = '';
			$this->description = $language->get('wsip.category.'.$this->category->category.'.description');
			if ($this->description == 'wsip.category.'.$this->category->category.'.description') $this->description = '';
		}

		// get category options
		$this->categoryOptions = Category::getCategorySelect('', array(), array($this->categoryID));
	}

	/**
	 * @see CategoryAddForm::validateParentID()
	 */
	protected function validateParentID() {
		parent::validateParentID();

		if ($this->parentID) {
			if ($this->categoryID == $this->parentID || Category::searchChildren($this->categoryID, $this->parentID)) {
				throw new UserInputException('parentID', 'invalid');
			}
		}
	}

	/**
	 * @see Form::save()
	 */
	public function save() {
		AbstractForm::save();

		// update category
		$this->category->update($this->parentID, $this->title, $this->description, $this->allowDescriptionHtml, $this->showOrder, $this->languageID);
		$this->category->removeAssignedPublicationTypes();
		$this->category->assignPublicationTypes($this->publicationTypes);

		// save permissions
		$this->permissions = CategoryEditor::getCleanedPermissions($this->permissions);
		$this->category->removePermissions();
		$this->category->addPermissions($this->permissions, $this->permissionSettings);

		// save moderators
		$this->moderators = CategoryEditor::getCleanedPermissions($this->moderators);
		$this->category->removeModerators();
		$this->category->addModerators($this->moderators, $this->moderatorSettings);

		// reset cache
		Category::resetCache();

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
			'categoryID' => $this->categoryID,
			'category' => $this->category,
			'languageID' => $this->languageID,
			'languages' => $this->languages,
			'categoryQuickJumpOptions' => Category::getCategorySelect('', array()),
		));
	}
}
?>