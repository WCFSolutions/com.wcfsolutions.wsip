{include file='header'}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/ItemListEditor.class.js"></script>
<script type="text/javascript">
	//<![CDATA[
	document.observe("dom:loaded", function() {
		var boxList = $('boxList');
		if (boxList) {
			boxList.addClassName('dragable');
			
			Sortable.create(boxList, { 
				tag: 'tr',
				onUpdate: function(list) {
					var rows = list.select('tr');
					var showOrder = 0;
					var newShowOrder = 0;
					rows.each(function(row, i) {
						row.className = 'container-' + (i % 2 == 0 ? '1' : '2') + (row.hasClassName('marked') ? ' marked' : '');
						showOrder = row.select('.columnNumbers')[0];
						newShowOrder = i + 1;
						if (newShowOrder != showOrder.innerHTML) {
							showOrder.update(newShowOrder);
							new Ajax.Request('index.php?action=ContentItemBoxSort&contentItemID={@$contentItemID}&boxID='+row.id.gsub('boxRow_', '')+SID_ARG_2ND, { method: 'post', parameters: { showOrder: newShowOrder } } );
						}
					});
				}
			});
		}
	});
	//]]>
</script>

<div class="mainHeadline">
	<img src="{@RELATIVE_WSIP_DIR}icon/contentItemBoxAssignmentL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wsip.acp.contentItem.boxAssignment{/lang}</h2>
		{if $contentItemID}<p>{$contentItem->getTitle()}</p>{/if}
	</div>
</div>

{if $removedBoxID}
	<p class="success">{lang}wsip.acp.contentItem.boxAssignment.box.remove.success{/lang}</p>	
{/if}

{if $successfulSorting}
	<p class="success">{lang}wsip.acp.contentItem.boxAssignment.box.sort.success{/lang}</p>	
{/if}

{if $contentItemOptions|count}
	<fieldset>
		<legend>{lang}wsip.acp.contentItem.boxAssignment.contentItem{/lang}</legend>
		<div class="formElement" id="contentItemDiv">
			<div class="formFieldLabel">
				<label for="contentItemChange">{lang}wsip.acp.contentItem.boxAssignment.contentItem{/lang}</label>
			</div>
			<div class="formField">
				<select id="contentItemChange" onchange="document.location.href=fixURL('index.php?page=ContentItemBoxAssignment&amp;contentItemID='+this.options[this.selectedIndex].value+'&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}')">
					<option value="0"></option>
					{htmloptions options=$contentItemOptions selected=$contentItemID disableEncoding=true}
				</select>
			</div>
			<div class="formFieldDesc hidden" id="contentItemHelpMessage">
				{lang}wsip.acp.contentItem.boxAssignment.contentItem.description{/lang}
			</div>
		</div>
		<script type="text/javascript">//<![CDATA[
			inlineHelp.register('contentItem');
		//]]></script>
	</fieldset>
{else}
	<div class="border content">
		<div class="container-1">
			<p>{lang}wsip.acp.contentItem.boxAssignment.count.noContentItems{/lang}</p>
		</div>
	</div>
{/if}

{if $contentItemID && $contentItem->isBoxContainer()}
	<div class="border titleBarPanel">
		<div class="containerHead"><h3>{lang}wsip.acp.contentItem.boxAssignment.boxes{/lang}</h3></div>
	</div>
	{if $boxes|count}
		<div class="border borderMarginRemove">
			<table class="tableList">
				<thead>
					<tr class="tableHead">
						<th class="columnBoxID" colspan="2"><div><span class="emptyHead">{lang}wsip.acp.contentItem.boxAssignment.box.boxID{/lang}</span></div></th>
						<th class="columnBox"><div><span class="emptyHead">{lang}wsip.acp.contentItem.boxAssignment.box.box{/lang}</span></div></th>
						<th class="columnShowOrder"><div><span class="emptyHead">{lang}wsip.acp.contentItem.boxAssignment.box.showOrder{/lang}</span></div></th>
						
						{if $additionalColumnHeads|isset}{@$additionalColumnHeads}{/if}
					</tr>
				</thead>
				<tbody id="boxList">
					{foreach from=$boxes item=child}
						{assign var=box value=$child.box}
						<tr class="{cycle values="container-1,container-2"}" id="boxRow_{@$box->boxID}">
							<td class="columnIcon">
								{if $this->user->getPermission('admin.box.canEditBoxLayout')}
									<a href="index.php?action=ContentItemBoxRemove&amp;contentItemID={@$contentItemID}&amp;boxID={@$box->boxID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" onclick="return confirm('{lang}wsip.acp.contentItem.boxAssignment.box.remove.sure{/lang}')" title="{lang}wsip.acp.contentItem.boxAssignment.box.remove{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/deleteS.png" alt="" /></a>
								{else}
									<img src="{@RELATIVE_WCF_DIR}icon/deleteDisabledS.png" alt="" title="{lang}wsip.acp.contentItem.boxAssignment.box.removeDisabled{/lang}" />
								{/if}
								
								{if $additionalButtons.$box->boxID|isset}{@$additionalButtons.$box->boxID}{/if}
							</td>
							<td class="columnBoxID columnID">{@$box->boxID}</td>
							<td class="columnBox columnText">
								{$box->getTitle()}
							</td>
							<td class="columnShowOrder columnNumbers">{@$child.showOrder}</td>
							
							{if $additionalColumns.$box->boxID|isset}{@$additionalColumns.$box->boxID}{/if}
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
	{/if}
	{if $boxOptions|count}
		<form method="post" action="index.php?page=ContentItemBoxAssignment">
			<div class="border content borderMarginRemove">
				<div class="container-1">
					<fieldset>
						<legend>{lang}wsip.acp.contentItem.boxAssignment.box.add{/lang}</legend>
						<div class="formElement{if $errorField == 'boxID'} formError{/if}">
							<div class="formFieldLabel">
								<label for="boxID">{lang}wsip.acp.contentItem.boxAssignment.box{/lang}</label>
							</div>
							<div class="formField">
								<select name="boxID" id="boxID">
									{htmloptions options=$boxOptions selected=$boxID disableEncoding=true}
								</select>
								<input type="submit" accesskey="s" value="{lang}wsip.acp.contentItem.boxAssignment.box.button.add{/lang}" />
								<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
								<input type="hidden" name="contentItemID" value="{@$contentItemID}" />
								{@SID_INPUT_TAG}
								{if $errorField == 'boxID'}
									<p class="innerError">
										{if $errorType == 'invalid'}{lang}wsip.acp.contentItem.boxAssignment.box.invalid{/lang}{/if}
									</p>
								{/if}
							</div>
						</div>
					</fieldset>
				</div>
			</div>
		</form>
	{/if}
{/if}

{include file='footer'}