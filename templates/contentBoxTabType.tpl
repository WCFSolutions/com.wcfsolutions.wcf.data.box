<div class="container-1">
	{if $boxTab->thumbnailImage}
		<p class="thumbnailImage border container-4">
			{if $boxTab->thumbnailEnableFullsize || $boxTab->thumbnailImageLink != 'http://'}
				<a href="{if $boxTab->thumbnailEnableFullsize}{$boxTab->thumbnailImage}{else}{$boxTab->thumbnailImageLink}{/if}"{if $boxTab->thumbnailEnableFullsize} class="enlargable"{/if} title="{$boxTab->thumbnailTitle}"><img src="{$boxTab->thumbnailImage}" alt="{$boxTab->thumbnailAlternativeTitle}" /></a>
			{else}
				<img src="{$boxTab->thumbnailImage}" alt="{$boxTab->thumbnailAlternativeTitle}" />
			{/if}
			{if $boxTab->thumbnailTitle}<span class="smallFont light">{$boxTab->thumbnailTitle}</span>{/if}
		</p>
	{/if}
	{@$boxTabData}
</div>