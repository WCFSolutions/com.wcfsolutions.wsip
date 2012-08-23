{include file="documentHeader"}
<head>
	<title>{lang}wsip.contentItem.search.results{/lang} - {lang}{PAGE_TITLE}{/lang}</title>

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

	<div class="border tabMenuContent">
		<div class="container-1">
			<h3 class="subHeadline">{lang}wsip.contentItem.search.results{/lang}</h3>

			{foreach from=$contentItems item=contentItem}
				<div class="message content">
					 <div class="messageInner container-{cycle name='results' values='1,2'}">
						<h3><a href="index.php?page=ContentItem&amp;contentItemID={@$contentItem.contentItemID}{@SID_ARG_2ND}">{@$contentItem.title}</a></h3>

						{if $contentItem.text}
							<div class="messageBody">
								{@$contentItem.text}
							</div>
						{/if}
						<hr />
					</div>
				</div>
			{/foreach}
		</div>
	</div>

	{assign var=encodedHighlight value=$highlight|urlencode}
	{pages print=true assign=pagesOutput link="index.php?page=ContentItemSearchResult&pageNo=%d&searchID=$searchID&highlight=$encodedHighlight"|concat:SID_ARG_2ND_NOT_ENCODED}

</div>

{include file='footer' sandbox=false}

</body>
</html>