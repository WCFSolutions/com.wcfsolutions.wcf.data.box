{include file='header'}

<div class="mainHeadline">
	<img src="{@RELATIVE_WCF_DIR}icon/boxLayoutL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wcf.acp.box.layout.view{/lang}</h2>
	</div>
</div>

{if $deletedBoxLayoutID}
	<p class="success">{lang}wcf.acp.box.layout.delete.success{/lang}</p>	
{/if}

<div class="contentHeader">
	{pages print=true assign=pagesLinks link="index.php?page=BoxLayoutList&pageNo=%d&packageID="|concat:PACKAGE_ID:SID_ARG_2ND_NOT_ENCODED}
	{if $this->user->getPermission('admin.box.canAddBoxLayout')}
		<div class="largeButtons">
			<ul><li><a href="index.php?form=BoxLayoutAdd&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/boxLayoutAddM.png" alt="" title="{lang}wcf.acp.box.layout.add{/lang}" /> <span>{lang}wcf.acp.box.layout.add{/lang}</span></a></li></ul>
		</div>
	{/if}
</div>

{if $boxLayouts|count}
	<div class="border titleBarPanel">
		<div class="containerHead"><h3>{lang}wcf.acp.box.layout.view.count{/lang}</h3></div>
	</div>
	<div class="border borderMarginRemove">
		<table class="tableList">
			<thead>
				<tr class="tableHead">
					<th class="columnBoxLayoutID{if $sortField == 'boxLayoutID'} active{/if}" colspan="2"><div><a href="index.php?page=BoxLayoutList&amp;pageNo={@$pageNo}&amp;sortField=boxLayoutID&amp;sortOrder={if $sortField == 'boxLayoutID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{lang}wcf.acp.box.layout.boxLayoutID{/lang}{if $sortField == 'boxLayoutID'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
					<th class="columnBoxLayout{if $sortField == 'title'} active{/if}"><div><a href="index.php?page=BoxLayoutList&amp;pageNo={@$pageNo}&amp;sortField=title&amp;sortOrder={if $sortField == 'title' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{lang}wcf.acp.box.layout.title{/lang}{if $sortField == 'title'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
					<th class="columnBoxLayoutBoxes{if $sortField == 'boxes'} active{/if}"><div><a href="index.php?page=BoxLayoutList&amp;pageNo={@$pageNo}&amp;sortField=boxes&amp;sortOrder={if $sortField == 'boxes' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{lang}wcf.acp.box.layout.boxes{/lang}{if $sortField == 'boxes'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
					
					{if $additionalColumnHeads|isset}{@$additionalColumnHeads}{/if}
				</tr>
			</thead>
			<tbody id="boxLayoutList">
				{foreach from=$boxLayouts item=boxLayout}
					<tr class="{cycle values="container-1,container-2"}">
						<td class="columnIcon">
							{if $this->user->getPermission('admin.box.canEditBoxLayout')}
								{if $boxLayout->isDefault}
									<img src="{@RELATIVE_WCF_DIR}icon/defaultDisabledS.png" alt="" title="{lang}wcf.acp.box.layout.defaultDisabled{/lang}" />
								{else}
									<a href="index.php?action=BoxLayoutSetAsDefault&amp;boxLayoutID={@$boxLayout->boxLayoutID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/defaultS.png" alt="" title="{lang}wcf.acp.box.layout.setAsDefault{/lang}" /></a>
								{/if}
								<a href="index.php?form=BoxLayoutEdit&amp;boxLayoutID={@$boxLayout->boxLayoutID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/editS.png" alt="" title="{lang}wcf.acp.box.layout.edit{/lang}" /></a>
							{else}
								<img src="{@RELATIVE_WCF_DIR}icon/defaultDisabledS.png" alt="" title="{lang}wcf.acp.box.layout.defaultDisabled{/lang}" />
								<img src="{@RELATIVE_WCF_DIR}icon/editDisabledS.png" alt="" title="{lang}wcf.acp.box.layout.edit{/lang}" />
							{/if}
							{if $this->user->getPermission('admin.box.canDeleteBoxLayout')}
								<a onclick="return confirm('{lang}wcf.acp.box.layout.delete.sure{/lang}')" href="index.php?action=BoxLayoutDelete&amp;boxLayoutID={@$boxLayout->boxLayoutID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/deleteS.png" alt="" title="{lang}wcf.acp.box.layout.delete{/lang}" /></a>
							{else}
								<img src="{@RELATIVE_WCF_DIR}icon/deleteDisabledS.png" alt="" title="{lang}wcf.acp.box.layout.delete{/lang}" />
							{/if}
							
							{if $additionalButtons.$boxLayout->boxLayoutID|isset}{@$additionalButtons.$boxLayout->boxLayoutID}{/if}
						</td>
						<td class="columnBoxLayoutID columnID">{@$boxLayout->boxLayoutID}</td>
						<td class="columnBoxLayout columnText">
							{if $this->user->getPermission('admin.box.canEditBoxLayout')}
								<a href="index.php?form=BoxLayoutEdit&amp;boxLayoutID={@$boxLayout->boxLayoutID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{$boxLayout->title}</a>
							{else}
								{$boxLayout->title}
							{/if}
						</td>
						<td class="columnBoxes columnNumbers"><a href="index.php?page=BoxLayoutBoxAssignment&amp;boxLayoutID={@$boxLayout->boxLayoutID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{@$boxLayout->boxes}</a></td>
						
						{if $additionalColumns.$boxLayout->boxLayoutID|isset}{@$additionalColumns.$boxLayout->boxLayoutID}{/if}
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
	
	<div class="contentFooter">
		{@$pagesLinks}
		
		{if $this->user->getPermission('admin.box.canAddBoxLayout')}
			<div class="largeButtons">
				<ul><li><a href="index.php?form=BoxLayoutAdd&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/boxLayoutAddM.png" alt="" title="{lang}wcf.acp.box.layout.add{/lang}" /> <span>{lang}wcf.acp.box.layout.add{/lang}</span></a></li></ul>
			</div>
		{/if}
	</div>
{else}
	<div class="border content">
		<div class="container-1">
			<p>{lang}wcf.acp.box.layout.view.count.noBoxLayouts{/lang}</p>
		</div>
	</div>
{/if}

{include file='footer'}