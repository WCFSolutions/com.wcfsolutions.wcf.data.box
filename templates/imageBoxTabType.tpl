<div class="container-1">
	<p class="image border container-4">
		{if $boxTab->enableFullsize || $boxTab->imageLink != 'http://'}
			<a href="{if $boxTab->enableFullsize}{$boxTab->image}{else}{$boxTab->imageLink}{/if}"{if $boxTab->enableFullsize} class="enlargable"{/if} title="{$boxTab->title}"><img src="{$boxTab->image}" alt="{$boxTab->alternativeTitle}" /></a>
		{else}
			<img src="{$boxTab->image}" alt="{$boxTab->alternativeTitle}" />
		{/if}
		{if $boxTab->title}<span class="smallFont light">{$boxTab->title}</span>{/if}
	</p>
</div>