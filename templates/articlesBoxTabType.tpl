{if $boxTabData|count}
	{cycle name='articlesBoxTabCycle' values='container-1,container-2' reset=true print=false advance=false}
	{if $boxTab->displayType == 'large'}
		<div class="container-1">
			{foreach from=$boxTabData item=article}
				<div class="articleList">
					<div class="message">
						<div class="messageInner {cycle name='articlesBoxTabCycle'}">
							<div class="messageHeader">
								<div class="containerIcon">
									<img src="{icon}articleM.png{/icon}" alt="" />	
								</div>
								<div class="containerContent">
									<h3><a href="index.php?page=Article&amp;sectionID={@$article->firstSectionID}{@SID_ARG_2ND}">{$article->subject}</a></h3>
									<p class="light smallFont">{lang}wsip.article.by{/lang} {if $article->userID}<a href="index.php?page=User&amp;userID={@$article->userID}{@SID_ARG_2ND}">{$article->username}</a>{else}{$article->username}{/if} ({@$article->time|time})</p>
									<p class="rating">{@$article->getRatingOutput()}</p>
								</div>
							</div>
							<div class="messageBody">
								{@$article->getFormattedTeaser()}
							</div>
							
							<div class="editNote smallFont light">
								<p>{lang}wsip.article.views{/lang}: {#$article->views}{if $article->getViewsPerDay() > 0} ({lang}wsip.article.viewsPerDay{/lang}){/if}</p>
							</div>
							
							<div class="messageFooter">
								<div class="smallButtons">
									<ul>
										<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
										{if MODULE_COMMENT && ARTICLE_ENABLE_COMMENTS && $article->enableComments}<li><a href="index.php?page=Article&amp;sectionID={@$article->firstSectionID}{@SID_ARG_2ND}#comments" title="{lang}wsip.article.numberOfComments{/lang}"><img src="{icon}messageS.png{/icon}" alt="" /> <span>{lang}wsip.article.numberOfComments{/lang}</span></a></li>{/if}
										<li><a href="index.php?page=Article&amp;sectionID={@$article->firstSectionID}{@SID_ARG_2ND}" title="{lang}wsip.article.more{/lang}"><img src="{icon}articleReadMoreS.png{/icon}" alt="" /> <span>{lang}wsip.article.more{/lang}</span></a></li>
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
			{foreach from=$boxTabData item=article}
				<li class="{cycle name='articlesBoxTabCycle'}">
					<div class="containerIcon">
						<img src="{icon}articleM.png{/icon}" alt="" />
					</div>
					<div class="containerContent">
						<h4><a href="index.php?page=Article&amp;sectionID={@$article->firstSectionID}{@SID_ARG_2ND}">{$article->subject}</a></h4>
						<p class="firstPost smallFont light">
							{if $boxTab->sortField == 'rating'}
								{@$article->getRatingOutput()}
							{elseif $boxTab->sortField == 'views'}
								{lang}wsip.article.views{/lang}: {#$article->views}{if $article->getViewsPerDay() > 0} ({lang}wsip.article.viewsPerDay{/lang}){/if}
							{elseif $boxTab->sortField == 'comments'}
								{lang}wsip.article.numberOfComments{/lang}
							{else}
								{lang}wsip.article.by{/lang} {if $article->userID}<a href="index.php?page=User&amp;userID={@$article->userID}{@SID_ARG_2ND}">{$article->username}</a>{else}{$article->username}{/if} ({@$article->time|time})
							{/if}
						</p>
					</div>
				</li>
			{/foreach}
		</ul>
	{/if}
{else}
	<div class="container-1">
		{lang}wsip.article.noArticles{/lang}
	</div>
{/if}