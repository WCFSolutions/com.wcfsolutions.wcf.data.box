{include file='header'}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>
<script type="text/javascript">
	//<![CDATA[
	document.observe("dom:loaded", function() {
		var boxTabList = $('boxTabList');
		if (boxTabList) {
			boxTabList.addClassName('dragable');
			var startValue = {if $sortOrder == 'ASC'}{@$itemsPerPage} * ({@$pageNo} - 1) + 1{else}{$items} - {@$itemsPerPage} * ({@$pageNo} - 1){/if};
			
			Sortable.create(boxTabList, { 
				tag: 'tr',
				onUpdate: function(list) {
					var rows = list.select('tr');
					var showOrder = 0;
					var newShowOrder = 0;
					rows.each(function(row, i) {
						row.className = 'container-' + (i % 2 == 0 ? '1' : '2') + (row.hasClassName('marked') ? ' marked' : '');
						showOrder = row.select('.columnNumbers')[0];
						newShowOrder = {if $sortOrder == 'ASC'}i + startValue{else}startValue - i{/if};
						if (newShowOrder != showOrder.innerHTML) {
							showOrder.update(newShowOrder);
							new Ajax.Request('index.php?action=BoxTabSort&boxTabID='+row.id.gsub('boxTabRow_', '')+SID_ARG_2ND, { method: 'post', parameters: { showOrder: newShowOrder } } );
						}
					});
				}
			});
		}	
	});
</script>

<div class="mainHeadline">
	<img src="{@RELATIVE_WCF_DIR}icon/boxTabL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wcf.acp.box.tab.view{/lang}</h2>
		{if $boxID}<p>{lang}{$box->getTitle()}{/lang}</p>{/if}
	</div>
</div>

{if $deletedBoxTabID}
	<p class="success">{lang}wcf.acp.box.tab.delete.success{/lang}</p>	
{/if}

{if $boxes|count}
	<div class="contentHeader">
		{pages print=true assign=pagesLinks link="index.php?page=BoxTabList&boxID=$boxID&pageNo=%d&packageID="|concat:PACKAGE_ID:SID_ARG_2ND_NOT_ENCODED}
		{if $this->user->getPermission('admin.box.canAddBoxTab')}
			<div class="largeButtons">
				<ul><li><a href="index.php?form=BoxTabAdd&amp;boxID={@$boxID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/boxTabAddM.png" alt="" title="{lang}wcf.acp.box.tab.add{/lang}" /> <span>{lang}wcf.acp.box.tab.add{/lang}</span></a></li></ul>
			</div>
		{/if}
	</div>
	
	<fieldset>
		<legend>{lang}wcf.acp.box.tab.box{/lang}</legend>
		<div class="formElement" id="boxDiv">
			<div class="formFieldLabel">
				<label for="boxChange">{lang}wcf.acp.box.tab.box{/lang}</label>
			</div>
			<div class="formField">
				<select id="boxChange" onchange="document.location.href=fixURL('index.php?page=BoxTabList&amp;boxID='+this.options[this.selectedIndex].value+'&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}')">
					<option value="0"></option>
					{htmloptions options=$boxes selected=$boxID}
				</select>
			</div>
			<div class="formFieldDesc hidden" id="boxHelpMessage">
				{lang}wcf.acp.box.tab.box.description{/lang}
			</div>
		</div>
		<script type="text/javascript">//<![CDATA[
			inlineHelp.register('box');
		//]]></script>
	</fieldset>
{else}
	<div class="border content">
		<div class="container-1">
			<p>{lang}wcf.acp.box.tab.view.count.noBoxes{/lang}</p>
		</div>
	</div>
{/if}

{if $boxID}
	{if $boxTabs|count}
		<div class="border titleBarPanel">
			<div class="containerHead"><h3>{lang}wcf.acp.box.tab.view.count{/lang}</h3></div>
		</div>
		<div class="border borderMarginRemove">
			<table class="tableList">
				<thead>
					<tr class="tableHead">
						<th class="columnBoxTabID{if $sortField == 'boxTabID'} active{/if}" colspan="2"><div><a href="index.php?page=BoxTabList&amp;boxID={@$boxID}&amp;pageNo={@$pageNo}&amp;sortField=boxTabID&amp;sortOrder={if $sortField == 'boxTabID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{lang}wcf.acp.box.tab.boxTabID{/lang}{if $sortField == 'boxTabID'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
						<th class="columnBoxTab{if $sortField == 'boxTab'} active{/if}"><div><a href="index.php?page=BoxTabList&amp;boxID={@$boxID}&amp;pageNo={@$pageNo}&amp;sortField=boxTab&amp;sortOrder={if $sortField == 'boxTab' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{lang}wcf.acp.box.tab.boxTab{/lang}{if $sortField == 'boxTab'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
						<th class="columnBoxTabType{if $sortField == 'boxTabType'} active{/if}"><div><a href="index.php?page=BoxTabList&amp;boxID={@$boxID}&amp;pageNo={@$pageNo}&amp;sortField=boxTabType&amp;sortOrder={if $sortField == 'boxTabType' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{lang}wcf.acp.box.tab.boxTabType{/lang}{if $sortField == 'boxTabType'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
						<th class="columnShowOrder{if $sortField == 'showOrder'} active{/if}"><div><a href="index.php?page=BoxTabList&amp;boxID={@$boxID}&amp;pageNo={@$pageNo}&amp;sortField=showOrder&amp;sortOrder={if $sortField == 'showOrder' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{lang}wcf.acp.box.tab.showOrder{/lang}{if $sortField == 'showOrder'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
						
						{if $additionalColumnHeads|isset}{@$additionalColumnHeads}{/if}
					</tr>
				</thead>
				<tbody id="boxTabList">
					{foreach from=$boxTabs item=boxTab}
						<tr class="{cycle values="container-1,container-2"}" id="boxTabRow_{@$boxTab->boxTabID}">
							<td class="columnIcon">
								{if $this->user->getPermission('admin.box.canEditBoxTab')}
									<a href="index.php?form=BoxTabEdit&amp;boxTabID={@$boxTab->boxTabID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/editS.png" alt="" title="{lang}wcf.acp.box.tab.edit{/lang}" /></a>
								{else}
									<img src="{@RELATIVE_WCF_DIR}icon/editDisabledS.png" alt="" title="{lang}wcf.acp.box.tab.edit{/lang}" />
								{/if}
								{if $this->user->getPermission('admin.box.canDeleteBoxTab')}
									<a onclick="return confirm('{lang}wcf.acp.box.tab.delete.sure{/lang}')" href="index.php?action=BoxTabDelete&amp;boxTabID={@$boxTab->boxTabID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/deleteS.png" alt="" title="{lang}wcf.acp.box.tab.delete{/lang}" /></a>
								{else}
									<img src="{@RELATIVE_WCF_DIR}icon/deleteDisabledS.png" alt="" title="{lang}wcf.acp.box.tab.delete{/lang}" />
								{/if}
								
								{if $additionalButtons.$boxTab->boxTabID|isset}{@$additionalButtons.$boxTab->boxTabID}{/if}
							</td>
							<td class="columnBoxTabID columnID">{@$boxTab->boxTabID}</td>
							<td class="columnBoxTab columnText">
								{if $this->user->getPermission('admin.box.canEditBoxTab')}
									<a href="index.php?form=BoxTabEdit&amp;boxTabID={@$boxTab->boxTabID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{$boxTab->getTitle()}</a>
								{else}
									{$boxTab->getTitle()}
								{/if}
							</td>
							<td class="columnBoxTabType columnText">{lang}wcf.acp.box.tab.type.{$boxTab->boxTabType}{/lang}</td>
							<td class="columnShowOrder columnNumbers">{@$boxTab->showOrder}</td>
							
							{if $additionalColumns.$boxTab->boxTabID|isset}{@$additionalColumns.$sboxTab->boxTabID}{/if}
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
		
		<div class="contentFooter">
			{@$pagesLinks}
			
			{if $this->user->getPermission('admin.box.canAddBoxTab')}
				<div class="largeButtons">
					<ul><li><a href="index.php?form=BoxTabAdd&amp;boxID={@$boxID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/boxTabAddM.png" alt="" title="{lang}wcf.acp.box.tab.add{/lang}" /> <span>{lang}wcf.acp.box.tab.add{/lang}</span></a></li></ul>
				</div>
			{/if}
		</div>
	{else}
		<div class="border content">
			<div class="container-1">
				<p>{lang}wcf.acp.box.tab.view.count.noBoxTabs{/lang}</p>
			</div>
		</div>
	{/if}
{/if}

{include file='footer'}