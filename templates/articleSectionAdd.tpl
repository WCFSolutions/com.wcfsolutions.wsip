{include file="documentHeader"}
<head>
	<title>{lang}wsip.article.section.{@$action}{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	
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
		<li><a href="index.php?page=Article&amp;sectionID={@$article->firstSectionID}{@SID_ARG_2ND}"><img src="{icon}articleS.png{/icon}" alt="" /> <span>{$article->subject}</span></a> &raquo;</li>
	</ul>
	
	<div class="mainHeadline">
		<img src="{icon}article{@$action|ucfirst}L.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}wsip.article.section.{@$action}{/lang}</h2>
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
	
	<form enctype="multipart/form-data" method="post" action="index.php?form=ArticleSection{@$action|ucfirst}{if $action == 'add'}&amp;articleID={@$articleID}{elseif $action == 'edit'}&amp;sectionID={@$sectionID}{/if}">
		<div class="border content">
			<div class="container-1">
				
				{if $action == 'edit' && $article->firstSectionID == $section->sectionID && $categoryOptions|count > 0}
					<fieldset>
						<legend>{lang}wsip.article.move{/lang}</legend>
						
						<div class="formElement{if $errorField == 'categoryID'} formError{/if}">
							<div class="formFieldLabel">
								<label for="categoryID">{lang}wsip.article.categoryID{/lang}</label>
							</div>
							<div class="formField">
								<select name="categoryID" id="categoryID">
									{htmlOptions options=$categoryOptions disableEncoding=true selected=$categoryID}
								</select>
								{if $errorField == 'categoryID'}
									<p class="innerError">
										{if $errorType == 'invalid'}{lang}wsip.article.categoryID.invalid{/lang}{/if}
									</p>
								{/if}
							</div>
						</div>
						
						<div class="formElement">
							<div class="formField">
								<input type="submit" name="send" value="{lang}wcf.global.button.submit{/lang}" class="hidden" />
								<input type="submit" name="moveGallery" value="{lang}wsip.article.button.move{/lang}" tabindex="{counter name='tabindex'}" />
							</div>
						</div>
					</fieldset>
				{/if}
				
				<fieldset>
					<legend>{lang}wsip.article.information{/lang}</legend>
					
					{if $action == 'edit' && $article->firstSectionID == $section->sectionID && $availableLanguages|count > 1}
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
					
					{if $action == 'edit' && $article->firstSectionID == $section->sectionID}
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
					{/if}
					
					{if $sectionOptions|count > 0}
						<div class="formElement{if $errorField == 'parentSectionID'} formError{/if}">
							<div class="formFieldLabel">
								<label for="parentID">{lang}wsip.article.section.parentSectionID{/lang}</label>
							</div>
							<div class="formField">
								<select name="parentSectionID" id="parentSectionID">
									<option value="0"></option>
									{htmlOptions options=$sectionOptions disableEncoding=true selected=$parentSectionID}
								</select>
								{if $errorField == 'parentSectionID'}
									<p class="innerError">
										{if $errorType == 'invalid'}{lang}wsip.article.section.error.parentSectionID.invalid{/lang}{/if}
									</p>
								{/if}
							</div>
						</div>
					{/if}
					
					{if MODULE_TAGGING && ARTICLE_ENABLE_TAGS && $action == 'edit' && $article->firstSectionID == $section->sectionID && $category->getPermission('canSetArticleTags')}{include file='tagAddBit'}{/if}
					
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
					
					{if $action == 'edit' && $article->firstSectionID == $section->sectionID}
						{capture append=additionalSettings}	
							<div class="formField">
								<label><input type="checkbox" name="enableComments" value="1" {if $enableComments == 1}checked="checked" {/if}/> {lang}wsip.article.enableComments{/lang}</label>
							</div>
							<div class="formFieldDesc">
								<p>{lang}wsip.article.enableComments.description{/lang}</p>
							</div>
						{/capture}
					{/if}
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