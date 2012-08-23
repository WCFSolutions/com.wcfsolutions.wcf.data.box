{include file='header'}

<div class="mainHeadline">
	<img src="{@RELATIVE_WCF_DIR}icon/boxTab{@$action|ucfirst}L.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wcf.acp.box.tab.{@$action}{/lang}</h2>
	</div>
</div>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
	<p class="success">{lang}wcf.acp.box.tab.{@$action}.success{/lang}</p>
{/if}

{if $ckeditor}
	{@$ckeditor->getConfigurationHTML()}
{/if}

<div class="contentHeader">
	<div class="largeButtons">
		<ul>
			<li><a href="index.php?page=BoxTabList{if $boxID}&amp;boxID={@$boxID}{/if}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}wcf.acp.menu.link.content.box.tab.view{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/boxTabM.png" alt="" /> <span>{lang}wcf.acp.menu.link.content.box.tab.view{/lang}</span></a></li>
			{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
		</ul>
	</div>
</div>

{if $action == 'add'}
	<fieldset>
		<legend>{lang}wcf.acp.box.tab.type{/lang}</legend>

		<div class="formElement" id="boxTabTypeDiv">
			<div class="formFieldLabel">
				<label for="boxTabTypeChange">{lang}wcf.acp.box.tab.type{/lang}</label>
			</div>
			<div class="formField">
				<select id="boxTabTypeChange" onchange="document.location.href=fixURL('index.php?form=BoxTabAdd{if $boxID}&amp;boxID={@$boxID}{/if}&amp;boxTabTypeID='+this.options[this.selectedIndex].value+'&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}')">
					{htmloptions options=$boxTabTypes selected=$boxTabTypeID disableEncoding=true}
				</select>
			</div>
			<div class="formFieldDesc hidden" id="boxTabTypeHelpMessage">
				{lang}wcf.acp.box.tab.type.description{/lang}
			</div>
		</div>
		<script type="text/javascript">//<![CDATA[
		inlineHelp.register('boxTabType');
		//]]></script>
	</fieldset>
{/if}

<form method="post" action="index.php?form=BoxTab{@$action|ucfirst}">
	<div class="border content">
		<div class="container-1">
			<fieldset>
				<legend>{lang}wcf.acp.box.tab.general{/lang}</legend>

				{if $action == 'edit'}
					<div class="formElement" id="languageIDDiv">
						<div class="formFieldLabel">
							<label for="languageID">{lang}wcf.acp.box.tab.language{/lang}</label>
						</div>
						<div class="formField">
							<select name="languageID" id="languageID" onchange="location.href='index.php?form=BoxTabEdit&amp;boxTabID={@$boxTabID}&amp;languageID='+this.value+'&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}'">
								{foreach from=$languages key=availableLanguageID item=languageCode}
									<option value="{@$availableLanguageID}"{if $availableLanguageID == $languageID} selected="selected"{/if}>{lang}wcf.global.language.{@$languageCode}{/lang}</option>
								{/foreach}
							</select>
						</div>
						<div class="formFieldDesc hidden" id="languageIDHelpMessage">
							{lang}wcf.acp.box.tab.language.description{/lang}
						</div>
					</div>
					<script type="text/javascript">//<![CDATA[
						inlineHelp.register('languageID');
					//]]></script>
				{/if}

				<div class="formElement" id="boxIDDiv">
					<div class="formFieldLabel">
						<label for="boxID">{lang}wcf.acp.box.tab.boxID{/lang}</label>
					</div>
					<div class="formField">
						<select name="boxID" id="boxID">
							{htmlOptions options=$boxes selected=$boxID}
						</select>
					</div>
					<div class="formFieldDesc hidden" id="boxIDHelpMessage">
						{lang}wcf.acp.box.tab.boxID.description{/lang}
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('boxID');
				//]]></script>

				<div class="formElement{if $errorType.boxTabName|isset} formError{/if}" id="boxTabNameDiv">
					<div class="formFieldLabel">
						<label for="boxTabName">{lang}wcf.acp.box.tab.boxTabName{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="boxTabName" name="boxTabName" value="{$boxTabName}" />
						{if $errorType.boxTabName|isset}
							<p class="innerError">
								{if $errorType.boxTabName == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
					<div class="formFieldDesc hidden" id="boxTabNameHelpMessage">
						<p>{lang}wcf.acp.box.tab.boxTabName.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('boxTabName');
				//]]></script>

				<div class="formElement" id="showOrderDiv">
					<div class="formFieldLabel">
						<label for="showOrder">{lang}wcf.acp.box.tab.showOrder{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" name="showOrder" id="showOrder" value="{$showOrder}" />
					</div>
					<div class="formFieldDesc hidden" id="showOrderHelpMessage">
						{lang}wcf.acp.box.tab.showOrder.description{/lang}
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('showOrder');
				//]]></script>

				{if $additionalGeneralFields|isset}{@$additionalGeneralFields}{/if}
			</fieldset>

			{if $additionalFields|isset}{@$additionalFields}{/if}

			{foreach from=$options item=categoryLevel1}
				<fieldset>
					<legend>{lang}wcf.acp.box.tab.option.category.{$categoryLevel1.categoryName}{/lang}</legend>
					<p class="description">{lang}wcf.acp.box.tab.option.category.{$categoryLevel1.categoryName}.description{/lang}</p>
					{include file='optionFieldList' options=$categoryLevel1.options langPrefix='wcf.acp.box.tab.option.'|concat:$boxTabType.boxTabType|concat:'.'}
				</fieldset>
			{/foreach}
		</div>
	</div>

	<div class="formSubmit">
		<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
		<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
 		{@SID_INPUT_TAG}
 		<input type="hidden" name="action" value="{@$action}" />
 		{if $boxTabID|isset}<input type="hidden" name="boxTabID" value="{@$boxTabID}" />{/if}
 		{if $action == 'add'}<input type="hidden" name="boxTabTypeID" value="{@$boxTabTypeID}" />{/if}
 	</div>
</form>

{include file='footer'}