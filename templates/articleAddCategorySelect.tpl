{include file="documentHeader"}
<head>
	<title>{lang}wsip.article.add{/lang} - {lang}{PAGE_TITLE}{/lang}</title>

	{include file='headInclude' sandbox=false}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">

	<ul class="breadCrumbs">
		<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img src="{icon}indexS.png{/icon}" alt="" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
		<li><a href="index.php?page=ArticleOverview{@SID_ARG_2ND}"><img src="{icon}articleS.png{/icon}" alt="" /> <span>{lang}wsip.article.overview{/lang}</span></a> &raquo;</li>
	</ul>

	<div class="mainHeadline">
		<img src="{icon}articleAddL.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}wsip.article.add{/lang}</h2>
		</div>
	</div>

	{if $userMessages|isset}{@$userMessages}{/if}

	<form method="get" action="index.php">
		<div class="border content">
			<div class="container-1">

				<fieldset>
					<legend>{lang}wsip.article.add{/lang}</legend>

					<div class="formElement">
						<div class="formFieldLabel">
							<label for="languageID">{lang}wsip.article.category{/lang}</label>
						</div>
						<div class="formField">
							<select name="categoryID" id="categoryID" tabindex="{counter name='tabindex'}">
								{htmlOptions options=$categoryOptions disableEncoding=true}
							</select>
						</div>
					</div>
				</fieldset>

				{if $additionalFields|isset}{@$additionalFields}{/if}
			</div>
		</div>

		<div class="formSubmit">
			<input type="submit" accesskey="s" value="{lang}wcf.global.button.next{/lang}" tabindex="{counter name='tabindex'}" />
			<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" tabindex="{counter name='tabindex'}" />
			<input type="hidden" name="form" value="ArticleAdd" />
			{@SID_INPUT_TAG}
		</div>
	</form>

</div>

{include file='footer' sandbox=false}
</body>
</html>