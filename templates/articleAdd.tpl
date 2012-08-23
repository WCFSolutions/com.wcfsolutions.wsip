{include file="documentHeader"}
<head>
	<title>{lang}wsip.article.add{/lang} - {lang}{PAGE_TITLE}{/lang}</title>

	{include file='headInclude' sandbox=false}
	{include file='imageViewer'}
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabbedPane.class.js"></script>
	{if $canUseBBCodes}{include file="wysiwyg"}{/if}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">

	<ul class="breadCrumbs">
		<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img src="{icon}indexS.png{/icon}" alt="" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
		<li><a href="index.php?page=ArticleOverview{@SID_ARG_2ND}"><img src="{icon}articleS.png{/icon}" alt="" /> <span>{lang}wsip.article.overview{/lang}</span></a> &raquo;</li>
		{foreach from=$category->getParentCategories() item=parentCategory}
			<li><a href="index.php?page=ArticleOverview&amp;categoryID={@$parentCategory->categoryID}{@SID_ARG_2ND}"><img src="{icon}categoryS.png{/icon}" alt="" /> <span>{$parentCategory->getTitle()}</span></a> &raquo;</li>
		{/foreach}
		<li><a href="index.php?page=ArticleOverview&amp;categoryID={@$category->categoryID}{@SID_ARG_2ND}"><img src="{icon}categoryS.png{/icon}" alt="" /> <span>{$category->getTitle()}</span></a> &raquo;</li>
	</ul>

	<div class="mainHeadline">
		<img src="{icon}articleAddL.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}wsip.article.add{/lang}</h2>
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

	<form enctype="multipart/form-data" method="post" action="index.php?form=ArticleAdd&amp;categoryID={@$categoryID}">
		<div class="border content">
			<div class="container-1">
				<fieldset>
					<legend>{lang}wsip.article.information{/lang}</legend>

					{if $availableLanguages|count > 1}
						<div class="formElement">
							<div class="formFieldLabel">
								<label for="languageID">{lang}wsip.article.language{/lang}</label>
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

					{if !$this->user->userID}
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
							<label for="subject">{lang}wsip.article.subject{/lang}</label>
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
							<label for="teaser">{lang}wsip.article.teaser{/lang}</label>
						</div>
						<div class="formField">
							<textarea id="teaser" name="teaser" rows="5" cols="40" tabindex="{counter name='tabindex'}">{@$teaser}</textarea>
							{if $errorField == 'teaser'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
									{if $errorType == 'tooLong'}{lang}wsip.article.teaser.error.tooLong{/lang}{/if}
								</p>
							{/if}
						</div>
					</div>

					{if MODULE_TAGGING && ARTICLE_ENABLE_TAGS && $category->getPermission('canSetArticleTags')}{include file='tagAddBit'}{/if}

					{if $additionalInformationFields|isset}{@$additionalInformationFields}{/if}
				</fieldset>

				<fieldset>
					<legend>{lang}wsip.article.text{/lang}</legend>

					<div class="editorFrame formElement{if $errorField == 'text'} formError{/if}" id="textDiv">
						<div class="formFieldLabel">
							<label for="text">{lang}wsip.article.text{/lang}</label>
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
							<label><input type="checkbox" name="enableComments" value="1" {if $enableComments == 1}checked="checked" {/if}/> {lang}wsip.article.enableComments{/lang}</label>
						</div>
						<div class="formFieldDesc">
							<p>{lang}wsip.article.enableComments.description{/lang}</p>
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