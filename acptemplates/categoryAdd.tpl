{include file='header'}

<div class="mainHeadline">
	<img src="{@RELATIVE_WSIP_DIR}icon/category{@$action|ucfirst}L.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wsip.acp.category.{@$action}{/lang}</h2>
		{if $categoryID|isset}<p>{lang}{$category->title}{/lang}</p>{/if}
	</div>
</div>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
	<p class="success">{if $action == 'add'}{lang}wsip.acp.category.add.success{/lang}{else}{lang}wsip.acp.category.edit.success{/lang}{/if}</p>
{/if}

<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/Suggestion.class.js"></script>
<script type="text/javascript" src="{@RELATIVE_WSIP_DIR}acp/js/PermissionList.class.js"></script>
<script type="text/javascript">
	//<![CDATA[
	var language = new Object();
	language['wsip.acp.category.permissions.permissionsFor'] = '{staticlang}wsip.acp.category.permissions.permissionsFor{/staticlang}';
	language['wsip.acp.category.permissions.fullControl'] = '{lang}wsip.acp.category.permissions.fullControl{/lang}';
	{foreach from=$moderatorSettings item=moderatorSetting}
		language['wsip.acp.category.permissions.{@$moderatorSetting}'] = '{lang}wsip.acp.category.permissions.{@$moderatorSetting}{/lang}';
	{/foreach}
	{foreach from=$permissionSettings item=permissionSetting}
		language['wsip.acp.category.permissions.{@$permissionSetting}'] = '{lang}wsip.acp.category.permissions.{@$permissionSetting}{/lang}';
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

	var moderators = new Hash();
	{assign var=i value=0}
	{foreach from=$moderators item=moderator}
		var settings = new Hash();
		settings.set('fullControl', -1);

		{foreach from=$moderator.settings key=setting item=value}
			{if $setting != 'name' && $setting != 'type' && $setting != 'id'}
				settings.set('{@$setting}', {@$value});
			{/if}
		{/foreach}

		moderators.set({@$i}, {
			'name': '{@$moderator.name|encodeJS}',
			'type': '{@$moderator.type}',
			'id': '{@$moderator.id}',
			'settings': settings
		});

		{assign var=i value=$i+1}
	{/foreach}

	var permissionSettings = new Array({implode from=$permissionSettings item=permissionSetting}'{@$permissionSetting}'{/implode});
	var moderatorSettings = new Array({implode from=$moderatorSettings item=moderatorSetting}'{@$moderatorSetting}'{/implode});

	document.observe("dom:loaded", function() {
		// user/group permissions
		var permissionList = new PermissionList('permission', 'category', permissions, permissionSettings);

		// moderators
		var moderatorPermissionList = new PermissionList('moderator', 'category', moderators, moderatorSettings);

		// add onsubmit event
		$('categoryAddForm').onsubmit = function() {
			if (suggestion.selectedIndex != -1) return false;
			if (permissionList.inputHasFocus || moderatorPermissionList.inputHasFocus) return false;
			permissionList.submit(this); moderatorPermissionList.submit(this);
		};

	});
	//]]>
</script>

<div class="contentHeader">
	<div class="largeButtons">
		<ul><li><a href="index.php?page=CategoryList&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}wsip.acp.menu.link.content.category.view{/lang}"><img src="{@RELATIVE_WSIP_DIR}icon/categoryM.png" alt="" /> <span>{lang}wsip.acp.menu.link.content.category.view{/lang}</span></a></li></ul>
	</div>
</div>

{if $categoryID|isset && $categoryQuickJumpOptions|count > 1}
	<fieldset>
		<legend>{lang}wsip.acp.category.edit{/lang}</legend>
		<div class="formElement">
			<div class="formFieldLabel">
				<label for="categoryChange">{lang}wsip.acp.category.edit{/lang}</label>
			</div>
			<div class="formField">
				<select id="categoryChange" onchange="document.location.href=fixURL('index.php?form=CategoryEdit&amp;categoryID='+this.options[this.selectedIndex].value+'&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}')">
					{htmloptions options=$categoryQuickJumpOptions selected=$categoryID disableEncoding=true}
				</select>
			</div>
		</div>
	</fieldset>
{/if}

<form method="post" action="index.php?form=Category{@$action|ucfirst}" id="categoryAddForm">

	<div class="border content">
		<div class="container-1">
			<fieldset>
				<legend>{lang}wsip.acp.category.general{/lang}</legend>

				{if $action == 'edit'}
					<div class="formElement" id="languageIDDiv">
						<div class="formFieldLabel">
							<label for="languageID">{lang}wsip.acp.category.language{/lang}</label>
						</div>
						<div class="formField">
							<select name="languageID" id="languageID" onchange="location.href='index.php?form=CategoryEdit&amp;categoryID={@$categoryID}&amp;languageID='+this.value+'&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}'">
								{foreach from=$languages key=availableLanguageID item=languageCode}
									<option value="{@$availableLanguageID}"{if $availableLanguageID == $languageID} selected="selected"{/if}>{lang}wcf.global.language.{@$languageCode}{/lang}</option>
								{/foreach}
							</select>
						</div>
						<div class="formFieldDesc hidden" id="languageIDHelpMessage">
							{lang}wsip.acp.category.language.description{/lang}
						</div>
					</div>
					<script type="text/javascript">//<![CDATA[
						inlineHelp.register('languageID');
					//]]></script>
				{/if}

				<div class="formElement{if $errorField == 'title'} formError{/if}">
					<div class="formFieldLabel">
						<label for="title">{lang}wsip.acp.category.title{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="title" name="title" value="{$title}" />
						{if $errorField == 'title'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
				</div>

				<div id="descriptionDiv" class="formElement">
					<div class="formFieldLabel">
						<label for="description">{lang}wsip.acp.category.description{/lang}</label>
					</div>
					<div class="formField">
						<textarea id="description" name="description" cols="40" rows="10">{$description}</textarea>
						<label><input type="checkbox" name="allowDescriptionHtml" value="1" {if $allowDescriptionHtml}checked="checked" {/if}/> {lang}wsip.acp.category.allowDescriptionHtml{/lang}</label>
					</div>
				</div>

				{if $additionalGeneralFields|isset}{@$additionalGeneralFields}{/if}
			</fieldset>

			<fieldset>
				<legend>{lang}wsip.acp.category.classification{/lang}</legend>

				<div class="formElement{if $errorField == 'publicationTypes'} formError{/if}">
					<div class="formFieldLabel">
						<label for="publicationTypes">{lang}wsip.acp.category.publicationTypes{/lang}</label>
					</div>
					<div class="formField">
						{foreach from=$publicationTypeSettings key=publicationType item=publicationTypeObj}
							<label><input type="checkbox" name="publicationTypes[]" value="{@$publicationType}"{if $publicationType|in_array:$publicationTypes} checked="checked"{/if} /> {lang}wsip.publication.type.{@$publicationType}{/lang}</label>
						{/foreach}
						{if $errorField == 'publicationTypes'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
					<div class="formFieldDesc hidden" id="publicationTypesHelpMessage">
						<p>{lang}wsip.acp.category.publicationTypes.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('publicationTypes');
				//]]></script>

				{if $categoryOptions|count > 0}
					<div class="formElement{if $errorField == 'parentID'} formError{/if}" id="parentIDDiv">
						<div class="formFieldLabel">
							<label for="parentID">{lang}wsip.acp.category.parentID{/lang}</label>
						</div>
						<div class="formField">
							<select name="parentID" id="parentID">
								<option value="0"></option>
								{htmlOptions options=$categoryOptions disableEncoding=true selected=$parentID}
							</select>
							{if $errorField == 'parentID'}
								<p class="innerError">
									{if $errorType == 'invalid'}{lang}wsip.acp.category.error.parentID.invalid{/lang}{/if}
								</p>
							{/if}
						</div>
						<div class="formFieldDesc hidden" id="parentIDHelpMessage">
							<p>{lang}wsip.acp.category.parentID.description{/lang}</p>
						</div>
					</div>
					<script type="text/javascript">//<![CDATA[
						inlineHelp.register('parentID');
					//]]></script>
				{/if}

				<div class="formElement" id="showOrderDiv">
					<div class="formFieldLabel">
						<label for="showOrder">{lang}wsip.acp.category.showOrder{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" name="showOrder" id="showOrder" value="{$showOrder}" />
					</div>
					<div class="formFieldDesc hidden" id="showOrderHelpMessage">
						{lang}wsip.acp.category.showOrder.description{/lang}
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('showOrder');
				//]]></script>

				{if $additionalClassificationFields|isset}{@$additionalClassificationFields}{/if}
			</fieldset>

			<fieldset id="permissions">
				<legend>{lang}wsip.acp.category.permissions{/lang}</legend>

				<div class="formElement">
					<div class="formFieldLabel" id="permissionTitle">
						{lang}wsip.acp.category.permissions.title{/lang}
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
						<input id="permissionAddButton" type="button" value="{lang}wsip.acp.category.permissions.add{/lang}" />
					</div>
				</div>

				<div class="formElement" style="display: none;">
					<div class="formFieldLabel">
						<div id="permissionSettingsTitle" class="accessRightsTitle"></div>
					</div>
					<div class="formField">
						<div id="permissionHeader" class="accessRightsHeader">
							<span class="deny">{lang}wsip.acp.category.permissions.deny{/lang}</span>
							<span class="allow">{lang}wsip.acp.category.permissions.allow{/lang}</span>
						</div>
						<div id="permissionSettings" class="accessRights"></div>
					</div>
				</div>

				{if $additionalPermissionFields|isset}{@$additionalPermissionFields}{/if}
			</fieldset>

			<fieldset id="moderators">
				<legend>{lang}wsip.acp.category.moderators{/lang}</legend>

				<div class="formElement">
					<div class="formFieldLabel" id="moderatorTitle">
						{lang}wsip.acp.category.permissions.title{/lang}
					</div>
					<div class="formField"><div id="moderator" class="accessRights"></div></div>
				</div>
				<div class="formElement">
					<div class="formField">
						<input id="moderatorAddInput" type="text" name="" value="" class="inputText accessRightsInput" />
						<script type="text/javascript">
							//<![CDATA[
							suggestion.init('moderatorAddInput');
							//]]>
						</script>
						<input id="moderatorAddButton" type="button" value="{lang}wsip.acp.category.permissions.add{/lang}" />
					</div>
				</div>

				<div class="formElement" style="display: none;">
					<div class="formFieldLabel">
						<div id="moderatorSettingsTitle" class="accessRightsTitle"></div>
					</div>
					<div class="formField">
						<div id="moderatorHeader" class="accessRightsHeader">
							<span class="deny">{lang}wsip.acp.category.permissions.deny{/lang}</span>
							<span class="allow">{lang}wsip.acp.category.permissions.allow{/lang}</span>
						</div>
						<div id="moderatorSettings" class="accessRights"></div>
					</div>
				</div>

				{if $additionalModeratorFields|isset}{@$additionalModeratorFields}{/if}
			</fieldset>

			{if $additionalFields|isset}{@$additionalFields}{/if}
		</div>
	</div>

	<div class="formSubmit">
		<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
		<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
 		{@SID_INPUT_TAG}
 		{if $categoryID|isset}<input type="hidden" name="categoryID" value="{@$categoryID}" />{/if}
 	</div>
</form>

{include file='footer'}