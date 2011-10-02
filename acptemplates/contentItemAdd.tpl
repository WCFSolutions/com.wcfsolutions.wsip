{include file='header'}

<div class="mainHeadline">
	<img src="{@RELATIVE_WSIP_DIR}icon/contentItem{@$action|ucfirst}L.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wsip.acp.contentItem.{@$action}{/lang}</h2>
		{if $contentItemID|isset}<p>{lang}{$contentItem->title}{/lang}</p>{/if}
	</div>
</div>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
	<p class="success">{if $action == 'add'}{lang}wsip.acp.contentItem.add.success{/lang}{else}{lang}wsip.acp.contentItem.edit.success{/lang}{/if}</p>	
{/if}

<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/Suggestion.class.js"></script>
<script type="text/javascript" src="{@RELATIVE_WSIP_DIR}acp/js/PermissionList.class.js"></script>
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/Calendar.class.js"></script>
<script type="text/javascript">
	//<![CDATA[
	var language = new Object();
	language['wsip.acp.contentItem.permissions.permissionsFor'] = '{staticlang}wsip.acp.contentItem.permissions.permissionsFor{/staticlang}';
	language['wsip.acp.contentItem.permissions.fullControl'] = '{lang}wsip.acp.contentItem.permissions.fullControl{/lang}';
	{foreach from=$permissionSettings item=permissionSetting}
		language['wsip.acp.contentItem.permissions.{@$permissionSetting}'] = '{lang}wsip.acp.contentItem.permissions.{@$permissionSetting}{/lang}';
	{/foreach}
	
	var permissions = new Hash();
	{assign var=i value=0}		
	{foreach from=$permissions item=permission}
		var settings = new Hash();
		settings.set('fullControl', -1);
		
		{foreach from=$permission.settings key=setting item=value}
			{if $setting != 'name' && $setting != 'type' && $setting != 'id'}
				settings.set('{@$setting}', {@$value});
			{/if}
		{/foreach}
		
		permissions.set({@$i}, {
			'name': '{@$permission.name|encodeJS}',
			'type': '{@$permission.type}',
			'id': '{@$permission.id}',
			'settings': settings
		});

		{assign var=i value=$i+1}
	{/foreach}
	
	var permissionSettings = new Array({implode from=$permissionSettings item=permissionSetting}'{@$permissionSetting}'{/implode});
	
	var calendar = new Calendar('{$monthList}', '{$weekdayList}', {@$startOfWeek});
	
	// content item type
	function setContentItemType(newType) {
		switch (newType) {
			case 0:
				showOptions('descriptionDiv', 'textDiv', 'allowSpidersToIndexThisPageDiv', 'meta', 'style');
				hideOptions('externalURLDiv');
				break;
			case 1:
				showOptions('externalURLDiv');
				hideOptions('descriptionDiv', 'textDiv', 'allowSpidersToIndexThisPageDiv', 'meta', 'style');
				break;
			case 2:
				showOptions('descriptionDiv', 'allowSpidersToIndexThisPageDiv', 'meta', 'style');
				hideOptions('externalURLDiv', 'textDiv');
				break;
		}
	}
	
	document.observe("dom:loaded", function() {
		setContentItemType({@$contentItemType});
		
		// user/group permissions
		var permissionList = new PermissionList('permission', 'contentItem', permissions, permissionSettings);
		
		// add onsubmit event
		$('contentItemAddForm').onsubmit = function() { 
			if (suggestion.selectedIndex != -1) return false;
			if (permissionList.inputHasFocus) return false;
			permissionList.submit(this);
		};
	});
	//]]>
</script>

{@$ckeditor->getConfigurationHTML()}

<div class="contentHeader">
	<div class="largeButtons">
		<ul><li><a href="index.php?page=ContentItemList&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}wsip.acp.menu.link.content.contentItem.view{/lang}"><img src="{@RELATIVE_WSIP_DIR}icon/contentItemM.png" alt="" /> <span>{lang}wsip.acp.menu.link.content.contentItem.view{/lang}</span></a></li></ul>
	</div>
</div>

<form method="post" action="index.php?form=ContentItem{@$action|ucfirst}" id="contentItemAddForm">
	<div class="border content">
		<div class="container-1">
			{if $contentItemID|isset && $contentItemQuickJumpOptions|count > 1}
				<fieldset>
					<legend>{lang}wsip.acp.contentItem.edit{/lang}</legend>
					<div class="formElement">
						<div class="formFieldLabel">
							<label for="contentItemChange">{lang}wsip.acp.contentItem.edit{/lang}</label>
						</div>
						<div class="formField">
							<select id="contentItemChange" onchange="document.location.href=fixURL('index.php?form=ContentItemEdit&amp;contentItemID='+this.options[this.selectedIndex].value+'&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}')">
								{htmloptions options=$contentItemQuickJumpOptions selected=$contentItemID disableEncoding=true}
							</select>
						</div>
					</div>
				</fieldset>
			{/if}
			
			<fieldset>
				<legend>{lang}wsip.acp.contentItem.contentItemType{/lang}</legend>
				<div class="formElement{if $errorField == 'contentItemType'} formError{/if}">
					<ul class="formOptions">
						<li><label><input onclick="if (IS_SAFARI) setContentItemType(0)" onfocus="setContentItemType(0)" type="radio" name="contentItemType" value="0" {if $contentItemType == 0}checked="checked" {/if}/> {lang}wsip.acp.contentItem.contentItemType.0{/lang}</label></li>
						<li><label><input onclick="if (IS_SAFARI) setContentItemType(1)" onfocus="setContentItemType(1)" type="radio" name="contentItemType" value="1" {if $contentItemType == 1}checked="checked" {/if}/> {lang}wsip.acp.contentItem.contentItemType.1{/lang}</label></li>
						<li><label><input onclick="if (IS_SAFARI) setContentItemType(2)" onfocus="setContentItemType(2)" type="radio" name="contentItemType" value="2" {if $contentItemType == 2}checked="checked" {/if}/> {lang}wsip.acp.contentItem.contentItemType.2{/lang}</label></li>
					</ul>
					{if $errorField == 'contentItemType'}
						<p class="innerError">
							{if $errorType == 'invalid'}{lang}wsip.acp.contentItem.error.contentItemType.invalid{/lang}{/if}
						</p>
					{/if}
				</div>
			</fieldset>
			
			<fieldset>
				<legend>{lang}wsip.acp.contentItem.classification{/lang}</legend>
					
				{if $contentItemOptions|count > 0}
					<div class="formElement{if $errorField == 'parentID'} formError{/if}" id="parentIDDiv">
						<div class="formFieldLabel">
							<label for="parentID">{lang}wsip.acp.contentItem.parentID{/lang}</label>
						</div>
						<div class="formField">
							<select name="parentID" id="parentID">
								<option value="0"></option>
								{htmlOptions options=$contentItemOptions selected=$parentID disableEncoding=true}
							</select>
							{if $errorField == 'parentID'}
								<p class="innerError">
									{if $errorType == 'invalid'}{lang}wsip.acp.contentItem.error.parentID.invalid{/lang}{/if}
								</p>
							{/if}
						</div>
						<div class="formFieldDesc hidden" id="parentIDHelpMessage">
							<p>{lang}wsip.acp.contentItem.parentID.description{/lang}</p>
						</div>
					</div>
					<script type="text/javascript">//<![CDATA[
						inlineHelp.register('parentID');
					//]]></script>
				{/if}
				
				<div class="formElement" id="showOrderDiv">
					<div class="formFieldLabel">
						<label for="showOrder">{lang}wsip.acp.contentItem.showOrder{/lang}</label>
					</div>
					<div class="formField">	
						<input type="text" class="inputText" name="showOrder" id="showOrder" value="{$showOrder}" />
					</div>
					<div class="formFieldDesc hidden" id="showOrderHelpMessage">
						{lang}wsip.acp.contentItem.showOrder.description{/lang}
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('showOrder');
				//]]></script>
					
				{if $additionalClassificationFields|isset}{@$additionalClassificationFields}{/if}
			</fieldset>
			
			<fieldset>
				<legend>{lang}wsip.acp.contentItem.data{/lang}</legend>
				
				{if $action == 'edit'}
					<div class="formElement" id="languageIDDiv">
						<div class="formFieldLabel">
							<label for="languageID">{lang}wsip.acp.contentItem.language{/lang}</label>
						</div>
						<div class="formField">
							<select name="languageID" id="languageID" onchange="location.href='index.php?form=ContentItemEdit&amp;contentItemID={@$contentItemID}&amp;languageID='+this.value+'&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}'">
								{foreach from=$languages key=availableLanguageID item=languageCode}
									<option value="{@$availableLanguageID}"{if $availableLanguageID == $languageID} selected="selected"{/if}>{lang}wcf.global.language.{@$languageCode}{/lang}</option>
								{/foreach}
							</select>
						</div>
						<div class="formFieldDesc hidden" id="languageIDHelpMessage">
							{lang}wsip.acp.contentItem.language.description{/lang}
						</div>
					</div>
					<script type="text/javascript">//<![CDATA[
						inlineHelp.register('languageID');
					//]]></script>
				{/if}
				
				<div class="formElement{if $errorField == 'title'} formError{/if}" id="titleDiv">
					<div class="formFieldLabel">
						<label for="title">{lang}wsip.acp.contentItem.title{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="title" name="title" value="{$title}" />
						{if $errorField == 'title'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
					<div class="formFieldDesc hidden" id="titleHelpMessage">
						<p>{lang}wsip.acp.contentItem.title.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('title');
				//]]></script>
				
				<div class="formElement" id="descriptionDiv">
					<div class="formFieldLabel">
						<label for="description">{lang}wsip.acp.contentItem.description{/lang}</label>
					</div>
					<div class="formField">
						<textarea id="description" name="description" cols="40" rows="5">{$description}</textarea>
					</div>
					<div class="formFieldDesc hidden" id="descriptionHelpMessage">
						<p>{lang}wsip.acp.contentItem.description.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('description');
				//]]></script>
				
				<div class="formElement{if $errorField == 'text'} formError{/if}" id="textDiv">
					<div class="formFieldLabel">
						<label for="text">{lang}wsip.acp.contentItem.text{/lang}</label>
					</div>
					<div class="formField">
						<textarea id="text" name="text" cols="40" rows="10">{$text}</textarea>
						{if $errorField == 'text'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
					<div class="formFieldDesc hidden" id="textHelpMessage">
						<p>{lang}wsip.acp.contentItem.text.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('text');
				//]]></script>					
				
				<div class="formElement{if $errorField == 'externalURL'} formError{/if}" id="externalURLDiv">
					<div class="formFieldLabel">
						<label for="externalURL">{lang}wsip.acp.contentItem.externalURL{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="externalURL" name="externalURL" value="{$externalURL}" />
						{if $errorField == 'externalURL'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
					<div class="formFieldDesc hidden" id="externalURLHelpMessage">
						<p>{lang}wsip.acp.contentItem.externalURL.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('externalURL');
				//]]></script>	
					
				<div class="formElement" id="allowSpidersToIndexThisPageDiv">
					<div class="formField">
						<label id="allowSpidersToIndexThisPage"><input type="checkbox" name="allowSpidersToIndexThisPage" value="1" {if $allowSpidersToIndexThisPage}checked="checked" {/if}/> {lang}wsip.acp.contentItem.allowSpidersToIndexThisPage{/lang}</label>
					</div>
					<div class="formFieldDesc hidden" id="allowSpidersToIndexThisPageHelpMessage">
						<p>{lang}wsip.acp.contentItem.allowSpidersToIndexThisPage.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('allowSpidersToIndexThisPage');
				//]]></script>
					
				{if $additionalDataFields|isset}{@$additionalDataFields}{/if}
			</fieldset>
			
			<fieldset id="meta">
				<legend>{lang}wsip.acp.contentItem.meta{/lang}</legend>
				
				<div class="formElement" id="metaDescriptionDiv">
					<div class="formFieldLabel">
						<label for="metaDescription">{lang}wsip.acp.contentItem.metaDescription{/lang}</label>
					</div>
					<div class="formField">
						<textarea id="metaDescription" name="metaDescription" cols="40" rows="5">{$metaDescription}</textarea>
					</div>
					<div class="formFieldDesc hidden" id="metaDescriptionHelpMessage">
						<p>{lang}wsip.acp.contentItem.metaDescription.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('metaDescription');
				//]]></script>
				
				<div class="formElement" id="metaKeywordsDiv">
					<div class="formFieldLabel">
						<label for="metaKeywords">{lang}wsip.acp.contentItem.metaKeywords{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="metaKeywords" name="metaKeywords" value="{$metaKeywords}" />
					</div>
					<div class="formFieldDesc hidden" id="metaKeywordsHelpMessage">
						<p>{lang}wsip.acp.contentItem.metaKeywords.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('metaKeywords');
				//]]></script>
					
				{if $additionalMetaFields|isset}{@$additionalMetaFields}{/if}
			</fieldset>
			
			<fieldset>
				<legend>{lang}wsip.acp.contentItem.publishingTime{/lang}</legend>
				
				<div class="formGroup{if $errorField == 'publishingStartTime'} formError{/if}" id="publishingStartTimeDiv">
					<div class="formGroupLabel">
						<label>{lang}wsip.acp.contentItem.publishingStartTime{/lang}</label>
					</div>
					<div class="formGroupField">
						<fieldset>
							<legend><label>{lang}wsip.acp.contentItem.publishingStartTime{/lang}</label></legend>
							
							<div class="formField">
								<div class="floatedElement">
									<label for="publishingStartTimeDay">{lang}wcf.global.date.day{/lang}</label>
									{htmlOptions options=$dayOptions selected=$publishingStartTimeDay id=publishingStartTimeDay name=publishingStartTimeDay}
								</div>
								
								<div class="floatedElement">
									<label for="publishingStartTimeMonth">{lang}wcf.global.date.month{/lang}</label>
									{htmlOptions options=$monthOptions selected=$publishingStartTimeMonth id=publishingStartTimeMonth name=publishingStartTimeMonth}
								</div>
								
								<div class="floatedElement">
									<label for="publishingStartTimeYear">{lang}wcf.global.date.year{/lang}</label>
									<input id="publishingStartTimeYear" class="inputText fourDigitInput" type="text" name="publishingStartTimeYear" value="{@$publishingStartTimeYear}" maxlength="4" />
								</div>
								
								<div class="floatedElement">
									<label for="publishingStartTimeHour">{lang}wcf.global.date.hour{/lang}</label>
									{htmlOptions options=$hourOptions selected=$publishingStartTimeHour id=publishingStartTimeHour name=publishingStartTimeHour} :
								</div>
																	
								<div class="floatedElement">
									<label for="publishingStartTimeMinutes">{lang}wcf.global.date.minutes{/lang}</label>
									{htmlOptions options=$minuteOptions selected=$publishingStartTimeMinutes id=publishingStartTimeMinutes name=publishingStartTimeMinutes}
								</div>
								
								<div class="floatedElement">
									<a id="publishingStartTimeButton"><img src="{@RELATIVE_WCF_DIR}icon/datePickerOptionsM.png" alt="" /></a>
									<div id="publishingStartTimeCalendar" class="inlineCalendar"></div>
									<script type="text/javascript">
										//<![CDATA[
										calendar.init('publishingStartTime');
										//]]>
									</script>
								</div>
								
								{if $errorField == 'publishingStartTime'}
									<p class="floatedElement innerError">
										{if $errorType == 'invalid'}{lang}wsip.acp.contentItem.publishingStartTime.error.invalid{/lang}{/if}
									</p>
								{/if}
							</div>
							
							<div class="formFieldDesc hidden" id="publishingStartTimeHelpMessage">
								<p>{lang}wsip.acp.contentItem.publishingStartTime.description{/lang}</p>
							</div>
						</fieldset>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('publishingStartTime');
				//]]></script>
				
				<div class="formGroup{if $errorField == 'publishingEndTime'} formError{/if}" id="publishingEndTimeDiv">
					<div class="formGroupLabel">
						<label>{lang}wsip.acp.contentItem.publishingEndTime{/lang}</label>
					</div>
					<div class="formGroupField">
						<fieldset>
							<legend><label>{lang}wsip.acp.contentItem.publishingEndTime{/lang}</label></legend>
							
							<div class="formField">
								<div class="floatedElement">
									<label for="publishingEndTimeDay">{lang}wcf.global.date.day{/lang}</label>
									{htmlOptions options=$dayOptions selected=$publishingEndTimeDay id=publishingEndTimeDay name=publishingEndTimeDay}
								</div>
								
								<div class="floatedElement">
									<label for="publishingEndTimeMonth">{lang}wcf.global.date.month{/lang}</label>
									{htmlOptions options=$monthOptions selected=$publishingEndTimeMonth id=publishingEndTimeMonth name=publishingEndTimeMonth}
								</div>
								
								<div class="floatedElement">
									<label for="publishingEndTimeYear">{lang}wcf.global.date.year{/lang}</label>
									<input id="publishingEndTimeYear" class="inputText fourDigitInput" type="text" name="publishingEndTimeYear" value="{@$publishingEndTimeYear}" maxlength="4" />
								</div>
								
								<div class="floatedElement">
									<label for="publishingEndTimeHour">{lang}wcf.global.date.hour{/lang}</label>
									{htmlOptions options=$hourOptions selected=$publishingEndTimeHour id=publishingEndTimeHour name=publishingEndTimeHour} :
								</div>
																	
								<div class="floatedElement">
									<label for="publishingEndTimeMinutes">{lang}wcf.global.date.minutes{/lang}</label>
									{htmlOptions options=$minuteOptions selected=$publishingEndTimeMinutes id=publishingEndTimeMinutes name=publishingEndTimeMinutes}
								</div>
								
								<div class="floatedElement">
									<a id="publishingEndTimeButton"><img src="{@RELATIVE_WCF_DIR}icon/datePickerOptionsM.png" alt="" /></a>
									<div id="publishingEndTimeCalendar" class="inlineCalendar"></div>
									<script type="text/javascript">
										//<![CDATA[
										calendar.init('publishingEndTime');
										//]]>
									</script>
								</div>
								
								{if $errorField == 'publishingEndTime'}
									<p class="floatedElement innerError">
										{if $errorType == 'invalid'}{lang}wsip.acp.contentItem.publishingEndTime.error.invalid{/lang}{/if}
									</p>
								{/if}
							</div>
							
							<div class="formFieldDesc hidden" id="publishingEndTimeHelpMessage">
								<p>{lang}wsip.acp.contentItem.publishingEndTime.description{/lang}</p>
							</div>
						</fieldset>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('publishingEndTime');
				//]]></script>
			</fieldset>
			
			<fieldset id="style">
				<legend>{lang}wsip.acp.contentItem.style{/lang}</legend>
				
				{if $availableStyles|count > 1}
					<div class="formElement" id="styleIDDiv">
						<div class="formFieldLabel">
							<label for="styleID">{lang}wsip.acp.contentItem.styleID{/lang}</label>
						</div>
						<div class="formField">
							<select name="styleID" id="styleID">
								<option value="0"></option>
								{htmlOptions options=$availableStyles selected=$styleID}
							</select>
							<label><input type="checkbox" name="enforceStyle" value="1" {if $enforceStyle}checked="checked" {/if}/> {lang}wsip.acp.contentItem.enforceStyle{/lang}</label>
						</div>
						<div class="formFieldDesc hidden" id="styleIDHelpMessage">
							<p>{lang}wsip.acp.contentItem.styleID.description{/lang}</p>
						</div>
					</div>
					<script type="text/javascript">//<![CDATA[
						inlineHelp.register('styleID');
					//]]></script>
				{/if}
				
				{if $boxLayoutOptions|count > 1}
					<div class="formElement" id="boxLayoutIDDiv">
						<div class="formFieldLabel">
							<label for="boxLayoutID">{lang}wsip.acp.contentItem.boxLayoutID{/lang}</label>
						</div>
						<div class="formField">
							<select name="boxLayoutID" id="boxLayoutID">
								<option value="0"></option>
								{htmlOptions options=$boxLayoutOptions selected=$boxLayoutID}
							</select>
						</div>
						<div class="formFieldDesc hidden" id="boxLayoutIDHelpMessage">
							<p>{lang}wsip.acp.contentItem.boxLayoutID.description{/lang}</p>
						</div>
					</div>
					<script type="text/javascript">//<![CDATA[
						inlineHelp.register('boxLayoutID');
					//]]></script>
				{/if}
				
				<div class="formElement" id="iconDiv">
					<div class="formFieldLabel">
						<label for="icon">{lang}wsip.acp.contentItem.icon{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="icon" name="icon" value="{$icon}" />
					</div>
					<div class="formFieldDesc hidden" id="iconHelpMessage">
						<p>{lang}wsip.acp.contentItem.icon.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('icon');
				//]]></script>
				
				{if $additionalStyleFields|isset}{@$additionalStyleFields}{/if}
			</fieldset>
				
			<fieldset id="permissions">
				<legend>{lang}wsip.acp.contentItem.permissions{/lang}</legend>
				
				<div class="formElement">
					<div class="formFieldLabel" id="permissionTitle">
						{lang}wsip.acp.contentItem.permissions.title{/lang}
					</div>
					<div class="formField"><div id="permission" class="accessRights"></div></div>
				</div>
				<div class="formElement">
					<div class="formField">	
						<input id="permissionAddInput" type="text" name="" value="" class="inputText accessRightsInput" />
						<script type="text/javascript">
							//<![CDATA[
							suggestion.setSource('index.php?page=PermissionsObjectsSuggest{@SID_ARG_2ND_NOT_ENCODED}');
							suggestion.enableIcon(true);
							suggestion.init('permissionAddInput');
							//]]>
						</script>
						<input id="permissionAddButton" type="button" value="{lang}wsip.acp.contentItem.permissions.add{/lang}" />
					</div>
				</div>
					
				<div class="formElement" style="display: none;">
					<div class="formFieldLabel">
						<div id="permissionSettingsTitle" class="accessRightsTitle"></div>
					</div>
					<div class="formField">
						<div id="permissionHeader" class="accessRightsHeader">
							<span class="deny">{lang}wsip.acp.contentItem.permissions.deny{/lang}</span>
							<span class="allow">{lang}wsip.acp.contentItem.permissions.allow{/lang}</span>
						</div>
						<div id="permissionSettings" class="accessRights"></div>
					</div>
				</div>
				
				{if $additionalPermissionFields|isset}{@$additionalPermissionFields}{/if}
			</fieldset>
			
			{if $additionalFields|isset}{@$additionalFields}{/if}
		</div>
	</div>
	
	<div class="formSubmit">
		<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
		<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
 		{@SID_INPUT_TAG}
 		{if $contentItemID|isset}<input type="hidden" name="contentItemID" value="{@$contentItemID}" />{/if}
 	</div>
</form>

{include file='footer'}