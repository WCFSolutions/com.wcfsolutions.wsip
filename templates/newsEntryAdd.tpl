{include file="documentHeader"}
<head>
	<title>{lang}wsip.news.entry.{@$action}{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	
	{include file='headInclude' sandbox=false}
	{include file='imageViewer'}
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabbedPane.class.js"></script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/Calendar.class.js"></script>
	{if $action == 'add'}
		<script type="text/javascript">
			//<![CDATA[
			var calendar = new Calendar('{$monthList}', '{$weekdayList}', {@$startOfWeek});
			
			document.observe('dom:loaded', function() {
				var checkbox = $('disableEntry');
				if (checkbox) {
					checkbox.observe('change', function() {
						if (this.checked) {
							enableOptions('publishingTime');
						}
						else {
							disableOptions('publishingTime');
						}
					});
				}
				{if !$disableEntry}
					disableOptions('publishingTime');
				{/if}
			});
			//]]>
		</script>
	{/if}
	{if $canUseBBCodes}{include file="wysiwyg"}{/if}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">
	
	<ul class="breadCrumbs">
		<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img src="{icon}indexS.png{/icon}" alt="" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
		<li><a href="index.php?page=NewsOverview{@SID_ARG_2ND}"><img src="{icon}newsS.png{/icon}" alt="" /> <span>{lang}wsip.news.overview{/lang}</span></a> &raquo;</li>
		{foreach from=$category->getParentCategories() item=parentCategory}
			<li><a href="index.php?page=NewsOverview&amp;categoryID={@$parentCategory->categoryID}{@SID_ARG_2ND}"><img src="{icon}categoryS.png{/icon}" alt="" /> <span>{$parentCategory->getTitle()}</span></a> &raquo;</li>
		{/foreach}
		<li><a href="index.php?page=NewsOverview&amp;categoryID={@$category->categoryID}{@SID_ARG_2ND}"><img src="{icon}categoryS.png{/icon}" alt="" /> <span>{$category->getTitle()}</span></a> &raquo;</li>
		{if $entry|isset}<li><a href="index.php?page=NewsEntry&amp;entryID={@$entry->entryID}{@SID_ARG_2ND}"><img src="{icon}newsEntryS.png{/icon}" alt="" /> <span>{$entry->subject}</span></a> &raquo;</li>{/if}
	</ul>
	
	<div class="mainHeadline">
		<img src="{icon}newsEntry{@$action|ucfirst}L.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}wsip.news.entry.{@$action}{/lang}</h2>
		</div>
	</div>
	
	{if $userMessages|isset}{@$userMessages}{/if}
	
	{if $errorField}
		<p class="error">{lang}wcf.global.form.error{/lang}</p>
	{/if}
	
	{if $preview|isset}
		<div class="border messagePreview">
			<div class="containerHead">
				<h3>{lang}wcf.message.preview{/lang}</h3>
			</div>
			<div class="message content">
				<div class="messageInner container-1">
					{if $subject}
						<h4>{$subject}</h4>
					{/if}
					<div class="messageBody">
						<div>{@$preview}</div>
					</div>
				</div>
			</div>
		</div>
	{/if}
	
	<form method="post" enctype="multipart/form-data" action="index.php?form=NewsEntry{@$action|ucfirst}{if $action == 'add'}&amp;categoryID={@$categoryID}{elseif $action == 'edit'}&amp;entryID={@$entryID}{/if}">
		<div class="border content">
			<div class="container-1">
				{if $entry|isset && $entry->isDeletable($category)}
					<fieldset>
						<legend><label for="sure"{if $errorField == 'sure'} class="formError"{/if}><input id="sure" type="checkbox" name="sure" value="1" tabindex="{counter name='tabindex'}" onclick="openList('deleteEntry')" /> {lang}wsip.news.entry.delete{/lang}</label></legend>
						<div id="deleteEntry">
							<div class="formElement {if $errorField == 'sure'} formError{/if}">
								{if $errorField == 'sure'}
									<p class="innerError">{lang}wsip.news.entry.delete.error{/lang}</p>
								{/if}
							</div>
							{if !$entry->isDeleted && NEWS_ENTRY_ENABLE_RECYCLE_BIN}
								<div class="formElement">
									<div class="formFieldLabel">
										<label for="deleteReason">{lang}wsip.news.entry.delete.reason{/lang}</label>
									</div>
									<div class="formField">
										<textarea name="deleteReason" id="deleteReason" rows="5" cols="40">{$deleteReason}</textarea>
									</div>
								</div>
							{/if}
							<div class="formElement">
								<div class="formField">
									<input type="submit" name="send" value="{lang}wcf.global.button.submit{/lang}" class="hidden" />
									<input type="submit" name="deleteEntry" value="{lang}wcf.global.button.delete{/lang}" tabindex="{counter name='tabindex'}" />
								</div>
							</div>
							<script type="text/javascript">
								//<![CDATA[
								document.observe('dom:loaded', function() { $('deleteEntry').setStyle({ 'display' : 'none' }); });
								//]]>
							</script>
						</div>
					</fieldset>
				{/if}
				
				<fieldset>
					<legend>{lang}wsip.news.entry.information{/lang}</legend>
					
					{if $availableLanguages|count > 1}
						<div class="formElement">
							<div class="formFieldLabel">
								<label for="languageID">{lang}wsip.news.entry.language{/lang}</label>
							</div>
							<div class="formField">
								<select name="languageID" id="languageID" tabindex="{counter name='tabindex'}">
									{foreach from=$availableLanguages item=availableLanguage}
									<option value="{@$availableLanguage.languageID}"
										{if $availableLanguage.languageID == $languageID} selected="selected"{/if}>{lang}wcf.global.language.{@$availableLanguage.languageCode}{/lang}</option>
									{/foreach}
								</select>
							</div>
						</div>
					{/if}
					
					{if $action == 'add' && !$this->user->userID}
						<div class="formElement{if $errorField == 'username'} formError{/if}">
							<div class="formFieldLabel">
								<label for="username">{lang}wcf.user.username{/lang}</label>
							</div>
							<div class="formField">
								<input type="text" class="inputText" name="username" id="username" value="{$username}" tabindex="{counter name='tabindex'}" />
								{if $errorField == 'username'}
									<p class="innerError">
										{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
										{if $errorType == 'notValid'}{lang}wcf.user.error.username.notValid{/lang}{/if}
										{if $errorType == 'notAvailable'}{lang}wcf.user.error.username.notUnique{/lang}{/if}
									</p>
								{/if}
							</div>
						</div>
					{/if}
					
					<div class="formElement{if $errorField == 'subject'} formError{/if}">
						<div class="formFieldLabel">
							<label for="subject">{lang}wsip.news.entry.subject{/lang}</label>
						</div>
						<div class="formField">
							<input type="text" class="inputText" name="subject" id="subject" value="{$subject}" tabindex="{counter name='tabindex'}" />
							{if $errorField == 'subject'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								</p>
							{/if}
						</div>
					</div>
					
					<div class="formElement{if $errorField == 'teaser'} formError{/if}">
						<div class="formFieldLabel">
							<label for="teaser">{lang}wsip.news.entry.teaser{/lang}</label>
						</div>
						<div class="formField">
							<textarea id="teaser" name="teaser" rows="5" cols="40" tabindex="{counter name='tabindex'}">{@$teaser}</textarea>
							{if $errorField == 'teaser'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
									{if $errorType == 'tooLong'}{lang}wsip.news.entry.teaser.error.tooLong{/lang}{/if}
								</p>
							{/if}
						</div>
					</div>
					
					{if MODULE_TAGGING && NEWS_ENTRY_ENABLE_TAGS && $category->getPermission('canSetNewsTags')}{include file='tagAddBit'}{/if}
					
					{if $additionalInformationFields|isset}{@$additionalInformationFields}{/if}
				</fieldset>
				
				{if ($action == 'add' || !$entry->everEnabled) && $category->getModeratorPermission('canEnableNewsEntry')}
					<fieldset>
						<legend>{lang}wsip.news.entry.publishing{/lang}</legend>
						
						{if $action == 'add'}
							<div class="formElement">
								<div class="formField">
									<label><input type="checkbox" name="disableEntry" id="disableEntry" value="1" {if $disableEntry == 1}checked="checked" {/if}/> {lang}wsip.news.entry.disableEntry{/lang}</label>
								</div>
								<div class="formFieldDesc">
									<p>{lang}wsip.news.entry.disableEntry.description{/lang}</p>
								</div>
							</div>
						{/if}
											
						<div class="formGroup{if $errorField == 'publishingTime'} formError{/if}" id="publishingTimeDiv">
							<div class="formGroupLabel">
								<label>{lang}wsip.news.entry.publishingTime{/lang}</label>
							</div>
							<div class="formGroupField">
								<fieldset>
									<legend><label>{lang}wsip.news.entry.publishingTime{/lang}</label></legend>
						
									<div class="formField">
										<div class="floatedElement">
											<label for="publishingTimeDay">{lang}wcf.global.date.day{/lang}</label>
											{htmlOptions options=$dayOptions selected=$publishingTimeDay id=publishingTimeDay name=publishingTimeDay}
										</div>
										
										<div class="floatedElement">
											<label for="publishingTimeMonth">{lang}wcf.global.date.month{/lang}</label>
											{htmlOptions options=$monthOptions selected=$publishingTimeMonth id=publishingTimeMonth name=publishingTimeMonth}
										</div>
										
										<div class="floatedElement">
											<label for="publishingTimeYear">{lang}wcf.global.date.year{/lang}</label>
											<input id="publishingTimeYear" class="inputText fourDigitInput" type="text" name="publishingTimeYear" value="{@$publishingTimeYear}" maxlength="4" />
										</div>
										
										<div class="floatedElement">
											<label for="publishingTimeHour">{lang}wcf.global.date.hour{/lang}</label>
											{htmlOptions options=$hourOptions selected=$publishingTimeHour id=publishingTimeHour name=publishingTimeHour}
										</div>
										
										<div class="floatedElement">
											<a id="publishingTimeButton"><img src="{@RELATIVE_WCF_DIR}icon/datePickerOptionsM.png" alt="" /></a>
											<div id="publishingTimeCalendar" class="inlineCalendar"></div>
											<script type="text/javascript">
												//<![CDATA[
												calendar.init('publishingTime');
												//]]>
											</script>
										</div>
										
										{if $errorField == 'publishingTime'}
											<p class="floatedElement innerError">
												{if $errorType == 'invalid'}{lang}wsip.news.entry.publishingTime.error.invalid{/lang}{/if}
											</p>
										{/if}
									</div>
									<div class="formFieldDesc">
										<p>{lang}wsip.news.entry.publishingTime.description{/lang}</p>
									</div>
								</fieldset>
							</div>
						</div>
					</fieldset>
				{/if}
				
				<fieldset>
					<legend>{lang}wsip.news.entry.text{/lang}</legend>
					
					<div class="editorFrame formElement{if $errorField == 'text'} formError{/if}" id="textDiv">	
						<div class="formFieldLabel">
							<label for="text">{lang}wsip.news.entry.text{/lang}</label>
						</div>
						
						<div class="formField">				
							<textarea name="text" id="text" rows="15" cols="40" tabindex="{counter name='tabindex'}">{$text}</textarea>
							{if $errorField == 'text'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
									{if $errorType == 'tooLong'}{lang}wcf.message.error.tooLong{/lang}{/if}
									{if $errorType == 'censoredWordsFound'}{lang}wcf.message.error.censoredWordsFound{/lang}{/if}
								</p>
							{/if}
						</div>					
					</div>
					
					{capture append=additionalSettings}							
						<div class="formField">
							<label><input type="checkbox" name="enableComments" value="1" {if $enableComments == 1}checked="checked" {/if}/> {lang}wsip.news.entry.enableComments{/lang}</label>
						</div>
						<div class="formFieldDesc">
							<p>{lang}wsip.news.entry.enableComments.description{/lang}</p>
						</div>
					{/capture}
					{include file='messageFormTabs'}	
				</fieldset>
				
				{include file='captcha'}
				{if $additionalFields|isset}{@$additionalFields}{/if}
			</div>
		</div>
		
		<div class="formSubmit">
			<input type="submit" name="send" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" tabindex="{counter name='tabindex'}" />
			<input type="submit" name="preview" accesskey="p" value="{lang}wcf.global.button.preview{/lang}" tabindex="{counter name='tabindex'}" />
			<input type="reset" name="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" tabindex="{counter name='tabindex'}" />
			{@SID_INPUT_TAG}
			<input type="hidden" name="idHash" value="{$idHash}" />
		</div>
	</form>

</div>

{include file='footer' sandbox=false}
</body>
</html>