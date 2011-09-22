{include file="documentHeader"}
<head>
	<title>{lang}wsip.contentItem.overview{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	
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
			<li class="activeTabMenu"><a href="index.php?page=ContentItemOverview{@SID_ARG_2ND}"><img src="{icon}contentItemM.png{/icon}" alt="" /> <span>{lang}wsip.contentItem.overview{/lang}</span></a></li>
			<li><a href="index.php?form=ContentItemSearch{@SID_ARG_2ND}"><img src="{icon}searchM.png{/icon}" alt="" /> <span>{lang}wsip.contentItem.search{/lang}</span></a></li>
			{if $additionalTabs|isset}{@$additionalTabs}{/if}
		</ul>
	</div>
	<div class="subTabMenu">
		<div class="containerHead"> </div>
	</div>
	
	<div class="border tabMenuContent">
		<div class="container-1">
			<h3 class="subHeadline">{lang}wsip.contentItem.overview{/lang}</h3>
			
			{if $contentItems|count}
				<ol class="itemList">
					{foreach from=$contentItems item=child}
						{assign var="contentItem" value=$child.contentItem}
						
						<li>
							{if $child.additionalButtons|isset}
								<div class="buttons">
									{@$child.additionalButtons}
								</div>
							{/if}
							
							<h3 class="itemListTitle">
								<img src="{icon}{@$contentItem->getIcon()}S.png{/icon}" alt="" />
								
								<a href="index.php?page=ContentItem&amp;contentItemID={@$contentItem->contentItemID}{@SID_ARG_2ND}" class="title">{$contentItem->getTitle()}</a>
							</h3>
						
						{if $child.hasChildren}<ol>{else}</li>{/if}
						{if $child.openParents > 0}{@"</ol></li>"|str_repeat:$child.openParents}{/if}
					{/foreach}
				</ol>
			{else}
				<p>{lang}wsip.contentItem.overview.noContentItems{/lang}</p>
			{/if}
		</div>
	</div>
	
</div>

{include file='footer' sandbox=false}

</body>
</html>