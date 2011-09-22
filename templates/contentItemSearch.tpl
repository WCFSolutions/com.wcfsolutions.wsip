{include file="documentHeader"}
<head>
	<title>{lang}wsip.contentItem.search{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	
	{include file='headInclude' sandbox=false}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{* --- quick search controls --- *}
{assign var='searchScript' value='index.php?form=ContentItemSearch'}
{assign var='searchFieldTitle' value='{lang}wsip.contentItem.search.query.title{/lang}'}
{assign var='searchShowExtendedLink' value=false}
{assign var='searchFieldOptions' value=false}
{* --- end --- *}
{include file='header' sandbox=false}

<div id="main">
	
	<ul class="breadCrumbs">
		<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img src="{icon}indexS.png{/icon}" alt="" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
	</ul>
	
	<div class="mainHeadline">
		<img src="{icon}contentItemOverviewL.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}wsip.contentItem.overview{/lang}</h2>
		</div>
	</div>
	
	{if $userMessages|isset}{@$userMessages}{/if}
	
	{if $errorField}
		<p class="error">{lang}wcf.global.form.error{/lang}</p>
	{/if}
	
	{if $errorMessage|isset}
		<p class="error">{@$errorMessage}</p>
	{/if}
	
	<div class="tabMenu">
		<ul>
			<li><a href="index.php?page=ContentItemOverview{@SID_ARG_2ND}"><img src="{icon}contentItemM.png{/icon}" alt="" /> <span>{lang}wsip.contentItem.overview{/lang}</span></a></li>
			<li class="activeTabMenu"><a href="index.php?form=ContentItemSearch{@SID_ARG_2ND}"><img src="{icon}searchM.png{/icon}" alt="" /> <span>{lang}wsip.contentItem.search{/lang}</span></a></li>
			{if $additionalTabs|isset}{@$additionalTabs}{/if}
		</ul>
	</div>
	<div class="subTabMenu">
		<div class="containerHead"> </div>
	</div>
	
	<form method="post" action="index.php?form=ContentItemSearch">
		<div class="border tabMenuContent">
			<div class="container-1">
				<h3 class="subHeadline">{lang}wsip.contentItem.search{/lang}</h3>
				
				<fieldset>
					<legend>{lang}wsip.contentItem.search{/lang}</legend>
					
					<div class="formElement{if $errorField == 'query'} formError{/if}">
						<div class="formFieldLabel">
							<label for="searchTerm">{lang}wsip.contentItem.search.query{/lang}</label>
						</div>
						<div class="formField">
							<input type="text" class="inputText" id="searchTerm" name="q" value="{$query}" maxlength="255" />
							{if $errorField == 'query'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
									{if $errorType == 'invalid'}{lang}wsip.contentItem.search.query.error.invalid{/lang}{/if}
								</p>
							{/if}
						</div>
					</div>
					
					{if $additionalFields|isset}{@$additionalFields}{/if}
				</fieldset>
			</div>
		</div>
		
		<div class="formSubmit">
			<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
			<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
			{@SID_INPUT_TAG}
		</div>
	</form>
	
</div>

{include file='footer' sandbox=false}

</body>
</html>