{include file="documentHeader"}
<head>
	<title>{if $categoryID}{$category->getTitle()} - {/if}{lang}wsip.article.overview{/lang} - {lang}{PAGE_TITLE}{/lang}</title>

	{include file='headInclude' sandbox=false}
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">

	<ul class="breadCrumbs">
		<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img src="{icon}indexS.png{/icon}" alt="" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
		{if $categoryID}
			<li><a href="index.php?page=ArticleOverview{@SID_ARG_2ND}"><img src="{icon}articleS.png{/icon}" alt="" /> <span>{lang}wsip.article.overview{/lang}</span></a> &raquo;</li>
			{foreach from=$category->getParentCategories() item=parentCategory}
				<li><a href="index.php?page=ArticleOverview&amp;categoryID={@$parentCategory->categoryID}{@SID_ARG_2ND}"><img src="{icon}categoryS.png{/icon}" alt="" /> <span>{$parentCategory->getTitle()}</span></a> &raquo;</li>
			{/foreach}
		{/if}
	</ul>

	<div class="mainHeadline">
		<img src="{icon}{if $categoryID}category{else}article{/if}L.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{if $categoryID}<a href="index.php?page=ArticleOverview&amp;categoryID={@$categoryID}{@SID_ARG_2ND}">{$category->getTitle()}</a>{else}<a href="index.php?page=ArticleOverview{@SID_ARG_2ND}">{lang}wsip.article.overview{/lang}</a>{/if}</h2>
			{if $categoryID}{@$category->getFormattedDescription()}{/if}
		</div>
	</div>

	{if $userMessages|isset}{@$userMessages}{/if}

	<div class="border content">
		<div class="container-1">

			{if $articles|count > 0}
				<div class="contentBox">
					<h3 class="subHeadline">{if $tagID}{lang}wsip.article.articles.tagged{/lang}{else}{lang}wsip.article.articles{/lang}{/if} <span>({#$items})</span></h3>

					<div class="contentHeader">
						{assign var=multiplePagesLink value="index.php?page=ArticleOverview&categoryID=$categoryID&pageNo=%d&sortField=$sortField&sortOrder=$sortOrder&tagID=$tagID"}
						{pages print=true assign=pagesOutput link=$multiplePagesLink|concat:SID_ARG_2ND_NOT_ENCODED}

						{if $tagID || ($category && $category->getPermission('canAddArticle')) || (!$category && $this->user->getPermission('user.portal.canAddArticle')) || $additionalLargeButtons|isset}
							<div class="largeButtons">
								<ul>
									{if $tagID}<li><a href="index.php?page=ArticleOverview{if $categoryID}&amp;categoryID={@$categoryID}{/if}{@SID_ARG_2ND}" title="{lang}wsip.article.allArticles{/lang}"><img src="{icon}articleM.png{/icon}" alt="" /> <span>{lang}wsip.article.allArticles{/lang}</span></a></li>{/if}
									{if $category && $category->getPermission('canAddArticle')}<li><a href="index.php?form=ArticleAdd&amp;categoryID={@$categoryID}{@SID_ARG_2ND}" title="{lang}wsip.article.button.add{/lang}"><img src="{icon}articleAddM.png{/icon}" alt="" /> <span>{lang}wsip.article.button.add{/lang}</span></a></li>{/if}
									{if !$category && $this->user->getPermission('user.portal.canAddArticle')}<li><a href="index.php?form=ArticleAdd{@SID_ARG_2ND}" title="{lang}wsip.article.button.add{/lang}"><img src="{icon}articleAddM.png{/icon}" alt="" /> <span>{lang}wsip.article.button.add{/lang}</span></a></li>{/if}
									{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
								</ul>
							</div>
						{/if}
					</div>

					<div class="simpleBar smallFont">
						<ul>
							<li{if $sortField == 'time'} class="selected"{/if}><a href="index.php?page=ArticleOverview{if $categoryID}&amp;categoryID={@$categoryID}{/if}&amp;pageNo={@$pageNo}&amp;sortField=time&amp;sortOrder={if $sortField == 'time' && $sortOrder == 'DESC'}ASC{else}DESC{/if}&amp;tagID={@$tagID}{@SID_ARG_2ND}">{lang}wsip.article.time{/lang}{if $sortField == 'time'} <img src="{icon}sort{@$sortOrder}S.png{/icon}" alt="" />{/if}</a></li>
							<li{if $sortField == 'rating'} class="selected"{/if}><a href="index.php?page=ArticleOverview{if $categoryID}&amp;categoryID={@$categoryID}{/if}&amp;pageNo={@$pageNo}&amp;sortField=rating&amp;sortOrder={if $sortField == 'rating' && $sortOrder == 'DESC'}ASC{else}DESC{/if}&amp;tagID={@$tagID}{@SID_ARG_2ND}">{lang}wsip.article.rating{/lang}{if $sortField == 'rating'} <img src="{icon}sort{@$sortOrder}S.png{/icon}" alt="" />{/if}</a></li>
							<li{if $sortField == 'views'} class="selected"{/if}><a href="index.php?page=ArticleOverview{if $categoryID}&amp;categoryID={@$categoryID}{/if}&amp;pageNo={@$pageNo}&amp;sortField=views&amp;sortOrder={if $sortField == 'views' && $sortOrder == 'DESC'}ASC{else}DESC{/if}&amp;tagID={@$tagID}{@SID_ARG_2ND}">{lang}wsip.article.views{/lang}{if $sortField == 'views'} <img src="{icon}sort{@$sortOrder}S.png{/icon}" alt="" />{/if}</a></li>
							<li{if $sortField == 'comments'} class="selected"{/if}><a href="index.php?page=ArticleOverview{if $categoryID}&amp;categoryID={@$categoryID}{/if}&amp;pageNo={@$pageNo}&amp;sortField=comments&amp;sortOrder={if $sortField == 'comments' && $sortOrder == 'DESC'}ASC{else}DESC{/if}&amp;tagID={@$tagID}{@SID_ARG_2ND}">{lang}wsip.article.comments{/lang}{if $sortField == 'comments'} <img src="{icon}sort{@$sortOrder}S.png{/icon}" alt="" />{/if}</a></li>
						</ul>
					</div>

					{assign var='messageNumber' value=$items-$startIndex+1}
					{foreach from=$articles item=article}
						{assign var="articleID" value=$article->articleID}
						<div class="articleList">
							<div class="message">
								<div class="messageInner {cycle values='container-1,container-2'}">
									<div class="messageHeader">
										<p class="messageCount">
											<a href="index.php?page=Article&amp;sectionID={@$article->firstSectionID}{@SID_ARG_2ND}" title="{lang}wsip.article.permalink{/lang}" class="messageNumber">{#$messageNumber}</a>
										</p>
										<div class="containerIcon">
											<img src="{icon}articleM.png{/icon}" alt="" />
										</div>
										<div class="containerContent">
											<h3><a href="index.php?page=Article&amp;sectionID={@$article->firstSectionID}{@SID_ARG_2ND}">{$article->subject}</a></h3>
											<p class="light smallFont">{lang}wsip.article.by{/lang} {if $article->userID}<a href="index.php?page=User&amp;userID={@$article->userID}{@SID_ARG_2ND}">{$article->username}</a>{else}{$article->username}{/if} ({@$article->time|time})</p>
											{if ARTICLE_ENABLE_RATING}<p class="rating">{@$article->getRatingOutput()}</p>{/if}
										</div>
									</div>
									<div class="messageBody">
										{@$article->getFormattedTeaser()}
									</div>

									<div class="editNote smallFont light">
										<p>{lang}wsip.article.views{/lang}: {#$article->views}{if $article->getViewsPerDay() > 0} ({lang}wsip.article.viewsPerDay{/lang}){/if}</p>

										{if $tags.$articleID|isset}
											{if $tags.$articleID|isset}<p>{lang}wsip.article.tags{/lang}: {implode from=$tags.$articleID item=articleTag}<a href="index.php?page=ArticleOverview&amp;categoryID={@$categoryID}&amp;tagID={@$articleTag->getID()}{@SID_ARG_2ND}">{$articleTag->getName()}</a>{/implode}</p>{/if}
										{/if}
									</div>

									<div class="messageFooter">
										<div class="smallButtons">
											<ul>
												<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
												{if MODULE_COMMENT && ARTICLE_ENABLE_COMMENTS && $article->enableComments}<li><a href="index.php?page=Article&amp;sectionID={@$article->firstSectionID}{@SID_ARG_2ND}#comments" title="{lang}wsip.article.numberOfComments{/lang}"><img src="{icon}messageS.png{/icon}" alt="" /> <span>{lang}wsip.article.numberOfComments{/lang}</span></a></li>{/if}
												<li><a href="index.php?page=Article&amp;sectionID={@$article->firstSectionID}{@SID_ARG_2ND}" title="{lang}wsip.article.more{/lang}"><img src="{icon}articleReadMoreS.png{/icon}" alt="" /> <span>{lang}wsip.article.more{/lang}</span></a></li>
												{if $additionalSmallButtons[$article->articleID]|isset}{@$additionalSmallButtons[$article->articleID]}{/if}
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
							<li{if $sortField == 'time'} class="selected"{/if}><a href="index.php?page=ArticleOverview{if $categoryID}&amp;categoryID={@$categoryID}{/if}&amp;pageNo={@$pageNo}&amp;sortField=time&amp;sortOrder={if $sortField == 'time' && $sortOrder == 'DESC'}ASC{else}DESC{/if}&amp;tagID={@$tagID}{@SID_ARG_2ND}">{lang}wsip.article.time{/lang}{if $sortField == 'time'} <img src="{icon}sort{@$sortOrder}S.png{/icon}" alt="" />{/if}</a></li>
							<li{if $sortField == 'rating'} class="selected"{/if}><a href="index.php?page=ArticleOverview{if $categoryID}&amp;categoryID={@$categoryID}{/if}&amp;pageNo={@$pageNo}&amp;sortField=rating&amp;sortOrder={if $sortField == 'rating' && $sortOrder == 'DESC'}ASC{else}DESC{/if}&amp;tagID={@$tagID}{@SID_ARG_2ND}">{lang}wsip.article.rating{/lang}{if $sortField == 'rating'} <img src="{icon}sort{@$sortOrder}S.png{/icon}" alt="" />{/if}</a></li>
							<li{if $sortField == 'views'} class="selected"{/if}><a href="index.php?page=ArticleOverview{if $categoryID}&amp;categoryID={@$categoryID}{/if}&amp;pageNo={@$pageNo}&amp;sortField=views&amp;sortOrder={if $sortField == 'views' && $sortOrder == 'DESC'}ASC{else}DESC{/if}&amp;tagID={@$tagID}{@SID_ARG_2ND}">{lang}wsip.article.views{/lang}{if $sortField == 'views'} <img src="{icon}sort{@$sortOrder}S.png{/icon}" alt="" />{/if}</a></li>
							<li{if $sortField == 'comments'} class="selected"{/if}><a href="index.php?page=ArticleOverview{if $categoryID}&amp;categoryID={@$categoryID}{/if}&amp;pageNo={@$pageNo}&amp;sortField=comments&amp;sortOrder={if $sortField == 'comments' && $sortOrder == 'DESC'}ASC{else}DESC{/if}&amp;tagID={@$tagID}{@SID_ARG_2ND}">{lang}wsip.article.comments{/lang}{if $sortField == 'comments'} <img src="{icon}sort{@$sortOrder}S.png{/icon}" alt="" />{/if}</a></li>
						</ul>
					</div>

					<div class="contentFooter">
						{@$pagesOutput}

						{if $tagID || ($category && $category->getPermission('canAddArticle')) || (!$category && $this->user->getPermission('user.portal.canAddArticle')) || $additionalLargeButtons|isset}
							<div class="largeButtons">
								<ul>
									{if $tagID}<li><a href="index.php?page=ArticleOverview{if $categoryID}&amp;categoryID={@$categoryID}{/if}{@SID_ARG_2ND}" title="{lang}wsip.article.allArticles{/lang}"><img src="{icon}articleM.png{/icon}" alt="" /> <span>{lang}wsip.article.allArticles{/lang}</span></a></li>{/if}
									{if $category && $category->getPermission('canAddArticle')}<li><a href="index.php?form=ArticleAdd&amp;categoryID={@$categoryID}{@SID_ARG_2ND}" title="{lang}wsip.article.button.add{/lang}"><img src="{icon}articleAddM.png{/icon}" alt="" /> <span>{lang}wsip.article.button.add{/lang}</span></a></li>{/if}
									{if !$category && $this->user->getPermission('user.portal.canAddArticle')}<li><a href="index.php?form=ArticleAdd{@SID_ARG_2ND}" title="{lang}wsip.article.button.add{/lang}"><img src="{icon}articleAddM.png{/icon}" alt="" /> <span>{lang}wsip.article.button.add{/lang}</span></a></li>{/if}
									{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
								</ul>
							</div>
						{/if}
					</div>
				</div>
			{else}
				<h3 class="subHeadline">{lang}wsip.article.articles{/lang}</h3>
				<p>{lang}wsip.article.noArticles{/lang}</p>

				{if $tagID || ($category && $category->getPermission('canAddArticle')) || (!$category && $this->user->getPermission('user.portal.canAddArticle')) || $additionalLargeButtons|isset}
					<div class="largeButtons">
						<ul>
							{if $tagID}<li><a href="index.php?page=ArticleOverview{if $categoryID}&amp;categoryID={@$categoryID}{/if}{@SID_ARG_2ND}" title="{lang}wsip.article.allArticles{/lang}"><img src="{icon}articleM.png{/icon}" alt="" /> <span>{lang}wsip.article.allArticles{/lang}</span></a></li>{/if}
							{if $category && $category->getPermission('canAddArticle')}<li><a href="index.php?form=ArticleAdd&amp;categoryID={@$categoryID}{@SID_ARG_2ND}" title="{lang}wsip.article.button.add{/lang}"><img src="{icon}articleAddM.png{/icon}" alt="" /> <span>{lang}wsip.article.button.add{/lang}</span></a></li>{/if}
							{if !$category && $this->user->getPermission('user.portal.canAddArticle')}<li><a href="index.php?form=ArticleAdd{@SID_ARG_2ND}" title="{lang}wsip.article.button.add{/lang}"><img src="{icon}articleAddM.png{/icon}" alt="" /> <span>{lang}wsip.article.button.add{/lang}</span></a></li>{/if}
							{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
						</ul>
					</div>
				{/if}
			{/if}

		</div>
	</div>

	{if ARTICLE_ENABLE_STATS || $tags|count > 0 || $additionalBoxes|isset}
		<div class="border infoBox">
			{if ARTICLE_ENABLE_STATS}
				<div class="{cycle values='container-1,container-2'}">
					<div class="containerIcon"><img src="{icon}statisticsM.png{/icon}" alt="" /></div>
					<div class="containerContent">
						<h3>{lang}wsip.article.stats{/lang}</h3>
						<p class="smallFont">{lang}wsip.article.stats.detail{/lang}</p>
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
								<li><a href="index.php?page=ArticleOverview{if $categoryID}&amp;categoryID={@$categoryID}{/if}&amp;sortField={@$sortField}&amp;sortOrder={@$sortOrder}&amp;tagID={@$tag->getID()}{@SID_ARG_2ND}" style="font-size: {@$tag->getSize()}%">{$tag->getName()}</a></li>
							{/foreach}
						</ul>
					</div>
				</div>
			{/if}

			{if $additionalBoxes|isset}{@$additionalBoxes}{/if}
		</div>
	{/if}

	{include file='categoryQuickJump' pageName="ArticleOverview"}
</div>

{include file='footer' sandbox=false}

</body>
</html>