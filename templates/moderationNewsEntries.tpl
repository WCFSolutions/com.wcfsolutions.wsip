{include file="documentHeader"}
<head>
	<title>{lang}wsip.moderation.{@$action}{/lang} {if $pageNo > 1}- {lang}wcf.page.pageNo{/lang} {/if}- {lang}wsip.news.entries{/lang} - {lang}wcf.moderation{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	
	{include file='headInclude' sandbox=false}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>

{include file='header' sandbox=false}

<div id="main">
	
	{include file="moderationCPHeader"}
	
	<div class="border tabMenuContent">
		<div class="container-1">
			<h3 class="subHeadline">{lang}wsip.moderation.{@$action}{/lang}{if $items > 0} <span>({#$items}){/if}</span></h3>
			
			{if $entries|count}
				<div class="contentHeader">
					{pages print=true assign=pagesOutput link="index.php?page=$pageName&pageNo=%d"|concat:SID_ARG_2ND_NOT_ENCODED}

					<div class="optionButtons">
						<ul>
							<li><a><label><input name="newsEntryMarkAll" type="checkbox" /> <span>{lang}wsip.moderation.news.entries.markAll{/lang}</span></label></a></li>
						</ul>
					</div>
				</div>
				
				{if $permissions.canHandleNewsEntry}
					<script type="text/javascript">
						//<![CDATA[	
						var language = new Object();
						//]]>
					</script>
					{include file='newsEntryInlineEdit'}
				{/if}
				
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
										<p class="light smallFont">{lang}wsip.news.entry.by{/lang} <a href="index.php?page=User&amp;userID={@$entry->userID}{@SID_ARG_2ND}">{$entry->username}</a> ({@$entry->time|time})</p>
										{if NEWS_ENTRY_ENABLE_RATING}<p class="rating">{@$entry->getRatingOutput()}</p>{/if}
									</div>
								</div>
								<div class="messageBody">
									{@$entry->getFormattedTeaser()}
								</div>
								
								<div class="editNote smallFont light">
									{if $entry->publishingTime}<p>{lang}wsip.news.entry.publishingTime{/lang}: {@$entry->publishingTime|time}{/if}
									<p>{lang}wsip.news.entry.views{/lang}: {#$entry->views}{if $entry->getViewsPerDay() > 0} ({lang}wsip.news.entry.viewsPerDay{/lang}){/if}</p>
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
				
				<div class="contentFooter">
					{@$pagesOutput}
					
					<div id="newsEntryEditMarked" class="optionButtons"></div>
				</div>
			{else}
				<p>{lang}wsip.moderation.{@$action}.noEntries{/lang}</p>
			{/if}
		</div>
	</div>
	
</div>

{include file='footer' sandbox=false}

</body>
</html>