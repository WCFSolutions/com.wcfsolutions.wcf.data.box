<?php
/**
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
$packageID = $this->installation->getPackageID();

// delete deprecated templates
$deprecatedTemplates = array(
	'contentBoxType'
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
	'boxAddBoxTypeSelect',
	'boxLayout'
);

$sql = "DELETE FROM	wcf".WCF_N."_acp_template
	WHERE		templateName IN ('".implode("','", array_map('escapeString', $deprecatedACPTemplates))."')
			AND packageID = ".$packageID;
WCF::getDB()->sendQuery($sql);

foreach ($deprecatedACPTemplates as $template) {
	@unlink(RELATIVE_WCF_DIR.$this->installation->getPackage()->getDir().'acp/templates/'.$template.'.tpl');
}
?>