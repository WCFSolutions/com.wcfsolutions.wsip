{include file="documentHeader"}
<head>
	<title>{$article->subject} - {lang}wsip.article.overview{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	
	{include file='headInclude' sandbox=false}
	{include file='imageViewer'}
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">
	
	<ul class="breadCrumbs">
		<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img src="{icon}indexS.png{/icon}" alt="" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
		<li><a href="index.php?page=ArticleOverview{@SID_ARG_2ND}"><img src="{icon}articleS.png{/icon}" alt="" /> <span>{lang}wsip.article.overview{/lang}</span></a> &raquo;</li>
		{foreach from=$category->getParentCategories() item=parentCategory}
			<li><a href="index.php?page=ArticleOverview&amp;categoryID={@$parentCategory->categoryID}{@SID_ARG_2ND}"><img src="{icon}categoryS.png{/icon}" alt="" /> <span>{$parentCategory->getTitle()}</span></a> &raquo;</li>
		{/foreach}
		<li><a href="index.php?page=ArticleOverview&amp;categoryID={@$category->categoryID}{@SID_ARG_2ND}"><img src="{icon}categoryS.png{/icon}" alt="" /> <span>{$category->getTitle()}</span></a> &raquo;</li>
	</ul>
	
	<div class="mainHeadline">
		<img src="{icon}articleL.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2><a href="index.php?page=Article&amp;sectionID={@$article->firstSectionID}{@SID_ARG_2ND}">{$article->subject}</a></h2>
		</div>
	</div>
	
	{if $userMessages|isset}{@$userMessages}{/if}
	
	{if MODULE_COMMENT && ARTICLE_ENABLE_COMMENTS && $article->enableComments && $article->isCommentable() && $action != 'edit'}{assign var=commentUsername value=$username}{/if}
	
	<div class="border content">
		<div class="container-1 articles">
			<div class="contentBox articleSection">
				<p class="messageCount">
					<a href="index.php?page=Article&amp;sectionID={@$article->firstSectionID}{@SID_ARG_2ND}" title="{lang messageNumber=$article->articleID}wsip.article.permalink{/lang}" class="messageNumber">{#$article->articleID}</a>
				</p>
				
				<h3 class="subHeadline">{$section->subject}</h3>
				
				<div class="contentHeader">
					{if ARTICLE_ENABLE_RATING}<p class="rating light smallFont">{lang}wsip.article.rating{/lang}: <span id="com.wcfsolutions.wsip.article-ratingOutput{@$article->articleID}">{@$article->getRatingOutput()}</span></p>{/if}
					
					<p class="light smallFont">{lang}wsip.article.by{/lang} {if $article->userID}<a href="index.php?page=User&amp;userID={@$article->userID}{@SID_ARG_2ND}">{$article->username}</a>{else}{$article->username}{/if}, {@$article->time|time}</p>
				</div>
				
				<div class="articleSectionInner">
					{if $sections|count > 1}
						<div class="container-3">
							<div class="border titleBarPanel articleSections"> 
								<div class="containerHead"> 
									<h3><a href="index.php?page=ArticleSectionList&amp;articleID={@$article->articleID}{@SID_ARG_2ND}" title="{lang}wsip.article.sections{/lang}">{lang}wsip.article.sections{/lang}</a></h3>
								</div>
								<ul class="itemList dataList">
									{foreach from=$sections item=child}
										<li class="{if $child.section->sectionID == $sectionID}container-3 selected{else}container-1{/if}"><h4 class="itemListTitle"><a href="index.php?page=Article&amp;sectionID={@$child.section->sectionID}{@SID_ARG_2ND}"><span>{$child.section->subject}</span></a></h4>
										{if $child.hasChildren}<ul class="itemList">{else}</li>{/if}
										{if $child.openParents > 0}{@"</ul></li>"|str_repeat:$child.openParents}{/if}
									{/foreach}
								</ul>
							</div>
						</div>
					{/if}
					
					<div class="messageBody" id="articleSectionText{@$section->sectionID}">
						{@$section->getFormattedMessage()}
					</div>
					
					{assign var="messageID" value=$section->sectionID}
					{assign var="author" value=$section->getUser()}
					{include file='attachmentsShow'}
					
					<div class="buttonBar">
						<p class="light smallFont">{lang}wsip.article.views{/lang}: {#$article->views}{if $article->getViewsPerDay() > 0} ({lang}wsip.article.viewsPerDay{/lang}){/if}</p>
						
						{if $tags|count > 0}
							<p class="light smallFont">{lang}wsip.article.tags{/lang}: {implode from=$tags item=tag}<a href="index.php?page=NewsOverview&amp;categoryID={@$article->categoryID}&amp;tagID={@$tag->getID()}{@SID_ARG_2ND}">{$tag->getName()}</a>{/implode}</p>
						{/if}
						
						{if $socialBookmarks|isset}
							{@$socialBookmarks}
						{/if}
						
						{if ARTICLE_ENABLE_RATING}
							<div class="pageOptions rating">
								<span>{lang}wsip.article.rate{/lang}</span>
								{include file='objectRating'}
								<div id="com.wcfsolutions.wsip.article-rating{@$article->articleID}"></div>
								<noscript>
									<form method="post" action="index.php?action=ObjectRating{@SID_ARG_2ND}">
										<div>
											<select id="articleRatingSelect" name="rating">
												{section name=i start=1 loop=6}
													<option value="{@$i}"{if $i == $rating->getUserRating()} selected="selected"{/if}>{@$i}</option>
												{/section}
											</select>
											<input type="hidden" name="objectName" value="com.wcfsolutions.wsip.article" />
											<input type="hidden" name="objectID" value="{@$article->articleID}" />
											<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
											<input type="hidden" name="url" value="index.php?page=Article&amp;articleID={@$article->articleID}" />
											<input type="image" class="inputImage" src="{icon}submitS.png{/icon}" alt="{lang}wcf.global.button.submit{/lang}" />
										</div>
									</form>
								</noscript>
								<script type="text/javascript">
									//<![CDATA[
									objectRatingObj.initializeObject({
										currentRating: {@$rating->getUserRating()},
										objectID: {@$article->articleID},
										objectName: 'com.wcfsolutions.wsip.article',
										packageID: {@PACKAGE_ID}
									});
									//]]>
								</script>
							</div>
						{/if}
					</div>
					
					<div class="buttonBar">
						<div class="smallButtons">
							<ul id="articleSectionButtons{@$section->sectionID}">
								<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="{lang}wcf.global.scrollUp{/lang}" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
								{if $article->isEditable($category)}
									<li><a href="index.php?form=ArticleSectionAdd&amp;articleID={@$article->articleID}{@SID_ARG_2ND}" title="{lang}wsip.article.section.add{/lang}"><img src="{icon}addS.png{/icon}" alt="" /> <span>{lang}wsip.article.section.add{/lang}</span></a></li>
									<li><a href="index.php?form=ArticleSectionEdit&amp;sectionID={@$section->sectionID}{@SID_ARG_2ND}" title="{lang}wsip.article.section.edit{/lang}"><img src="{icon}editS.png{/icon}" alt="" /> <span>{lang}wsip.article.section.edit{/lang}</span></a></li>
								{/if}
								{if $article->isDeletable($category)}
									<li><a href="index.php?action=ArticleSectionDelete&amp;sectionID={@$section->sectionID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" onclick="return confirm('{lang}wsip.article.section.delete.sure{/lang}')" title="{lang}wsip.article.section.delete{/lang}"><img src="{icon}deleteS.png{/icon}" alt="" /> <span>{lang}wsip.article.section.delete{/lang}</span></a></li>
								{/if}
								{if $this->user->userID}
									{if !$article->isSubscribed()}
										<li><a href="index.php?action=PublicationObjectSubscribe&amp;publicationType=article&amp;publicationObjectID={@$article->articleID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" title="{lang}wsip.publication.object.subscribe{/lang}"><img src="{icon}publicationObjectSubscribeS.png{/icon}" alt="" /> <span>{lang}wsip.publication.object.subscribe{/lang}</span></a></li>
									{else}
										<li><a href="index.php?action=PublicationObjectUnsubscribe&amp;publicationType=article&amp;publicationObjectID={@$article->articleID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" title="{lang}wsip.publication.object.unsubscribe{/lang}"><img src="{icon}publicationObjectUnsubscribeS.png{/icon}" alt="" /> <span>{lang}wsip.publication.object.unsubscribe{/lang}</span></a></li>
									{/if}
								{/if}
								{if $additionalSmallButtons|isset}{@$additionalSmallButtons}{/if}
							</ul>
						</div>
					</div>
				</div>
			</div>
			
			{if MODULE_COMMENT && ARTICLE_ENABLE_COMMENTS && $article->enableComments}{include file='publicationObjectComments' publicationObj=$article sandbox=false}{/if}
			
			{if $additionalContents|isset}{@$additionalContents}{/if}
		</div>
	</div>
	
	{if $additionalBoxes|isset}
		<div class="border infoBox">
			{if $additionalBoxes|isset}{@$additionalBoxes}{/if}
		</div>
	{/if}
	
</div>

{include file='footer' sandbox=false}

</body>
</html>