{include file="documentHeader"}
<head>
	<title>{if $categoryID}{$category->getTitle()} - {/if}{lang}wsip.news.overview{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	
	{include file='headInclude' sandbox=false}
	
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>
	<link rel="alternate" type="application/rss+xml" href="index.php?page=NewsFeed&amp;format=rss2{if $categoryID}&amp;categoryID={@$categoryID}{/if}" title="{if $categoryID}{lang}wsip.news.category.feed{/lang}{else}{lang}wsip.news.feed{/lang}{/if} (RSS2)" />
	<link rel="alternate" type="application/atom+xml" href="index.php?page=NewsFeed&amp;format=atom{if $categoryID}&amp;categoryID={@$categoryID}{/if}" title="{if $categoryID}{lang}wsip.news.category.feed{/lang}{else}{lang}wsip.news.feed{/lang}{/if} (Atom)" />
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">
	
	<ul class="breadCrumbs">
		<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img src="{icon}indexS.png{/icon}" alt="" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
		{if $categoryID}
			<li><a href="index.php?page=NewsOverview{@SID_ARG_2ND}"><img src="{icon}newsS.png{/icon}" alt="" /> <span>{lang}wsip.news.overview{/lang}</span></a> &raquo;</li>
			{foreach from=$category->getParentCategories() item=parentCategory}
				<li><a href="index.php?page=NewsOverview&amp;categoryID={@$parentCategory->categoryID}{@SID_ARG_2ND}"><img src="{icon}categoryS.png{/icon}" alt="" /> <span>{$parentCategory->getTitle()}</span></a> &raquo;</li>
			{/foreach}
		{/if}
	</ul>
	
	<div class="mainHeadline">
		<img src="{icon}{if $categoryID}category{else}news{/if}L.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{if $categoryID}<a href="index.php?page=NewsOverview&amp;categoryID={@$categoryID}{@SID_ARG_2ND}">{$category->getTitle()}</a>{else}<a href="index.php?page=NewsOverview{@SID_ARG_2ND}">{lang}wsip.news.overview{/lang}</a>{/if}</h2>
			{if $categoryID}{@$category->getFormattedDescription()}{/if}
		</div>
	</div>
	
	{if $userMessages|isset}{@$userMessages}{/if}
	
	<div class="border content">
		<div class="container-1 news">
			
			{if $permissions.canHandleNewsEntry}
				<script type="text/javascript">
					//<![CDATA[	
					var language = new Object();
					//]]>
				</script>
				{include file='newsEntryInlineEdit'}
			{/if}
			
			{if $entries|count > 0}
				<div class="contentBox">
					<h3 class="subHeadline">{if $tagID}{lang}wsip.news.entries.tagged{/lang}{else}{lang}wsip.news.entries{/lang}{/if} <span>({#$items})</span></h3>
					
					<div class="contentHeader">
						{assign var=multiplePagesLink value="index.php?page=NewsOverview&categoryID=$categoryID&pageNo=%d&sortField=$sortField&sortOrder=$sortOrder&tagID=$tagID"}
						{pages print=true assign=pagesOutput link=$multiplePagesLink|concat:SID_ARG_2ND_NOT_ENCODED}
						
						{if $entries|count && $permissions.canHandleNewsEntry}
							<div class="optionButtons">
								<ul>
									<li><a><label><input name="newsEntryMarkAll" type="checkbox" /> <span>{lang}wsip.news.entries.markAll{/lang}</span></label></a></li>
								</ul>
							</div>
						{/if}
						{if $tagID || ($category && $category->getPermission('canAddNewsEntry')) || (!$category && $this->user->getPermission('user.portal.canAddNewsEntry')) || $additionalLargeButtons|isset}
							<div class="largeButtons">
								<ul>
									{if $tagID}<li><a href="index.php?page=NewsOverview{if $categoryID}&amp;categoryID={@$categoryID}{/if}{@SID_ARG_2ND}" title="{lang}wsip.news.allEntries{/lang}"><img src="{icon}newsM.png{/icon}" alt="" /> <span>{lang}wsip.news.allEntries{/lang}</span></a></li>{/if}
									{if $category && $category->getPermission('canAddNewsEntry')}<li><a href="index.php?form=NewsEntryAdd&amp;categoryID={@$categoryID}{@SID_ARG_2ND}" title="{lang}wsip.news.entry.button.add{/lang}"><img src="{icon}newsEntryAddM.png{/icon}" alt="" /> <span>{lang}wsip.news.entry.button.add{/lang}</span></a></li>{/if}
									{if !$category && $this->user->getPermission('user.portal.canAddNewsEntry')}<li><a href="index.php?form=NewsEntryAdd{@SID_ARG_2ND}" title="{lang}wsip.news.entry.button.add{/lang}"><img src="{icon}newsEntryAddM.png{/icon}" alt="" /> <span>{lang}wsip.news.entry.button.add{/lang}</span></a></li>{/if}
									{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
								</ul>
							</div>
						{/if}
					</div>
					
					<div class="simpleBar smallFont">
						<ul>
							<li{if $sortField == 'time'} class="selected"{/if}><a href="index.php?page=NewsOverview{if $categoryID}&amp;categoryID={@$categoryID}{/if}&amp;pageNo={@$pageNo}&amp;sortField=time&amp;sortOrder={if $sortField == 'time' && $sortOrder == 'DESC'}ASC{else}DESC{/if}&amp;tagID={@$tagID}{@SID_ARG_2ND}">{lang}wsip.news.entry.time{/lang}{if $sortField == 'time'} <img src="{icon}sort{@$sortOrder}S.png{/icon}" alt="" />{/if}</a></li>
							<li{if $sortField == 'rating'} class="selected"{/if}><a href="index.php?page=NewsOverview{if $categoryID}&amp;categoryID={@$categoryID}{/if}&amp;pageNo={@$pageNo}&amp;sortField=rating&amp;sortOrder={if $sortField == 'rating' && $sortOrder == 'DESC'}ASC{else}DESC{/if}&amp;tagID={@$tagID}{@SID_ARG_2ND}">{lang}wsip.news.entry.rating{/lang}{if $sortField == 'rating'} <img src="{icon}sort{@$sortOrder}S.png{/icon}" alt="" />{/if}</a></li>
							<li{if $sortField == 'views'} class="selected"{/if}><a href="index.php?page=NewsOverview{if $categoryID}&amp;categoryID={@$categoryID}{/if}&amp;pageNo={@$pageNo}&amp;sortField=views&amp;sortOrder={if $sortField == 'views' && $sortOrder == 'DESC'}ASC{else}DESC{/if}&amp;tagID={@$tagID}{@SID_ARG_2ND}">{lang}wsip.news.entry.views{/lang}{if $sortField == 'views'} <img src="{icon}sort{@$sortOrder}S.png{/icon}" alt="" />{/if}</a></li>
							<li{if $sortField == 'comments'} class="selected"{/if}><a href="index.php?page=NewsOverview{if $categoryID}&amp;categoryID={@$categoryID}{/if}&amp;pageNo={@$pageNo}&amp;sortField=comments&amp;sortOrder={if $sortField == 'comments' && $sortOrder == 'DESC'}ASC{else}DESC{/if}&amp;tagID={@$tagID}{@SID_ARG_2ND}">{lang}wsip.news.entry.comments{/lang}{if $sortField == 'comments'} <img src="{icon}sort{@$sortOrder}S.png{/icon}" alt="" />{/if}</a></li>
						</ul>
					</div>
						
					{assign var='messageNumber' value=$items-$startIndex+1}
					{foreach from=$entries item=entry}
						{assign var="entryID" value=$entry->entryID}
						<div class="newsEntryList">
							<div class="message" id="newsEntryRow{@$entry->entryID}">
								<div class="messageInner {cycle values='container-1,container-2'}">
									<a id="entry{@$entry->entryID}"></a>
									<div class="messageHeader">
										<p class="messageCount">
											<a href="index.php?page=NewsEntry&amp;entryID={@$entry->entryID}{@SID_ARG_2ND}" title="{lang}wsip.news.entry.permalink{/lang}" class="messageNumber">{#$messageNumber}</a>
											{if $permissions.canMarkNewsEntry}
												<span class="messageMarkCheckBox">
													<input id="newsEntryMark{@$entry->entryID}" type="checkbox" />
												</span>
											{/if}
										</p>
										<div class="containerIcon">
											{if $permissions.canHandleNewsEntry}
												<script type="text/javascript">
													//<![CDATA[
													newsEntryData.set({@$entry->entryID}, {
														'isMarked': {@$entry->isMarked()},
														'isDeleted': {@$entry->isDeleted},
														'isDisabled': {@$entry->isDisabled}
													});
													//]]>
												</script>
											{/if}
											<img id="newsEntryEdit{@$entry->entryID}" src="{icon}newsEntryM.png{/icon}" alt="" />	
										</div>
										<div class="containerContent">
											<h3 id="newsEntryTitle{@$entry->entryID}"><a href="index.php?page=NewsEntry&amp;entryID={@$entry->entryID}{@SID_ARG_2ND}">{$entry->subject}</a></h3>
											<p class="light smallFont">{lang}wsip.news.entry.by{/lang} {if $entry->userID}<a href="index.php?page=User&amp;userID={@$entry->userID}{@SID_ARG_2ND}">{$entry->username}</a>{else}{$entry->username}{/if} ({@$entry->time|time})</p>
											{if NEWS_ENTRY_ENABLE_RATING}<p class="rating">{@$entry->getRatingOutput()}</p>{/if}
										</div>
									</div>
									<div class="messageBody">
										{@$entry->getFormattedTeaser()}
									</div>
														
									<div class="editNote smallFont light">
										{if $entry->publishingTime}<p>{lang}wsip.news.entry.publishingTime{/lang}: {@$entry->publishingTime|time}{/if}
										<p>{lang}wsip.news.entry.views{/lang}: {#$entry->views}{if $entry->getViewsPerDay() > 0} ({lang}wsip.news.entry.viewsPerDay{/lang}){/if}</p>
										
										{if $tags.$entryID|isset}
											{if $tags.$entryID|isset}<p>{lang}wsip.news.entry.tags{/lang}: {implode from=$tags.$entryID item=entryTag}<a href="index.php?page=NewsOverview&amp;categoryID={@$categoryID}&amp;tagID={@$entryTag->getID()}{@SID_ARG_2ND}">{$entryTag->getName()}</a>{/implode}</p>{/if}
										{/if}
									</div>
									
									{if $entry->isDeleted}
										<p class="deleteNote smallFont light">{lang}wsip.news.entries.deleteNote{/lang}</p>
									{/if}
																
									<div class="messageFooter">
										<div class="smallButtons">
											<ul>
												<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
												{if MODULE_COMMENT && NEWS_ENTRY_ENABLE_COMMENTS && $entry->enableComments}<li><a href="index.php?page=NewsEntry&amp;entryID={@$entry->entryID}{@SID_ARG_2ND}#comments" title="{lang}wsip.news.entry.numberOfComments{/lang}"><img src="{icon}messageS.png{/icon}" alt="" /> <span>{lang}wsip.news.entry.numberOfComments{/lang}</span></a></li>{/if}
												<li><a href="index.php?page=NewsEntry&amp;entryID={@$entry->entryID}{@SID_ARG_2ND}" title="{lang}wsip.news.entry.more{/lang}"><img src="{icon}newsEntryReadMoreS.png{/icon}" alt="" /> <span>{lang}wsip.news.entry.more{/lang}</span></a></li>
												{if $additionalSmallButtons[$entry->entryID]|isset}{@$additionalSmallButtons[$entry->entryID]}{/if}
											</ul>
										</div>
									</div>
									<hr />
								</div>
							</div>
						</div>
						{assign var='messageNumber' value=$messageNumber-1}
					{/foreach}
					
					<div class="simpleBar smallFont">
						<ul>
							<li{if $sortField == 'time'} class="selected"{/if}><a href="index.php?page=NewsOverview{if $categoryID}&amp;categoryID={@$categoryID}{/if}&amp;pageNo={@$pageNo}&amp;sortField=time&amp;sortOrder={if $sortField == 'time' && $sortOrder == 'DESC'}ASC{else}DESC{/if}&amp;tagID={@$tagID}{@SID_ARG_2ND}">{lang}wsip.news.entry.time{/lang}{if $sortField == 'time'} <img src="{icon}sort{@$sortOrder}S.png{/icon}" alt="" />{/if}</a></li>
							<li{if $sortField == 'rating'} class="selected"{/if}><a href="index.php?page=NewsOverview{if $categoryID}&amp;categoryID={@$categoryID}{/if}&amp;pageNo={@$pageNo}&amp;sortField=rating&amp;sortOrder={if $sortField == 'rating' && $sortOrder == 'DESC'}ASC{else}DESC{/if}&amp;tagID={@$tagID}{@SID_ARG_2ND}">{lang}wsip.news.entry.rating{/lang}{if $sortField == 'rating'} <img src="{icon}sort{@$sortOrder}S.png{/icon}" alt="" />{/if}</a></li>
							<li{if $sortField == 'views'} class="selected"{/if}><a href="index.php?page=NewsOverview{if $categoryID}&amp;categoryID={@$categoryID}{/if}&amp;pageNo={@$pageNo}&amp;sortField=views&amp;sortOrder={if $sortField == 'views' && $sortOrder == 'DESC'}ASC{else}DESC{/if}&amp;tagID={@$tagID}{@SID_ARG_2ND}">{lang}wsip.news.entry.views{/lang}{if $sortField == 'views'} <img src="{icon}sort{@$sortOrder}S.png{/icon}" alt="" />{/if}</a></li>
							<li{if $sortField == 'comments'} class="selected"{/if}><a href="index.php?page=NewsOverview{if $categoryID}&amp;categoryID={@$categoryID}{/if}&amp;pageNo={@$pageNo}&amp;sortField=comments&amp;sortOrder={if $sortField == 'comments' && $sortOrder == 'DESC'}ASC{else}DESC{/if}&amp;tagID={@$tagID}{@SID_ARG_2ND}">{lang}wsip.news.entry.comments{/lang}{if $sortField == 'comments'} <img src="{icon}sort{@$sortOrder}S.png{/icon}" alt="" />{/if}</a></li>
						</ul>
					</div>
					
					<div class="contentFooter">
						{@$pagesOutput}
						
						<div id="newsEntryEditMarked" class="optionButtons"></div>
						
						{if $tagID || ($category && $category->getPermission('canAddNewsEntry')) || (!$category && $this->user->getPermission('user.portal.canAddNewsEntry')) || $additionalLargeButtons|isset}
							<div class="largeButtons">
								<ul>
									{if $tagID}<li><a href="index.php?page=NewsOverview{if $categoryID}&amp;categoryID={@$categoryID}{/if}{@SID_ARG_2ND}" title="{lang}wsip.news.allEntries{/lang}"><img src="{icon}newsM.png{/icon}" alt="" /> <span>{lang}wsip.news.allEntries{/lang}</span></a></li>{/if}
									{if $category && $category->getPermission('canAddNewsEntry')}<li><a href="index.php?form=NewsEntryAdd&amp;categoryID={@$categoryID}{@SID_ARG_2ND}" title="{lang}wsip.news.entry.button.add{/lang}"><img src="{icon}newsEntryAddM.png{/icon}" alt="" /> <span>{lang}wsip.news.entry.button.add{/lang}</span></a></li>{/if}
									{if !$category && $this->user->getPermission('user.portal.canAddNewsEntry')}<li><a href="index.php?form=NewsEntryAdd{@SID_ARG_2ND}" title="{lang}wsip.news.entry.button.add{/lang}"><img src="{icon}newsEntryAddM.png{/icon}" alt="" /> <span>{lang}wsip.news.entry.button.add{/lang}</span></a></li>{/if}
									{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
								</ul>
							</div>
						{/if}
					</div>
				</div>
			{else}
				<h3 class="subHeadline">{lang}wsip.news.entries{/lang}</h3>
				<p>{lang}wsip.news.noEntries{/lang}</p>
				
				<div id="newsEntryEditMarked" class="optionButtons"></div>
						
				{if $tagID || ($category && $category->getPermission('canAddNewsEntry')) || (!$category && $this->user->getPermission('user.portal.canAddNewsEntry')) || $additionalLargeButtons|isset}
					<div class="largeButtons">
						<ul>
							{if $tagID}<li><a href="index.php?page=NewsOverview{if $categoryID}&amp;categoryID={@$categoryID}{/if}{@SID_ARG_2ND}" title="{lang}wsip.news.allEntries{/lang}"><img src="{icon}newsM.png{/icon}" alt="" /> <span>{lang}wsip.news.allEntries{/lang}</span></a></li>{/if}
							{if $category && $category->getPermission('canAddNewsEntry')}<li><a href="index.php?form=NewsEntryAdd&amp;categoryID={@$categoryID}{@SID_ARG_2ND}" title="{lang}wsip.news.entry.button.add{/lang}"><img src="{icon}newsEntryAddM.png{/icon}" alt="" /> <span>{lang}wsip.news.entry.button.add{/lang}</span></a></li>{/if}
							{if !$category && $this->user->getPermission('user.portal.canAddNewsEntry')}<li><a href="index.php?form=NewsEntryAdd{@SID_ARG_2ND}" title="{lang}wsip.news.entry.button.add{/lang}"><img src="{icon}newsEntryAddM.png{/icon}" alt="" /> <span>{lang}wsip.news.entry.button.add{/lang}</span></a></li>{/if}
							{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
						</ul>
					</div>
				{/if}
			{/if}
			
		</div>
	</div>
	
	{if NEWS_ENABLE_STATS || $tags|count > 0 || $additionalBoxes|isset}
		<div class="border infoBox">
			{if NEWS_ENABLE_STATS}
				<div class="{cycle values='container-1,container-2'}">
					<div class="containerIcon"><img src="{icon}statisticsM.png{/icon}" alt="" /></div>
					<div class="containerContent">
						<h3>{lang}wsip.news.stats{/lang}</h3> 
						<p class="smallFont">{lang}wsip.news.stats.detail{/lang}</p>
					</div>
				</div>
			{/if}
			
			{if $tags|count > 0}
				<div class="{cycle values='container-1,container-2'}">
					<div class="containerIcon">
						<img src="{icon}tagM.png{/icon}" alt="" />
					</div>
					<div class="containerContent">
						<h3><span>{lang}wcf.tagging.filter{/lang}</span></h3>
						<ul class="tagCloud">
							{foreach from=$availableTags item=tag}
								<li><a href="index.php?page=NewsOverview{if $categoryID}&amp;categoryID={@$categoryID}{/if}&amp;sortField={@$sortField}&amp;sortOrder={@$sortOrder}&amp;tagID={@$tag->getID()}{@SID_ARG_2ND}" style="font-size: {@$tag->getSize()}%">{$tag->getName()}</a></li>
							{/foreach}
						</ul>						
					</div>
				</div>
			{/if}
			
			{if $additionalBoxes|isset}{@$additionalBoxes}{/if}
		</div>
	{/if}
	
	{include file='categoryQuickJump' pageName="NewsOverview"}
</div>

{include file='footer' sandbox=false}

</body>
</html>