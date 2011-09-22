{include file="documentHeader"}
<head>
	<title>{lang}wsip.article.sections{/lang} - {$article->subject} - {lang}wsip.article.overview{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	
	{include file='headInclude' sandbox=false}
	{include file='imageViewer'}
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
		<li><a href="index.php?page=Article&amp;sectionID={@$article->firstSectionID}{@SID_ARG_2ND}"><img src="{icon}articleS.png{/icon}" alt="" /> <span>{$article->subject}</span></a> &raquo;</li>
	</ul>
	
	<div class="mainHeadline">
		<img src="{icon}articleL.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2><a href="index.php?page=ArticleSectionList&amp;articleID={@$articleID}{@SID_ARG_2ND}">{lang}wsip.article.sections{/lang}</a></h2>
		</div>
	</div>
	
	{if $userMessages|isset}{@$userMessages}{/if}
	
	{if $successfullSorting}
		<p class="success">{lang}wsip.article.section.sort.success{/lang}</p>	
	{/if}
	
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/ItemListEditor.class.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		function init() {
			{if $sections|count > 0 && $sections|count < 100 && $article->isEditable($category)}
				new ItemListEditor('articleSectionList', { tree: true, treeTag: 'ol' });
			{/if}
		}
		
		// when the dom is fully loaded, execute these scripts
		document.observe("dom:loaded", init);		
		//]]>
	</script>
	
	{if $article->isEditable($category)}
	<form method="post" action="index.php?action=ArticleSectionSort">
	{/if}	
		<div class="border content">
			<div class="container-1">
				<ol class="itemList" id="articleSectionList">
					{foreach from=$sections item=child}
						{assign var="section" value=$child.section}
							
						<li id="item_{@$section->sectionID}" class="deletable">
							{if $article->isEditable($category) || $article->isDeletable($category) || $child.additionalButtons|isset}
								<div class="buttons">
									{if $article->isEditable($category)}
										<a href="index.php?form=ArticleSectionEdit&amp;sectionID={@$section->sectionID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/editS.png" alt="" title="{lang}wsip.article.section.edit{/lang}" /></a>
									{/if}
									{if $article->isDeletable($category)}
										<a href="index.php?action=ArticleSectionDelete&amp;sectionID={@$section->sectionID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" title="{lang}wsip.article.section.delete{/lang}" class="deleteButton"><img src="{@RELATIVE_WCF_DIR}icon/deleteS.png" alt="" longdesc="{lang}wsip.article.section.delete.sure{/lang}"  /></a>
									{/if}
										
									{if $child.additionalButtons|isset}{@$child.additionalButtons}{/if}
								</div>
							{/if}
								
							<h3 class="itemListTitle">
								{if $article->isEditable($category)}
									<select name="articleSectionListPositions[{@$section->sectionID}][{@$section->parentSectionID}]">
										{section name='positions' loop=$child.maxPosition}
											<option value="{@$positions+1}"{if $positions+1 == $child.position} selected="selected"{/if}>{@$positions+1}</option>
										{/section}
									</select>
								{/if}
								
								ID-{@$section->sectionID} <a href="index.php?page=Article&amp;sectionID={@$section->sectionID}{@SID_ARG_2ND}" class="title">{$section->subject}</a>
							</h3>
						
						{if $child.hasChildren}<ol id="parentItem_{@$section->sectionID}">{else}<ol id="parentItem_{@$section->sectionID}"></ol></li>{/if}
						{if $child.openParents > 0}{@"</ol></li>"|str_repeat:$child.openParents}{/if}
					{/foreach}
				</ol>
			</div>
		</div>
	{if $article->isEditable($category)}
		<div class="formSubmit">
			<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
			<input type="reset" accesskey="r" id="reset" value="{lang}wcf.global.button.reset{/lang}" />
			<input type="hidden" name="articleID" value="{@$article->articleID}" />
			{@SID_INPUT_TAG}
			{@SECURITY_TOKEN_INPUT_TAG}
		</div>
	</form>
	{/if}

</div>

{include file='footer' sandbox=false}

</body>
</html>