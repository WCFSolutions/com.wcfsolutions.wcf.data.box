{include file='header'}

<div class="mainHeadline">
	<img src="{@RELATIVE_WCF_DIR}icon/box{@$action|ucfirst}L.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wcf.acp.box.{@$action}{/lang}</h2>
	</div>
</div>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
	<p class="success">{lang}wcf.acp.box.{@$action}.success{/lang}</p>
{/if}

<script type="text/javascript">
	//<![CDATA[
	document.observe('dom:loaded', function() {
		var checkbox = $('enableTitle');
		if (checkbox) {
			checkbox.observe('change', function() {
				if (this.checked) {
					enableOptions('isClosable');
				}
				else {
					disableOptions('isClosable');
				}
			});
		}
		{if !$enableTitle}
			disableOptions('isClosable');
		{/if}
	});
	//]]>
</script>

<div class="contentHeader">
	<div class="largeButtons">
		<ul>
			<li><a href="index.php?page=BoxList&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}wcf.acp.menu.link.content.box.view{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/boxM.png" alt="" /> <span>{lang}wcf.acp.menu.link.content.box.view{/lang}</span></a></li>
			{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
		</ul>
	</div>
</div>
<form method="post" action="index.php?form=Box{@$action|ucfirst}">
	<div class="border content">
		<div class="container-1">
			<fieldset>
				<legend>{lang}wcf.acp.box.data{/lang}</legend>

				{if $action == 'edit'}
					<div class="formElement" id="languageIDDiv">
						<div class="formFieldLabel">
							<label for="languageID">{lang}wcf.acp.box.language{/lang}</label>
						</div>
						<div class="formField">
							<select name="languageID" id="languageID" onchange="location.href='index.php?form=BoxEdit&amp;boxID={@$boxID}&amp;languageID='+this.value+'&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}'">
								{foreach from=$languages key=availableLanguageID item=languageCode}
									<option value="{@$availableLanguageID}"{if $availableLanguageID == $languageID} selected="selected"{/if}>{lang}wcf.global.language.{@$languageCode}{/lang}</option>
								{/foreach}
							</select>
						</div>
						<div class="formFieldDesc hidden" id="languageIDHelpMessage">
							{lang}wcf.acp.box.language.description{/lang}
						</div>
					</div>
					<script type="text/javascript">//<![CDATA[
						inlineHelp.register('languageID');
					//]]></script>
				{/if}

				<div class="formElement{if $errorField == 'boxName'} formError{/if}" id="boxNameDiv">
					<div class="formFieldLabel">
						<label for="boxName">{lang}wcf.acp.box.boxName{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="boxName" name="boxName" value="{$boxName}" />
						{if $errorField == 'boxName'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
					<div class="formFieldDesc hidden" id="boxNameHelpMessage">
						<p>{lang}wcf.acp.box.boxName.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('boxName');
				//]]></script>

				<div class="formElement" id="descriptionDiv">
					<div class="formFieldLabel">
						<label for="description">{lang}wcf.acp.box.description{/lang}</label>
					</div>
					<div class="formField">
						<textarea id="description" name="description" cols="40" rows="5">{$description}</textarea>
					</div>
					<div class="formFieldDesc hidden" id="descriptionHelpMessage">
						<p>{lang}wcf.acp.box.description.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('description');
				//]]></script>

				<div class="formElement" id="enableTitleDiv">
					<div class="formField">
						<label for="enableTitle"><input type="checkbox" name="enableTitle" id="enableTitle" value="1" {if $enableTitle}checked="checked" {/if}/> {lang}wcf.acp.box.enableTitle{/lang}</label>
					</div>
					<div class="formFieldDesc hidden" id="enableTitleHelpMessage">
						<p>{lang}wcf.acp.box.enableTitle.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('enableTitle');
				//]]></script>

				<div class="formElement" id="isClosableDiv">
					<div class="formField">
						<label for="isClosable"><input type="checkbox" name="isClosable" id="isClosable" value="1" {if $isClosable}checked="checked" {/if}/> {lang}wcf.acp.box.isClosable{/lang}</label>
					</div>
					<div class="formFieldDesc hidden" id="isClosableHelpMessage">
						<p>{lang}wcf.acp.box.isClosable.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('isClosable');
				//]]></script>

				{if $additionalFields|isset}{@$additionalFields}{/if}
			</fieldset>

			{if $additionalFieldSets|isset}{@$additionalFieldSets}{/if}
		</div>
	</div>

	<div class="formSubmit">
		<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
		<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
 		{@SID_INPUT_TAG}
 		{if $boxID|isset}<input type="hidden" name="boxID" value="{@$boxID}" />{/if}
 	</div>
</form>

{include file='footer'}