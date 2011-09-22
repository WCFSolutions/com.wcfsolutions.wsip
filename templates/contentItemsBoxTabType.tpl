{if $boxTabData->getSubContentItems()|count}
	{cycle name='contentItemsBoxTabCycle' values='container-1,container-2' reset=true print=false advance=false}
	<ul class="dataList">
		{foreach from=$boxTabData->getSubContentItems() item=contentItem}
			<li class="{cycle name='contentItemsBoxTabCycle'}"><h4 class="itemListTitle"><img src="{icon}{$contentItem->getIcon()}S.png{/icon}" alt="" /> <a href="index.php?page=ContentItem&amp;contentItemID={@$contentItem->contentItemID}{@SID_ARG_2ND}"><span>{$contentItem->getTitle()}</span></a></h4></li>
		{/foreach}
	</ul>
{/if}