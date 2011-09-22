{if !$commentUsername|isset && $publicationObj->isCommentable() && $action != 'edit'}{assign var=commentUsername value=$username}{/if}

{if $comments|count > 0}
	<a id="comments"></a>
	<div class="contentBox publicationObjectComments">
		<h4 class="subHeadline">{lang}wsip.publication.object.comments{/lang} <span>({#$items})</span></h4>
		
		<div class="contentHeader">
			{pages print=true assign=pagesOutput link=$publicationObj->getURL()|concat:'&pageNo=%d#comments':SID_ARG_2ND_NOT_ENCODED}
		</div>
		
		<ul class="dataList messages">
			{assign var='messageNumber' value=$items-$startIndex+1}
			{foreach from=$comments item=commentObj}
				<li class="{cycle values='container-1,container-2'}">
					<a id="comment{@$commentObj->commentID}"></a>
					<div class="containerIcon">
						{if $commentObj->getUser()->getAvatar()}
							{assign var=x value=$commentObj->getUser()->getAvatar()->setMaxSize(24, 24)}
							{if $commentObj->userID}<a href="index.php?page=User&amp;userID={@$commentObj->userID}{@SID_ARG_2ND}" title="{lang username=$commentObj->username}wcf.user.viewProfile{/lang}">{/if}{@$commentObj->getUser()->getAvatar()}{if $commentObj->userID}</a>{/if}
						{else}
							{if $commentObj->userID}<a href="index.php?page=User&amp;userID={@$commentObj->userID}{@SID_ARG_2ND}" title="{lang username=$commentObj->username}wcf.user.viewProfile{/lang}">{/if}<img src="{@RELATIVE_WCF_DIR}images/avatars/avatar-default.png" alt="" style="width: 24px; height: 24px" />{if $commentObj->userID}</a>{/if}
						{/if}
					</div>
					<div class="containerContent">
						{if $action == 'edit' && $commentID == $commentObj->commentID}
							<form method="post" action="{$publicationObj->getURL()}&amp;commentID={@$commentObj->commentID}&amp;action=edit">
								<div{if $errorField == 'comment'} class="formError"{/if}>
									<textarea name="comment" id="comment" rows="10" cols="40">{$comment}</textarea>
									{if $errorField == 'comment'}
										<p class="innerError">
											{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
											{if $errorType == 'tooLong'}{lang}wsip.publication.object.comment.error.tooLong{/lang}{/if}
										</p>
									{/if}
								</div>
								<div class="formSubmit">
									<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
									<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
									{@SID_INPUT_TAG}
								</div>
							</form>
						{else}
							<div class="buttons">
								{if $commentObj->isEditable($publicationObj)}<a href="{$publicationObj->getURL()}&amp;commentID={@$commentObj->commentID}&amp;action=edit{@SID_ARG_2ND}#comment{@$commentObj->commentID}" title="{lang}wsip.publication.object.comment.edit{/lang}"><img src="{icon}editS.png{/icon}" alt="" /></a>{/if}
								{if $commentObj->isDeletable($publicationObj)}<a href="index.php?action=PublicationObjectCommentDelete&amp;commentID={@$commentObj->commentID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" onclick="return confirm('{lang}wsip.publication.object.comment.delete.sure{/lang}')" title="{lang}wsip.publication.object.comment.delete{/lang}"><img src="{icon}deleteS.png{/icon}" alt="" /></a>{/if}
								<a href="{$publicationObj->getURL()}&amp;commentID={@$commentObj->commentID}{@SID_ARG_2ND}#comment{@$commentObj->commentID}" title="{lang}wsip.publication.object.comment.permalink{/lang}" class="messageNumber extraButton">{#$messageNumber}</a>
							</div>
							<p class="firstPost smallFont light">{lang}wsip.publication.object.comment.by{/lang} {if $commentObj->userID}<a href="index.php?page=User&amp;userID={@$commentObj->userID}{@SID_ARG_2ND}">{$commentObj->username}</a>{else}{$commentObj->username}{/if} ({@$commentObj->time|time})</p>
							<p>{@$commentObj->getFormattedComment()}</p>
						{/if}
					</div>
				</li>
				{assign var='messageNumber' value=$messageNumber-1}
			{/foreach}
		</ul>
		
		<div class="contentFooter">
			{@$pagesOutput}
		</div>
		
		<div class="buttonBar">
			<div class="smallButtons">
				<ul>
					<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="{lang}wcf.global.scrollUp{/lang}" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
				</ul>
			</div>
		</div>
	</div>
{/if}

{if $publicationObj->isCommentable() && $action != 'edit'}
	{assign var=username value=$commentUsername}
	<div class="contentBox">
		<form method="post" action="{$publicationObj->getURL()}&amp;action=add">
			<fieldset>
				<legend>{lang}wsip.publication.object.comment.add{/lang}</legend>
				
				{if !$this->user->userID}
					<div class="formElement{if $errorField == 'username'} formError{/if}">
						<div class="formFieldLabel">
							<label for="username">{lang}wcf.user.username{/lang}</label>
						</div>
						<div class="formField">
							<input type="text" class="inputText" name="username" id="username" value="{$username}" />
							{if $errorField == 'username'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
									{if $errorType == 'notValid'}{lang}wcf.user.error.username.notValid{/lang}{/if}
									{if $errorType == 'notAvailable'}{lang}wcf.user.error.username.notUnique{/lang}{/if}
								</p>
							{/if}
						</div>
					</div>
				{/if}
				
				<div class="formElement{if $action == 'add' && $errorField == 'comment'} formError{/if}">
					<div class="formFieldLabel">
						<label for="comment">{lang}wsip.publication.object.comment{/lang}</label>
					</div>
					<div class="formField">
						<textarea name="comment" id="comment" rows="10" cols="40">{$comment}</textarea>
						{if $errorField == 'comment' && $action == 'add'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								{if $errorType == 'tooLong'}{lang}wsip.publication.object.comment.error.tooLong{/lang}{/if}
							</p>
						{/if}
					</div>
				</div>
				
				{include file='captcha' enableFieldset=false}
			</fieldset>
			
			<div class="formSubmit">
				<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
				<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
				{@SID_INPUT_TAG}
			</div>
		</form>
	</div>
{/if}