<div class="contentBox">
	<h3 class="subHeadline"><a href="index.php?form=Search&amp;types[]=article&amp;userID={@$user->userID}{@SID_ARG_2ND}">{lang}wcf.user.profile.lastArticles{/lang}</a> <span>({#$numberOfArticles})</span></h3>

	<ul class="dataList">
		{foreach from=$articles item=article}
			<li class="{cycle values='container-1,container-2'}">
				<div class="containerIcon">
					<img src="{icon}articleM.png{/icon}" alt="" />
				</div>
				<div class="containerContent">
					<h4><a href="index.php?page=Article&amp;articleID={@$article->firstSectionID}{@SID_ARG_2ND}">{$article->subject}</a></h4>
					<p class="firstPost smallFont light">{@$article->time|time}</p>
				</div>
			</li>
		{/foreach}
	</ul>

	<div class="buttonBar">
		<div class="smallButtons">
			<ul>
				<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="{lang}wcf.global.scrollUp{/lang}" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
				<li><a href="index.php?form=Search&amp;types[]=article&amp;userID={@$user->userID}{@SID_ARG_2ND}" title="{lang}wcf.user.profile.allArticles{/lang}"><img src="{icon}articleS.png{/icon}" alt="" /> <span>{lang}wcf.user.profile.allArticles{/lang}</span></a></li>
			</ul>
		</div>
	</div>
</div>