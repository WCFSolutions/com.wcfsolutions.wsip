{include file="documentHeader"}
<head>
	<title>{$contentItem->getTitle()} - {lang}{PAGE_TITLE}{/lang}</title>

	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/SubTabMenu.class.js"></script>
	{include file='headInclude' sandbox=false}
	{include file='imageViewer'}
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
		{foreach from=$contentItem->getParentContentItems() item=parentContentItem}
			<li><a href="index.php?page=ContentItem&amp;contentItemID={@$parentContentItem->contentItemID}{@SID_ARG_2ND}"><img src="{icon}{$contentItem->getIcon()}S.png{/icon}" alt="" /> <span>{$parentContentItem->getTitle()}</span></a> &raquo;</li>
		{/foreach}
	</ul>

	<div class="mainHeadline">
		<img src="{icon}{$contentItem->getIcon()}L.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2><a href="index.php?page=ContentItem&amp;contentItemID={@$contentItem->contentItemID}{@SID_ARG_2ND}">{$contentItem->getTitle()}</a></h2>
			<p>{@$contentItem->getFormattedDescription()}</p>
		</div>
	</div>

	{if $userMessages|isset}{@$userMessages}{/if}

	{if $contentItem->isPage()}
		<div class="border content">
			<div class="container-1">
				{@$contentItem->getText()}
			</div>
		</div>
	{else}
		<div class="boxList">
			{foreach from=$contentItem->getBoxes() item=box}
				{capture assign=boxPositionIdentifier}box{@$box->boxID}_contentItem{@$contentItemID}{/capture}
				{assign var=boxTabs value=$box->getBoxTabs()}
				<div class="contentBox">
					<div class="border">
						{if $box->enableTitle}
							<div class="containerHead">
								<h3>{$box->getTitle()}</h3>
								{if $box->getFormattedDescription()}<p class="smallFont">{@$box->getFormattedDescription()}</p>{/if}
							</div>
						{/if}
						{if $boxTabs|count > 1}
							<div class="subTabMenu" style="display: none;">
								<div class="containerHead">
									<ul>
										{foreach from=$box->getBoxTabs() item=boxTab}
											<li id="{@$boxPositionIdentifier}_{@$boxTab->boxTabID}"><a onclick="{@$boxPositionIdentifier}_tabMenu.showTabMenuContent('{@$boxPositionIdentifier}_{@$boxTab->boxTabID}');"><span>{$boxTab->getTitle()}</span></a></li>
										{/foreach}
									</ul>
								</div>
							</div>
						{/if}
						{foreach from=$boxTabs item=boxTab}
							{assign var=boxTabType value=$boxTab->getBoxTabType()}
							{assign var=boxTabData value=$boxTabType->getData($boxTab)}

							<div class="tabMenuContent" id="{@$boxPositionIdentifier}_{@$boxTab->boxTabID}-content">
								<noscript>
									<div class="subTabMenu">
										<div class="containerHead">
											<h4>{$boxTab->getTitle()}</h4>
										</div>
									</div>
								</noscript>
								{include file=$boxTab->getBoxTabType()->getTemplateName()}
							</div>
						{/foreach}
					</div>
				</div>
				{if $boxTabs|count > 1}
					<script type="text/javascript">
						//<![CDATA[
						{if $boxTabs|count > 1}
							var {@$boxPositionIdentifier}_tabMenu = new SubTabMenu();
							onloadEvents.push(function() { {@$boxPositionIdentifier}_tabMenu.showTabMenuContent('{@$boxPositionIdentifier}_{@$box->getFirstBoxTabID()}'); });
						{/if}
						//]]>
					</script>
				{/if}
			{/foreach}
		</div>
	{/if}

</div>

{include file='footer' sandbox=false}

</body>
</html>