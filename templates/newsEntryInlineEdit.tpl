<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/InlineListEdit.class.js"></script>
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/StringUtil.class.js"></script>
<script type="text/javascript" src="{@RELATIVE_WSIP_DIR}js/NewsEntryListEdit.class.js"></script>
<script type="text/javascript">
	//<![CDATA[
	// entry data
	var newsEntryData = new Hash();

	// language
	language['wcf.global.button.mark']				= '{lang}wcf.global.button.mark{/lang}';
	language['wcf.global.button.unmark'] 				= '{lang}wcf.global.button.unmark{/lang}';
	language['wcf.global.button.delete'] 				= '{lang}wcf.global.button.delete{/lang}';
	language['wcf.global.button.deleteCompletely'] 			= '{lang}wcf.global.button.deleteCompletely{/lang}';
	language['wsip.news.entries.button.recover'] 			= '{lang}wsip.news.entries.button.recover{/lang}';
	language['wsip.news.entries.button.enable'] 			= '{lang}wsip.news.entries.button.enable{/lang}';
	language['wsip.news.entries.button.disable'] 			= '{lang}wsip.news.entries.button.disable{/lang}';
	language['wsip.news.entries.button.editTitle'] 			= '{lang}wsip.news.entries.button.editTitle{/lang}';
	language['wsip.news.entries.markedEntries'] 			= '{lang}wsip.news.entries.markedEntries{/lang}';
	language['wsip.news.entries.delete.sure'] 			= '{lang}wsip.news.entries.delete.sure{/lang}';
	language['wsip.news.entries.deleteCompletely.sure'] 		= '{lang}wsip.news.entries.deleteCompletely.sure{/lang}';
	language['wsip.news.entries.deleteMarked.sure'] 		= '{lang}wsip.news.entries.deleteMarked.sure{/lang}';
	language['wsip.news.entries.button.move'] 			= '{lang}wsip.news.entries.button.move{/lang}';
	language['wsip.news.entries.button.showMarked'] 		= '{lang}wsip.news.entries.button.showMarked{/lang}';
	language['wsip.news.entries.delete.reason'] 			= '{lang}wsip.news.entries.delete.reason{/lang}';
	language['wsip.news.entries.deleteMarked.reason'] 		= '{lang}wsip.news.entries.deleteMarked.reason{/lang}';
	language['wcf.global.button.submit']				= '{lang}wcf.global.button.submit{/lang}';
	language['wcf.global.button.reset']				= '{lang}wcf.global.button.reset{/lang}';

	// permissions
	var permissions = new Object();
	permissions['canDeleteNewsEntry'] = {@$permissions.canDeleteNewsEntry};
	permissions['canReadDeletedNewsEntry'] = {@$permissions.canReadDeletedNewsEntry};
	permissions['canDeleteNewsEntryCompletely'] = {@$permissions.canDeleteNewsEntryCompletely};
	permissions['canEnableNewsEntry'] = {@$permissions.canEnableNewsEntry};
	permissions['canMarkNewsEntry'] = {@$permissions.canMarkNewsEntry};
	permissions['canMoveNewsEntry'] = {@$permissions.canMoveNewsEntry};
	permissions['canEditNewsEntry'] = {@$permissions.canEditNewsEntry};

	// init
	document.observe("dom:loaded", function() {
		newsEntryListEdit = new NewsEntryListEdit(newsEntryData, {@$markedEntries}, {
			'page':			'{@$pageType}',
			'url':			'{@$url}',
			'categoryID':		{if $category|isset}{@$category->categoryID}{else}0{/if},
			'entryID':		{if $entry|isset}{@$entry->entryID}{else}0{/if},
			'enableRecycleBin':	{@NEWS_ENTRY_ENABLE_RECYCLE_BIN}
		});
	});
	//]]>
</script>