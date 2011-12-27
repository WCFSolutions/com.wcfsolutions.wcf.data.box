<?php
/**
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
// delete deprecated box tabs
require_once(WCF_DIR.'lib/data/box/tab/BoxTabEditor.class.php');
$sql = "SELECT	boxTabID
	FROM	wcf".WCF_N."_box_tab
	WHERE	boxTabType = 'tinyMCE'";
$result = WCF::getDB()->sendQuery($sql);
while ($row = WCF::getDB()->fetchArray($result)) {
	$boxTab = new BoxTabEditor(null, $row);
	$boxTab->delete();
}
?>