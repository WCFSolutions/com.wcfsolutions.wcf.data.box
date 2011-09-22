<div class="container-1">
	{if $boxTabData.listItems|count}
		<{@$boxTabData.listTag} style="list-style-type: {@$boxTab->listStyleType}">
			{foreach from=$boxTabData.listItems item=listItem}
				<li>{@$listItem}</li>
			{/foreach}
		</{@$boxTabData.listTag}>
	{/if}
</div>