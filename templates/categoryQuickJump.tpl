{if $categoryQuickJumpOptions|count}
	<form method="get" action="index.php" class="quickJump">
		<div>
			<input type="hidden" name="page" value="{$pageName}" />
			<select name="categoryID" onchange="if (this.options[this.selectedIndex].value != 0) this.form.submit()">
				<option value="0">{lang}wsip.category.quickJump.title{/lang}</option>
				<option value="0">-----------------------</option>
				{if $category|isset}
					{htmloptions options=$categoryQuickJumpOptions selected=$category->categoryID disableEncoding=true}
				{else}
					{htmloptions options=$categoryQuickJumpOptions disableEncoding=true}
				{/if}
			</select>
			
			{@SID_INPUT_TAG}
			<input type="image" class="inputImage" src="{icon}submitS.png{/icon}" alt="{lang}wcf.global.button.submit{/lang}" />
		</div>
	</form>
{/if}