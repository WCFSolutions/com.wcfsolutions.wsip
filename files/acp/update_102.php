<?php
/**
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
$packageID = $this->installation->getPackageID();

// admin options
$sql = "UPDATE 	wcf".WCF_N."_group_option_value
	SET	optionValue = 1
	WHERE	groupID = 4
		AND optionID IN (
			SELECT	optionID
			FROM	wcf".WCF_N."_group_option
			WHERE	packageID IN (
					SELECT	dependency
					FROM	wcf".WCF_N."_package_dependency
					WHERE	packageID = ".$packageID."
				)
		)
		AND optionValue = '0'";
WCF::getDB()->sendQuery($sql);

// delete deprecated files
$deprecatedFiles = array(
	'acp/js/CategoryPermissionList.class.js',
	'icon/clockM.png',
	'icon/guestbookEntryAddL.png',
	'icon/guestbookEntryAddM.png',
	'icon/guestbookEntryEditL.png',
	'icon/guestbookEntryEditOptionsM.png',
	'icon/guestbookEntryM.png',
	'icon/guestbookEntryOptionsM.png',
	'icon/guestbookEntryS.png',
	'icon/guestbookEntryTrashM.png',
	'icon/guestbookEntryTrashOptionsM.png',
	'icon/guestbookOverviewL.png',
	'icon/guestbookOverviewM.png',
	'icon/guestbookOverviewS.png',
	'icon/linkAddL.png',
	'icon/linkAddM.png',
	'icon/linkEditL.png',
	'icon/linkL.png',
	'icon/linkM.png',
	'icon/linkReadMoreS.png',
	'icon/linkS.png',
	'icon/linkVisitS.png',
	'js/GuestbookEntryListEdit.class.js',
	'lib/acp/page/CategoryPermissionsObjectsPage.class.php',
	'lib/acp/page/CategoryPermissionsObjectsSuggestPage.class.php',
	'lib/action/AbstractGuestbookEntryAction.class.php',
	'lib/action/AbstractLinkAction.class.php',
	'lib/action/GuestbookEntryDeleteAction.class.php',
	'lib/action/GuestbookEntryDeleteMarkedAction.class.php',
	'lib/action/GuestbookEntryDisableAction.class.php',
	'lib/action/GuestbookEntryEnableAction.class.php',
	'lib/action/GuestbookEntryMarkAction.class.php',
	'lib/action/GuestbookEntryRecoverAction.class.php',
	'lib/action/GuestbookEntryRecoverMarkedAction.class.php',
	'lib/action/GuestbookEntrySubjectEditAction.class.php',
	'lib/action/GuestbookEntryTrashAction.class.php',
	'lib/action/GuestbookEntryUnmarkAllAction.class.php',
	'lib/action/LinkDeleteAction.class.php',
	'lib/data/box/type/LastArticlesBoxType.class.php',
	'lib/data/box/type/LastGuestbookEntriesBoxType.class.php',
	'lib/data/box/type/LastLinksBoxType.class.php',
	'lib/data/box/type/LastNewsEntriesBoxType.class.php',
	'lib/data/guestbook/GuestbookEntry.class.php',
	'lib/data/guestbook/GuestbookEntryEditor.class.php',
	'lib/data/guestbook/GuestbookEntryList.class.php',
	'lib/data/guestbook/GuestbookEntrySearch.class.php',
	'lib/data/guestbook/GuestbookEntrySearchResult.class.php',
	'lib/data/guestbook/GuestbookPublicationType.class.php',
	'lib/data/guestbook/ViewableGuestbookEntry.class.php',
	'lib/data/guestbook/ViewableGuestbookEntryList.class.php',
	'lib/data/link/CategoryLinkList.class.php',
	'lib/data/link/CategoryLinkTagList.class.php',
	'lib/data/link/Link.class.php',
	'lib/data/link/LinkEditor.class.php',
	'lib/data/link/LinkList.class.php',
	'lib/data/link/LinkPublicationType.class.php',
	'lib/data/link/LinkSearch.class.php',
	'lib/data/link/LinkSearchResult.class.php',
	'lib/data/link/TaggableLink.class.php',
	'lib/data/link/TaggedCategoryLinkList.class.php',
	'lib/data/link/TaggedLink.class.php',
	'lib/data/link/TaggedLinkList.class.php',
	'lib/data/link/ViewableLink.class.php',
	'lib/data/link/ViewableLinkList.class.php',
	'lib/data/moderation/type/DeletedGuestbookEntriesModerationType.class.php',
	'lib/data/moderation/type/HiddenGuestbookEntriesModerationType.class.php',
	'lib/data/moderation/type/MarkedGuestbookEntriesModerationType.class.php',
	'lib/form/GuestbookEntryAddForm.class.php',
	'lib/form/GuestbookEntryEditForm.class.php',
	'lib/form/LinkAddForm.class.php',
	'lib/form/LinkEditForm.class.php',
	'lib/page/GuestbookPage.class.php',
	'lib/page/LinkOverviewPage.class.php',
	'lib/page/LinkPage.class.php',
	'lib/page/LinkVisitPage.class.php',
	'lib/page/ModerationDeletedGuestbookEntriesPage.class.php',
	'lib/page/ModerationGuestbookEntriesPage.class.php',
	'lib/page/ModerationHiddenGuestbookEntriesPage.class.php',
	'lib/page/ModerationMarkedGuestbookEntriesPage.class.php',
	'lib/system/cache/CacheBuilderLastArticlesBoxType.class.php',
	'lib/system/cache/CacheBuilderLastGuestbookEntriesBoxType.class.php',
	'lib/system/cache/CacheBuilderLastLinksBoxType.class.php',
	'lib/system/cache/CacheBuilderLastNewsEntriesBoxType.class.php'
);

$sql = "DELETE FROM	wcf".WCF_N."_package_installation_file_log
	WHERE		filename IN ('".implode("','", array_map('escapeString', $deprecatedFiles))."')
			AND packageID = ".$packageID;
WCF::getDB()->sendQuery($sql);

foreach ($deprecatedFiles as $file) {
	@unlink(RELATIVE_WCF_DIR.$this->installation->getPackage()->getDir().$file);
}

// delete deprecated templates
$deprecatedTemplates = array(
	'articleSectionEdit',
	'guestbook',
	'guestbookEntryAdd',
	'guestbookEntryInlineEdit',
	'lastArticlesBoxType',
	'lastGuestbookEntriesBoxType.',
	'lastLinksBoxType',
	'lastNewsEntriesBoxType',
	'link',
	'linkAdd',
	'linkAddCategorySelect',
	'linkOverview',
	'moderationGuestbookEntries',
	'searchLink',
	'searchResultGuestbookEntry',
	'searchResultLink',
	'taggedLinks'
);

$sql = "DELETE FROM	wcf".WCF_N."_template
	WHERE		templateName IN ('".implode("','", array_map('escapeString', $deprecatedTemplates))."')
			AND packageID = ".$packageID;
WCF::getDB()->sendQuery($sql);

foreach ($deprecatedTemplates as $template) {
	@unlink(RELATIVE_WCF_DIR.$this->installation->getPackage()->getDir().'templates/'.$template.'.tpl');
}

// get languages
require_once(WCF_DIR.'lib/system/language/LanguageEditor.class.php');
$languageCodes = LanguageEditor::getAvailableLanguageCodes($packageID);
$languages = $languageItems = $languageItemsUseCustom = array();
foreach ($languageCodes as $languageID => $languageCode) {
	$languages[$languageID] = new LanguageEditor($languageID);
	$languageItems[$languageID] = $languageItemsUseCustom[$languageID] = array();
}

// get old language items
$oldLanguageItems = array();
$sql = "SELECT	languageID, languageItem, languageUseCustomValue, languageItemValue, languageCustomItemValue
	FROM	wcf".WCF_N."_language_item
	WHERE	languageCategoryID = (
			SELECT	languageCategoryID
			FROM	wcf".WCF_N."_language_category
			WHERE	languageCategory = 'wsip.contentItem'
		)
		AND packageID IN (
			SELECT	dependency
			FROM	wcf".WCF_N."_package_dependency
			WHERE	packageID = ".$packageID."
		)
		AND languageItem LIKE 'wsip.contentItem.contentItem%.description'";
$result = WCF::getDB()->sendQuery($sql);
while ($row = WCF::getDB()->fetchArray($result)) {
	if (!isset($oldLanguageItems[$row['languageID']])) {
		$oldLanguageItems[$row['languageID']] = array();
	}
	$oldLanguageItems[$row['languageID']][$row['languageItem']] = ($row['languageUseCustomValue'] ? $row['languageCustomItemValue'] : $row['languageItemValue']);
}

// get instance no
$instanceNo = WCF_N.'_'.$this->installation->getPackage()->getInstanceNo();

// update content items
$sql = "SELECT		* 
	FROM		wsip".$instanceNo."_content_item
	ORDER BY	showOrder";
$result = WCF::getDB()->sendQuery($sql);
while ($row = WCF::getDB()->fetchArray($result)) {
	foreach ($languages as $languageID => $language) {
		$languageItems[$languageID]['wsip.contentItem.'.$row['contentItem'].'.text'] = (isset($oldLanguageItems[$languageID]['wsip.contentItem.'.$row['contentItem'].'.description']) ? $oldLanguageItems[$languageID]['wsip.contentItem.'.$row['contentItem'].'.description'] : '');
		$languageItems[$languageID]['wsip.contentItem.'.$row['contentItem'].'.description'] = '';
		$languageItemsUseCustom[$languageID]['wsip.contentItem.'.$row['contentItem'].'.description'] = (isset($oldLanguageItems[$languageID]['wsip.contentItem.'.$row['contentItem'].'.description']) ? 1 : 0);
	}
}

// update language items
foreach ($languages as $languageID => $language) {
	$language->updateItems($languageItems[$languageID], 0, $packageID, $languageItemsUseCustom[$languageID]);
}

// delete deprecated box tabs
require_once(WCF_DIR.'lib/data/box/BoxEditor.class.php');
$sql = "SELECT		box.* 
	FROM		wcf".WCF_N."_box box
	LEFT JOIN	wcf".WCF_N."_box_tab box_tab
	ON		(box_tab.boxID = box.boxID)
	WHERE		box_tab.boxTabType IN ('lastLinks', 'lastGuestbookEntries')
			AND box_tab.packageID = ".$packageID;
$result = WCF::getDB()->sendQuery($sql);
while ($row = WCF::getDB()->fetchArray($result)) {
	$box = new BoxEditor(null, $row);
	$box->delete();
}

// update news entries box
$sql = "UPDATE 	wcf".WCF_N."_box_tab
	SET	boxTabType = 'newsEntries'
	WHERE	boxTabType = 'lastNewsEntries'
		AND packageID = ".$packageID;
WCF::getDB()->sendQuery($sql);

$sql = "UPDATE 	wcf".WCF_N."_box_tab_type
	SET	boxTabType = 'newsEntries'
	WHERE	boxTabType = 'lastNewsEntries'
		AND packageID = ".$packageID;
WCF::getDB()->sendQuery($sql);

$sql = "UPDATE 	wcf".WCF_N."_box_tab_option
	SET	boxTabType = 'newsEntries',
		optionName = 'maxEntries'
	WHERE	boxTabType = 'lastNewsEntries'
		AND optionName = 'maxNewsEntries'
		AND packageID = ".$packageID;
WCF::getDB()->sendQuery($sql);

$sql = "UPDATE 	wcf".WCF_N."_box_tab_option
	SET	boxTabType = 'newsEntries',
		optionName = 'categoryIDs'
	WHERE	boxTabType = 'lastNewsEntries'
		AND optionName = 'newsCategoryIDs'
		AND packageID = ".$packageID;
WCF::getDB()->sendQuery($sql);

$sql = "UPDATE 	wcf".WCF_N."_box_tab_option
	SET	boxTabType = 'newsEntries',
		optionName = 'displayType'
	WHERE	boxTabType = 'lastNewsEntries'
		AND optionName = 'newsDisplayType'
		AND packageID = ".$packageID;
WCF::getDB()->sendQuery($sql);

// update articles box
$sql = "UPDATE 	wcf".WCF_N."_box_tab
	SET	boxTabType = 'articles'
	WHERE	boxTabType = 'lastArticles'
		AND packageID = ".$packageID;
WCF::getDB()->sendQuery($sql);

$sql = "UPDATE 	wcf".WCF_N."_box_tab_type
	SET	boxTabType = 'articles'
	WHERE	boxTabType = 'lastArticles'
		AND packageID = ".$packageID;
WCF::getDB()->sendQuery($sql);

$sql = "UPDATE 	wcf".WCF_N."_box_tab_option
	SET	boxTabType = 'articles'
	WHERE	boxTabType = 'lastArticles'
		AND optionName = 'maxArticles'
		AND packageID = ".$packageID;
WCF::getDB()->sendQuery($sql);

$sql = "UPDATE 	wcf".WCF_N."_box_tab_option
	SET	boxTabType = 'articles',
		optionName = 'categoryIDs'
	WHERE	boxTabType = 'lastArticles'
		AND optionName = 'articleCategoryIDs'
		AND packageID = ".$packageID;
WCF::getDB()->sendQuery($sql);
?>