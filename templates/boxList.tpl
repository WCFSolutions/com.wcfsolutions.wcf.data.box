{if !$boxLayout|isset}
	{assign var=boxLayout value=$this->getBoxLayout()}
{/if}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/SubTabMenu.class.js"></script>
<div class="boxList">
	{foreach from=$boxLayout->getBoxesByPosition($boxPosition) item=box}
		{capture assign=boxPositionIdentifier}box{@$box->boxID}_{@$boxPosition}{/capture}
		{assign var=boxTabs value=$box->getBoxTabs()}
		<div class="contentBox">
			<div class="border">
				{if $box->enableTitle}
					{if $box->isClosable}
						<div class="containerHead">
							<div class="containerIcon">
								<a onclick="openList('{@$boxPositionIdentifier}', { save: true, openTitle: '{lang}wcf.box.open{/lang}', closeTitle: '{lang}wcf.box.close{/lang}' })"><img src="{icon}minusS.png{/icon}" id="{@$boxPositionIdentifier}Image" alt="" title="{lang}wcf.box.close{/lang}" /></a>
							</div>
							<div class="containerContent">
								<h3>{$box->getTitle()}</h3>
								{if $box->getFormattedDescription()}<p class="smallFont">{@$box->getFormattedDescription()}</p>{/if}
							</div>
						</div>
					{else}
						<div class="containerHead">
							<h3>{$box->getTitle()}</h3>
							{if $box->getFormattedDescription()}<p class="smallFont">{@$box->getFormattedDescription()}</p>{/if}
						</div>
					{/if}
				{/if}
				<div id="{@$boxPositionIdentifier}">
					{if $boxTabs|count > 1}
						<div class="subTabMenu" style="display: none;">
							<div class="containerHead">
								<ul>
									{foreach from=$box->getBoxTabs() item=boxTab}
										<li id="{@$boxPositionIdentifier}_{@$boxTab->boxTabID}"><a onclick="{@$boxPositionIdentifier}_tabMenu.showTabMenuContent('{@$boxPositionIdentifier}_{@$boxTab->boxTabID}');"><span>{$boxTab->getTitle()}</span></a></li>
									{/foreach}
								</ul>
							</div>
						</div>
					{/if}
					{foreach from=$boxTabs item=boxTab}
						{assign var=boxTabType value=$boxTab->getBoxTabType()}
						{assign var=boxTabData value=$boxTabType->getData($boxTab)}

						<div class="tabMenuContent" id="{@$boxPositionIdentifier}_{@$boxTab->boxTabID}-content">
							<noscript>
								<div class="subTabMenu">
									<div class="containerHead">
										<h4>{$boxTab->getTitle()}</h4>
									</div>
								</div>
							</noscript>
							{include file=$boxTab->getBoxTabType()->getTemplateName()}
						</div>
					{/foreach}
				</div>
			</div>
		</div>
		{if $boxTabs|count > 1 || $box->enableTitle && $box->isClosable}
			<script type="text/javascript">
				//<![CDATA[
				{if $boxTabs|count > 1}
					var {@$boxPositionIdentifier}_tabMenu = new SubTabMenu();
					onloadEvents.push(function() { {@$boxPositionIdentifier}_tabMenu.showTabMenuContent('{@$boxPositionIdentifier}_{@$box->getFirstBoxTabID()}'); });
				{/if}

				{if $box->enableTitle && $box->isClosable}
					initList('{@$boxPositionIdentifier}', {if $box->isClosed($boxPosition) == 1}0{else}1{/if});
				{/if}
				//]]>
			</script>
		{/if}
	{/foreach}
</div>