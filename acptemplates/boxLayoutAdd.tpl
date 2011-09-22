{include file='header'}

<div class="mainHeadline">
	<img src="{@RELATIVE_WCF_DIR}icon/boxLayout{@$action|ucfirst}L.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wcf.acp.box.layout.{@$action}{/lang}</h2>
		{if $boxLayoutID|isset}<p>{lang}{$boxLayout->title}{/lang}</p>{/if}
	</div>
</div>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
	<p class="success">{lang}wcf.acp.box.layout.{@$action}.success{/lang}</p>	
{/if}

<div class="contentHeader">
	<div class="largeButtons">
		<ul><li><a href="index.php?page=BoxLayoutList&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}wcf.acp.menu.link.content.box.layout.view{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/boxLayoutM.png" alt="" /> <span>{lang}wcf.acp.menu.link.content.box.layout.view{/lang}</span></a></li></ul>
	</div>
</div>

<form method="post" action="index.php?form=BoxLayout{@$action|ucfirst}">
	<div class="border content">
		<div class="container-1">
			<fieldset>
				<legend>{lang}wcf.acp.box.layout.data{/lang}</legend>
				
				<div class="formElement{if $errorField == 'title'} formError{/if}">
					<div class="formFieldLabel">
						<label for="title">{lang}wcf.acp.box.layout.title{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="title" name="title" value="{$title}" />
						{if $errorField == 'title'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
				</div>
				
				{if $additionalGeneralFields|isset}{@$additionalGeneralFields}{/if}
			</fieldset>
				
			{if $additionalFields|isset}{@$additionalFields}{/if}
		</div>
	</div>
		
	<div class="formSubmit">
		<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
		<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
 		{@SID_INPUT_TAG}
 		{if $boxLayoutID|isset}<input type="hidden" name="boxLayoutID" value="{@$boxLayoutID}" />{/if}
 	</div>
</form>

{include file='footer'}