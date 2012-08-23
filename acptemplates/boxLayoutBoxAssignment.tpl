{include file='header'}
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
							new Ajax.Request('index.php?action=BoxLayoutBoxSort&boxLayoutID={@$boxLayoutID}&boxPositionID={@$boxPositionID}&boxID='+row.id.gsub('boxRow_', '')+SID_ARG_2ND, { method: 'post', parameters: { showOrder: newShowOrder } } );
						}
					});
				}
			});
		}
	});
	//]]>
</script>

<div class="mainHeadline">
	<img src="{@RELATIVE_WCF_DIR}icon/boxLayoutBoxAssignmentL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wcf.acp.box.layout.boxAssignment{/lang}</h2>
	</div>
</div>

{if $removedBoxID}
	<p class="success">{lang}wcf.acp.box.layout.boxAssignment.box.remove.success{/lang}</p>
{/if}

{if $boxLayoutOptions|count}
	<fieldset>
		<legend>{lang}wcf.acp.box.layout.boxAssignment.boxLayout{/lang}</legend>
		<div class="formElement" id="boxLayoutDiv">
			<div class="formFieldLabel">
				<label for="boxLayoutChange">{lang}wcf.acp.box.layout.boxAssignment.boxLayout{/lang}</label>
			</div>
			<div class="formField">
				<select id="boxLayoutChange" onchange="document.location.href=fixURL('index.php?page=BoxLayoutBoxAssignment&amp;boxLayoutID='+this.options[this.selectedIndex].value+'&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}')">
					<option value="0"></option>
					{htmloptions options=$boxLayoutOptions selected=$boxLayoutID}
				</select>
			</div>
			<div class="formFieldDesc hidden" id="boxLayoutHelpMessage">
				{lang}wcf.acp.box.layout.boxAssignment.boxLayout.description{/lang}
			</div>
		</div>
		<script type="text/javascript">//<![CDATA[
			inlineHelp.register('boxLayout');
		//]]></script>
	</fieldset>
{/if}

{if $boxLayoutID}
	{if $boxPositionOptions|count}
		<fieldset>
			<legend>{lang}wcf.acp.box.layout.boxAssignment.boxPosition{/lang}</legend>
			<div class="formElement" id="boxPositionDiv">
				<div class="formFieldLabel">
					<label for="boxPositionChange">{lang}wcf.acp.box.layout.boxAssignment.boxPosition{/lang}</label>
				</div>
				<div class="formField">
					<select id="boxPositionChange" onchange="document.location.href=fixURL('index.php?page=BoxLayoutBoxAssignment&amp;boxLayoutID={@$boxLayoutID}&amp;boxPositionID='+this.options[this.selectedIndex].value+'&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}')">
						<option value="0"></option>
						{htmloptions options=$boxPositionOptions selected=$boxPositionID}
					</select>
				</div>
				<div class="formFieldDesc hidden" id="boxPositionHelpMessage">
					{lang}wcf.acp.box.layout.boxAssignment.boxPosition.description{/lang}
				</div>
			</div>
			<script type="text/javascript">//<![CDATA[
				inlineHelp.register('boxPosition');
			//]]></script>
		</fieldset>
	{/if}

	{if $boxPositionID}
		<div class="border titleBarPanel">
			<div class="containerHead"><h3>{lang}wcf.acp.box.layout.boxAssignment.boxes{/lang}</h3></div>
		</div>
		{if $boxes|count}
			<div class="border borderMarginRemove">
				<table class="tableList">
					<thead>
						<tr class="tableHead">
							<th class="columnBoxID" colspan="2"><div><span class="emptyHead">{lang}wcf.acp.box.layout.boxAssignment.box.boxID{/lang}</span></div></th>
							<th class="columnBox"><div><span class="emptyHead">{lang}wcf.acp.box.layout.boxAssignment.box.box{/lang}</span></div></th>
							<th class="columnShowOrder"><div><span class="emptyHead">{lang}wcf.acp.box.layout.boxAssignment.box.showOrder{/lang}</span></div></th>

							{if $additionalColumnHeads|isset}{@$additionalColumnHeads}{/if}
						</tr>
					</thead>
					<tbody id="boxList">
						{foreach from=$boxes item=child}
							{assign var=box value=$child.box}
							<tr class="{cycle values="container-1,container-2"}" id="boxRow_{@$box->boxID}">
								<td class="columnIcon">
									{if $this->user->getPermission('admin.box.canEditBoxLayout')}
										<a href="index.php?action=BoxLayoutBoxRemove&amp;boxLayoutID={@$boxLayout->boxLayoutID}&amp;boxPositionID={@$boxPosition->boxPositionID}&amp;boxID={@$box->boxID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" onclick="return confirm('{lang}wcf.acp.box.layout.boxAssignment.box.remove.sure{/lang}')" title="{lang}wcf.acp.box.layout.boxAssignment.box.remove{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/deleteS.png" alt="" /></a>
									{else}
										<img src="{@RELATIVE_WCF_DIR}icon/deleteDisabledS.png" alt="" title="{lang}wcf.acp.box.layout.boxAssignment.box.removeDisabled{/lang}" />
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
			<form method="post" action="index.php?page=BoxLayoutBoxAssignment">
				<div class="border content borderMarginRemove">
					<div class="container-1">
						<fieldset>
							<legend>{lang}wcf.acp.box.layout.boxAssignment.box.add{/lang}</legend>
							<div class="formElement{if $errorField == 'boxID'} formError{/if}">
								<div class="formFieldLabel">
									<label for="boxID">{lang}wcf.acp.box.layout.boxAssignment.box{/lang}</label>
								</div>
								<div class="formField">
									<select name="boxID" id="boxID">
										{htmloptions options=$boxOptions selected=$boxID disableEncoding=true}
									</select>
									<input type="submit" accesskey="s" value="{lang}wcf.acp.box.layout.boxAssignment.box.button.add{/lang}" />
									<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
									<input type="hidden" name="boxLayoutID" value="{@$boxLayoutID}" />
									<input type="hidden" name="boxPositionID" value="{@$boxPositionID}" />
									{@SID_INPUT_TAG}
									{if $errorField == 'boxID'}
										<p class="innerError">
											{if $errorType == 'invalid'}{lang}wcf.acp.box.layout.boxAssignment.box.invalid{/lang}{/if}
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
{/if}

{include file='footer'}