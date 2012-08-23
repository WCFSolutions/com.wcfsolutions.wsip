{include file="documentHeader"}
<head>
	<title>{lang}wsip.index.title{/lang} - {lang}{PAGE_TITLE}{/lang}</title>

	{include file='headInclude' sandbox=false}
	{include file='imageViewer'}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">

	<div class="mainHeadline">
		<img src="{icon}indexL.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}{PAGE_TITLE}{/lang}</h2>
			<p>{lang}{PAGE_DESCRIPTION}{/lang}</p>
		</div>
	</div>

	{if $userMessages|isset}{@$userMessages}{/if}

	{if $additionalTopContents|isset}{@$additionalTopContents}{/if}

	{include file='boxList' boxPosition='index'}

	{if $additionalBottomContents|isset}{@$additionalBottomContents}{/if}

</div>

{include file='footer' sandbox=false}

</body>
</html>