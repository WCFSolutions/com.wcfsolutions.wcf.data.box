<div class="container-1">
	{if $boxTab->thumbnailImage}
		<p class="thumbnailImage border container-4">
			<a href="{if $boxTab->thumbnailEnableFullsize}{$boxTab->thumbnailImage}{else}{$boxTab->thumbnailImageLink}{/if}" class="enlargable" title="{$boxTab->thumbnailTitle}"><img src="{$boxTab->thumbnailImage}" alt="{$boxTab->thumbnailAlternativeTitle}" /></a>
			{if $boxTab->thumbnailTitle}<span class="smallFont light">{$boxTab->thumbnailTitle}</span>{/if}
		</p>
	{/if}
	{@$boxTabData}
</div>