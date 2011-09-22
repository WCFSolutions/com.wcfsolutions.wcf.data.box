{include file='header'}

<div class="mainHeadline">
	<img src="{@RELATIVE_WCF_DIR}icon/boxL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wcf.acp.box.view{/lang}</h2>
	</div>
</div>

{if $deletedBoxID}
	<p class="success">{lang}wcf.acp.box.delete.success{/lang}</p>	
{/if}

<div class="contentHeader">
	{pages print=true assign=pagesLinks link="index.php?page=BoxList&pageNo=%d&packageID="|concat:PACKAGE_ID:SID_ARG_2ND_NOT_ENCODED}
	{if $this->user->getPermission('admin.box.canAddBox')}
		<div class="largeButtons">
			<ul><li><a href="index.php?form=BoxAdd&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/boxAddM.png" alt="" title="{lang}wcf.acp.box.add{/lang}" /> <span>{lang}wcf.acp.box.add{/lang}</span></a></li></ul>
		</div>
	{/if}
</div>

{if $boxes|count}
	<div class="border titleBarPanel">
		<div class="containerHead"><h3>{lang}wcf.acp.box.view.count{/lang}</h3></div>
	</div>
	<div class="border borderMarginRemove">
		<table class="tableList">
			<thead>
				<tr class="tableHead">
					<th class="columnBoxID{if $sortField == 'boxID'} active{/if}" colspan="2"><div><a href="index.php?page=BoxList&amp;pageNo={@$pageNo}&amp;sortField=boxID&amp;sortOrder={if $sortField == 'boxID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{lang}wcf.acp.box.boxID{/lang}{if $sortField == 'boxID'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
					<th class="columnBox{if $sortField == 'box'} active{/if}"><div><a href="index.php?page=BoxList&amp;pageNo={@$pageNo}&amp;sortField=box&amp;sortOrder={if $sortField == 'box' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{lang}wcf.acp.box.box{/lang}{if $sortField == 'box'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
					<th class="columnBoxTabs{if $sortField == 'boxTabs'} active{/if}"><div><a href="index.php?page=BoxList&amp;pageNo={@$pageNo}&amp;sortField=boxTabs&amp;sortOrder={if $sortField == 'boxTabs' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{lang}wcf.acp.box.boxTabs{/lang}{if $sortField == 'boxTabs'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
					
					{if $additionalColumnHeads|isset}{@$additionalColumnHeads}{/if}
				</tr>
			</thead>
			<tbody id="boxList">
				{foreach from=$boxes item=box}
					<tr class="{cycle values="container-1,container-2"}">
						<td class="columnIcon">
							{if $this->user->getPermission('admin.box.canEditBox')}
								<a href="index.php?form=BoxEdit&amp;boxID={@$box->boxID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/editS.png" alt="" title="{lang}wcf.acp.box.edit{/lang}" /></a>
							{else}
								<img src="{@RELATIVE_WCF_DIR}icon/editDisabledS.png" alt="" title="{lang}wcf.acp.box.edit{/lang}" />
							{/if}
							{if $this->user->getPermission('admin.box.canDeleteBox')}
								<a onclick="return confirm('{lang}wcf.acp.box.delete.sure{/lang}')" href="index.php?action=BoxDelete&amp;boxID={@$box->boxID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/deleteS.png" alt="" title="{lang}wcf.acp.box.delete{/lang}" /></a>
							{else}
								<img src="{@RELATIVE_WCF_DIR}icon/deleteDisabledS.png" alt="" title="{lang}wcf.acp.box.delete{/lang}" />
							{/if}
							
							{if $additionalButtons.$box->boxID|isset}{@$additionalButtons.$box->boxID}{/if}
						</td>
						<td class="columnBoxID columnID">{@$box->boxID}</td>
						<td class="columnBox columnText">
							{if $this->user->getPermission('admin.box.canEditBox')}
								<a href="index.php?form=BoxEdit&amp;boxID={@$box->boxID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{$box->getTitle()}</a>
							{else}
								{$box->getTitle()}
							{/if}
						</td>
						<td class="columnBoxTabs columnNumbers"><a href="index.php?page=BoxTabList&amp;boxID={@$box->boxID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{@$box->boxTabs}</a></td>
						
						{if $additionalColumns.$box->boxID|isset}{@$additionalColumns.$box->boxID}{/if}
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
	
	<div class="contentFooter">
		{@$pagesLinks}
		
		{if $this->user->getPermission('admin.box.canAddBox')}
			<div class="largeButtons">
				<ul><li><a href="index.php?form=BoxAdd&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/boxAddM.png" alt="" title="{lang}wcf.acp.box.add{/lang}" /> <span>{lang}wcf.acp.box.add{/lang}</span></a></li></ul>
			</div>
		{/if}
	</div>
{else}
	<div class="border content">
		<div class="container-1">
			<p>{lang}wcf.acp.box.view.count.noBoxes{/lang}</p>
		</div>
	</div>
{/if}

{include file='footer'}