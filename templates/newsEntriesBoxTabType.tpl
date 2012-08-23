{if $boxTabData|count}
	{cycle name='newsEntriesBoxTabCycle' values='container-1,container-2' reset=true print=false advance=false}
	{if $boxTab->displayType == 'large'}
		<div class="container-1">
			{foreach from=$boxTabData item=entry}
				<div class="newsEntryList">
					<div class="message">
						<div class="messageInner {cycle name='newsEntriesBoxTabCycle'}">
							<div class="messageHeader">
								<div class="containerIcon">
									<img src="{icon}newsEntryM.png{/icon}" alt="" />
								</div>
								<div class="containerContent">
									<h3><a href="index.php?page=NewsEntry&amp;entryID={@$entry->entryID}{@SID_ARG_2ND}">{$entry->subject}</a></h3>
									<p class="light smallFont">{lang}wsip.news.entry.by{/lang} {if $entry->userID}<a href="index.php?page=User&amp;userID={@$entry->userID}{@SID_ARG_2ND}">{$entry->username}</a>{else}{$entry->username}{/if} ({@$entry->time|time})</p>
									<p class="rating">{@$entry->getRatingOutput()}</p>
								</div>
							</div>
							<div class="messageBody">
								{@$entry->getFormattedTeaser()}
							</div>

							<div class="editNote smallFont light">
								<p>{lang}wsip.news.entry.views{/lang}: {#$entry->views}{if $entry->getViewsPerDay() > 0} ({lang}wsip.news.entry.viewsPerDay{/lang}){/if}</p>
							</div>

							<div class="messageFooter">
								<div class="smallButtons">
									<ul>
										<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
										{if MODULE_COMMENT && NEWS_ENTRY_ENABLE_COMMENTS && $entry->enableComments}<li><a href="index.php?page=NewsEntry&amp;entryID={@$entry->entryID}{@SID_ARG_2ND}#comments" title="{lang}wsip.news.entry.numberOfComments{/lang}"><img src="{icon}messageS.png{/icon}" alt="" /> <span>{lang}wsip.news.entry.numberOfComments{/lang}</span></a></li>{/if}
										<li><a href="index.php?page=NewsEntry&amp;entryID={@$entry->entryID}{@SID_ARG_2ND}" title="{lang}wsip.news.entry.more{/lang}"><img src="{icon}newsEntryReadMoreS.png{/icon}" alt="" /> <span>{lang}wsip.news.entry.more{/lang}</span></a></li>
									</ul>
								</div>
							</div>
							<hr />
						</div>
					</div>
				</div>
			{/foreach}
		</div>
	{else}
		<ul class="dataList">
			{foreach from=$boxTabData item=entry}
				<li class="{cycle name='newsEntriesBoxTabCycle'}">
					<div class="containerIcon">
						<img src="{icon}newsEntryM.png{/icon}" alt="" />
					</div>
					<div class="containerContent">
						<h4><a href="index.php?page=NewsEntry&amp;entryID={@$entry->entryID}{@SID_ARG_2ND}">{$entry->subject}</a></h4>
						<p class="firstPost smallFont light">
							{if $boxTab->sortField == 'rating'}
								{@$entry->getRatingOutput()}
							{elseif $boxTab->sortField == 'views'}
								{lang}wsip.news.entry.views{/lang}: {#$entry->views}{if $entry->getViewsPerDay() > 0} ({lang}wsip.news.entry.viewsPerDay{/lang}){/if}
							{elseif $boxTab->sortField == 'comments'}
								{lang}wsip.news.entry.numberOfComments{/lang}
							{else}
								{lang}wsip.news.entry.by{/lang} {if $entry->userID}<a href="index.php?page=User&amp;userID={@$entry->userID}{@SID_ARG_2ND}">{$entry->username}</a>{else}{$entry->username}{/if} ({@$entry->time|time})
							{/if}
						</p>
					</div>
				</li>
			{/foreach}
		</ul>
	{/if}
{else}
	<div class="container-1">
		{lang}wsip.news.noEntries{/lang}
	</div>
{/if}