{include file="documentHeader"}
<head>
	<title>{$entry->subject} - {lang}wsip.news.overview{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	
	{include file='headInclude' sandbox=false}
	{include file='imageViewer'}
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>
	{if $polls|isset}<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/Poll.class.js"></script>{/if}
	{if $permissions.canHandleNewsEntry}
		<script type="text/javascript">
			//<![CDATA[	
			var language = new Object();
			//]]>
		</script>
		{include file='newsEntryInlineEdit' pageType=newsEntry}
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
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">
	
	<ul class="breadCrumbs">
		<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img src="{icon}indexS.png{/icon}" alt="" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
		<li><a href="index.php?page=NewsOverview{@SID_ARG_2ND}"><img src="{icon}newsS.png{/icon}" alt="" /> <span>{lang}wsip.news.overview{/lang}</span></a> &raquo;</li>
		{foreach from=$category->getParentCategories() item=parentCategory}
			<li><a href="index.php?page=NewsOverview&amp;categoryID={@$parentCategory->categoryID}{@SID_ARG_2ND}"><img src="{icon}categoryS.png{/icon}" alt="" /> <span>{$parentCategory->getTitle()}</span></a> &raquo;</li>
		{/foreach}
		<li><a href="index.php?page=NewsOverview&amp;categoryID={@$category->categoryID}{@SID_ARG_2ND}"><img src="{icon}categoryS.png{/icon}" alt="" /> <span>{$category->getTitle()}</span></a> &raquo;</li>
	</ul>
	
	<div class="mainHeadline">
		<img id="newsEntryEdit{@$entry->entryID}" src="{icon}newsEntryL.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2 id="newsEntryTitle{@$entry->entryID}"><a href="index.php?page=NewsEntry&amp;entryID={@$entryID}{@SID_ARG_2ND}">{$entry->subject}</a></h2>
		</div>
	</div>
	
	{if $userMessages|isset}{@$userMessages}{/if}
	
	{if MODULE_COMMENT && NEWS_ENTRY_ENABLE_COMMENTS && $entry->enableComments && $entry->isCommentable() && $action != 'edit'}{assign var=commentUsername value=$username}{/if}
	
	<div class="border content">
		<div class="container-1 news">
			<div class="contentBox newsEntry">
				<p class="messageCount"><a href="index.php?page=NewsEntry&amp;entryID={@$entry->entryID}{@SID_ARG_2ND}" title="{lang messageNumber=$entry->entryID}wsip.news.entry.permalink{/lang}" class="messageNumber">{#$entry->entryID}</a></p>
				
				<h3 class="subHeadline">{lang}wsip.news.entry{/lang}</h3>
				
				<div class="contentHeader">
					{if NEWS_ENTRY_ENABLE_RATING}<p class="rating light smallFont">{lang}wsip.news.entry.rating{/lang}: <span id="com.wcfsolutions.wsip.news.entry-ratingOutput{@$entry->entryID}">{@$entry->getRatingOutput()}</span></p>{/if}
					
					<p class="light smallFont">{lang}wsip.news.entry.by{/lang} {if $entry->userID}<a href="index.php?page=User&amp;userID={@$entry->userID}{@SID_ARG_2ND}" title="{lang username=$entry->username}wcf.user.viewProfile{/lang}">{$entry->username}</a>{else}{$entry->username}{/if} ({@$entry->time|time})</p>
				</div>
				
				<div class="newsEntryInner">		
					{include pollID=$entry->pollID file='pollShow'}
					
					<div class="messageBody" id="newsEntryText{@$entry->entryID}">
						{@$entry->getFormattedMessage()}
					</div>
					
					{assign var="messageID" value=$entry->entryID}
					{assign var="author" value=$entry->getUser()}
					{include file='attachmentsShow'}
					
					<div class="buttonBar">
						{if $entry->publishingTime}<p class="light smallFont">{lang}wsip.news.entry.publishingTime{/lang}: {@$entry->publishingTime|time}{/if}
						<p class="light smallFont">{lang}wsip.news.entry.views{/lang}: {#$entry->views}{if $entry->getViewsPerDay() > 0} ({lang}wsip.news.entry.viewsPerDay{/lang}){/if}</p>
						
						{if $tags|count > 0}
							<p class="light smallFont">{lang}wsip.news.entry.tags{/lang}: {implode from=$tags item=tag}<a href="index.php?page=NewsOverview&amp;categoryID={@$entry->categoryID}&amp;tagID={@$tag->getID()}{@SID_ARG_2ND}">{$tag->getName()}</a>{/implode}</p>
						{/if}
						
						{if $socialBookmarks|isset}
							{@$socialBookmarks}
						{/if}
						
						{if NEWS_ENTRY_ENABLE_RATING}
							<div class="pageOptions rating">
								<span>{lang}wsip.news.entry.rate{/lang}</span>
								{include file='objectRating'}
								<div id="com.wcfsolutions.wsip.news.entry-rating{@$entry->entryID}"></div>
								<noscript>
									<form method="post" action="index.php?action=ObjectRating{@SID_ARG_2ND}">
										<div>
											<select id="newsEntryRatingSelect" name="rating">
												{section name=i start=1 loop=6}
													<option value="{@$i}"{if $i == $rating->getUserRating()} selected="selected"{/if}>{@$i}</option>
												{/section}
											</select>
											<input type="hidden" name="objectName" value="com.wcfsolutions.wsip.news.entry" />
											<input type="hidden" name="objectID" value="{@$entryID}" />
											<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
											<input type="hidden" name="url" value="index.php?page=NewsEntry&amp;entryID={@$entry->entryID}" />
											<input type="image" class="inputImage" src="{icon}submitS.png{/icon}" alt="{lang}wcf.global.button.submit{/lang}" />
										</div>
									</form>
								</noscript>
								<script type="text/javascript">
									//<![CDATA[
									objectRatingObj.initializeObject({
										currentRating: {@$rating->getUserRating()},
										objectID: {@$entryID},
										objectName: 'com.wcfsolutions.wsip.news.entry',
										packageID: {@PACKAGE_ID}
									});
									//]]>
								</script>
							</div>
						{/if}
					</div>
					
					<div class="buttonBar">
						<div class="smallButtons">
							<ul id="newsEntryButtons{@$entry->entryID}">
								<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="{lang}wcf.global.scrollUp{/lang}" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
								{if $entry->isEditable($category)}<li><a href="index.php?form=NewsEntryEdit&amp;entryID={@$entry->entryID}{@SID_ARG_2ND}" title="{lang}wsip.news.entry.edit{/lang}"><img src="{icon}editS.png{/icon}" alt="" /> <span>{lang}wcf.global.button.edit{/lang}</span></a></li>{/if}
								{if $this->user->userID}
									{if !$entry->isSubscribed()}
										<li><a href="index.php?action=PublicationObjectSubscribe&amp;publicationType=news&amp;publicationObjectID={@$entry->entryID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" title="{lang}wsip.publication.object.subscribe{/lang}"><img src="{icon}publicationObjectSubscribeS.png{/icon}" alt="" /> <span>{lang}wsip.publication.object.subscribe{/lang}</span></a></li>
									{else}
										<li><a href="index.php?action=PublicationObjectUnsubscribe&amp;publicationType=news&amp;publicationObjectID={@$entry->entryID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" title="{lang}wsip.publication.object.unsubscribe{/lang}"><img src="{icon}publicationObjectUnsubscribeS.png{/icon}" alt="" /> <span>{lang}wsip.publication.object.unsubscribe{/lang}</span></a></li>
									{/if}
								{/if}
								{if $additionalSmallButtons|isset}{@$additionalSmallButtons}{/if}
							</ul>
						</div>
					</div>
				</div>
			</div>
			
			{if MODULE_COMMENT && NEWS_ENTRY_ENABLE_COMMENTS && $entry->enableComments}{include file='publicationObjectComments' publicationObj=$entry sandbox=false}{/if}
			
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