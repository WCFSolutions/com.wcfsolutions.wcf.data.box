<?php
/**
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
$packageID = $this->installation->getPackageID();

// admin options
$sql = "UPDATE 	wcf".WCF_N."_group_option_value
	SET	optionValue = 1
	WHERE	groupID = 4
		AND optionID IN (
			SELECT	optionID
			FROM	wcf".WCF_N."_group_option
			WHERE	optionName LIKE 'admin.box.%'
				AND packageID IN (
					SELECT	dependency
					FROM	wcf".WCF_N."_package_dependency
					WHERE	packageID = ".$this->installation->getPackageID()."
				)
		)
		AND optionValue = '0'";
WCF::getDB()->sendQuery($sql);

// delete deprecated pips
$sql = "DELETE FROM	wcf".WCF_N."_package_installation_plugin
	WHERE		packageID = ".$this->installation->getPackageID()."
			AND pluginName IN ('BoxOptionsPackageInstallationPlugin', 'BoxTypePackageInstallationPlugin')";
WCF::getDB()->sendQuery($sql);

// delete deprecated files
$deprecatedFiles = array(
	'lib/acp/package/plugin/BoxOptionsPackageInstallationPlugin.class.php',
	'lib/acp/package/plugin/BoxTypePackageInstallationPlugin.class.php',
	'lib/acp/page/BoxLayoutPage.class.php',
	'lib/data/box/type/AbstractBoxType.class.php',
	'lib/data/box/type/BoxType.class.php.class.php',
	'lib/data/box/type/ContentBoxType.class.php',
	'lib/system/cache/CacheBuilderBoxOption.class.php',
	'lib/system/cache/CacheBuilderBoxTypes.class.php'
);

$sql = "DELETE FROM	wcf".WCF_N."_package_installation_file_log
	WHERE		filename IN ('".implode("','", array_map('escapeString', $deprecatedFiles))."')
			AND packageID = ".$packageID;
WCF::getDB()->sendQuery($sql);

foreach ($deprecatedFiles as $file) {
	@unlink(RELATIVE_WCF_DIR.$this->installation->getPackage()->getDir().$file);
}

// delete deprecated templates
$deprecatedTemplates = array(
	'contentBoxType.tpl'
);

$sql = "DELETE FROM	wcf".WCF_N."_template
	WHERE		templateName IN ('".implode("','", array_map('escapeString', $deprecatedTemplates))."')
			AND packageID = ".$packageID;
WCF::getDB()->sendQuery($sql);

foreach ($deprecatedTemplates as $template) {
	@unlink(RELATIVE_WCF_DIR.$this->installation->getPackage()->getDir().'templates/'.$template.'.tpl');
}

// delete deprecated acp templates
$deprecatedACPTemplates = array(
	'boxAddBoxTypeSelect.tpl',
	'boxLayout.tpl'
);

$sql = "DELETE FROM	wcf".WCF_N."_acp_template
	WHERE		templateName IN ('".implode("','", array_map('escapeString', $deprecatedACPTemplates))."')
			AND packageID = ".$packageID;
WCF::getDB()->sendQuery($sql);

foreach ($deprecatedACPTemplates as $template) {
	@unlink(RELATIVE_WCF_DIR.$this->installation->getPackage()->getDir().'acp/templates/'.$template.'.tpl');
}

// update content box
$sql = "UPDATE 	wcf".WCF_N."_box_tab_option
	SET	optionName = 'text'
	WHERE	boxTabType = 'content'
		AND optionName = 'content'
		AND packageID = ".$this->installation->getPackageID();
WCF::getDB()->sendQuery($sql);

// delete deprecated box tabs
require_once(WCF_DIR.'lib/data/box/BoxEditor.class.php');
$sql = "SELECT	boxID
	FROM	wcf".WCF_N."_box_tab
	WHERE	boxTabType = 'tinyMCE";
$result = WCF::getDB()->sendQuery($sql);
while ($row = WCF::getDB()->fetchArray($result)) {
	$box = new BoxEditor(null, $row);
	$box->delete();
}

// get languages
require_once(WCF_DIR.'lib/system/language/LanguageEditor.class.php');
$languageCodes = LanguageEditor::getAvailableLanguageCodes($packageID);
$languages = $languageItems = array();
foreach ($languageCodes as $languageID => $languageCode) {
	$languages[$languageID] = new LanguageEditor($languageID);
	$languages[$languageID] = new LanguageEditor($languageID);
	$languageItems[$languageID] = array();
}

// get old language items
$oldLanguageItems = array();
$sql = "SELECT	languageID, languageItem, languageUseCustomValue, languageItemValue, languageCustomItemValue
	FROM	wcf".WCF_N."_language_item
	WHERE	languageCategoryID = (
			SELECT	languageCategoryID
			FROM	wcf".WCF_N."_language_category
			WHERE	languageCategory = 'wcf.box'
		)
		AND languageItem LIKE 'wcf.box.box%'";
$result = WCF::getDB()->sendQuery($sql);
while ($row = WCF::getDB()->fetchArray($result)) {
	if (!isset($oldLanguageItems[$row['languageID']])) {
		$oldLanguageItems[$row['languageID']] = array();
	}
	$oldLanguageItems[$row['languageID']][$row['languageItem']] = ($row['languageUseCustomValue'] ? $row['languageCustomItemValue'] : $row['languageItemValue']);
}

// create empty box descriptions and box tab titles
$sql = "SELECT	boxID
	FROM	wcf".WCF_N."_box";
$result = WCF::getDB()->sendQuery($sql);
while ($row = WCF::getDB()->fetchArray($result)) {
	foreach ($languages as $languageID => $language) {
		if (!isset($languageItems[$languageID])) {
			$languageItems[$languageID] = array();
		}
		$languageItems[$languageID]['wcf.box.box'.$row['boxID'].'.description'] = '';
		$languageItems[$languageID]['wcf.box.tab.boxTab'.$row['boxID']] = (isset($oldLanguageItems[$languageID]['wcf.box.box'.$row['boxID']]) ? $oldLanguageItems[$languageID]['wcf.box.box'.$row['boxID']] : 'Boxtab #'.$row['boxID']);
	}
}

// update language items
foreach ($languages as $languageID => $language) {
	$language->updateItems($languageItems[$languageID], 0, $packageID);
}

// refresh style files
require_once(WCF_DIR.'lib/data/style/StyleEditor.class.php');
$sql = "SELECT	*
	FROM	wcf".WCF_N."_style";
$result = WCF::getDB()->sendQuery($sql);
while ($row = WCF::getDB()->fetchArray($result)) {
	$style = new StyleEditor(null, $row);
	$style->writeStyleFile();
}
?>