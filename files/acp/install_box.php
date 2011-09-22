<?php
/**
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
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