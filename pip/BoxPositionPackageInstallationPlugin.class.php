<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/package/plugin/AbstractXMLPackageInstallationPlugin.class.php');

/**
 * This PIP installs, updates or deletes box positions.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.package.plugin
 * @category	Community Framework
 */
class BoxPositionPackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin {
	public $tagName = 'boxposition';
	public $tableName = 'box_position';
	
	/** 
	 * @see PackageInstallationPlugin::install()
	 */
	public function install() {
		parent::install();
		
		if (!$xml = $this->getXML()) {
			return;
		}
		
		// Create an array with the data blocks (import or delete) from the xml file.
		$boxPositionXML = $xml->getElementTree('data');
		
		// Loop through the array and install or uninstall items.
		foreach ($boxPositionXML['children'] as $key => $block) {
			if (count($block['children'])) {
				// Handle the import instructions
				if ($block['name'] == 'import') {
					// Loop through items and create or update them.
					foreach ($block['children'] as $boxPosition) {
						// Extract item properties.
						foreach ($boxPosition['children'] as $child) {
							if (!isset($child['cdata'])) continue;
							$boxPosition[$child['name']] = $child['cdata'];
						}
						
						// default values
						$name = '';
						
						// get values
						if (isset($boxPosition['name'])) $name = $boxPosition['name'];
						
						// insert items
						$sql = "INSERT IGNORE INTO		wcf".WCF_N."_box_position
											(packageID, boxPosition)
							VALUES				(".$this->installation->getPackageID().",
											'".escapeString($name)."')";
						WCF::getDB()->sendQuery($sql);
					}
				}
				// Handle the delete instructions.
				else if ($block['name'] == 'delete' && $this->installation->getAction() == 'update') {
					// Loop through items and delete them.
					$nameArray = array();
					foreach ($block['children'] as $boxPosition) {
						// Extract item properties.
						foreach ($boxPosition['children'] as $child) {
							if (!isset($child['cdata'])) continue;
							$boxPosition[$child['name']] = $child['cdata'];
						}
						
						if (empty($boxPosition['name'])) {
							throw new SystemException("Required 'name' attribute for box position is missing", 13023); 
						}
						$nameArray[] = $boxPosition['name'];
					}
					if (count($nameArray)) {
						$sql = "DELETE FROM	wcf".WCF_N."_box_position
							WHERE		packageID = ".$this->installation->getPackageID()."
									AND boxPosition IN ('".implode("','", array_map('escapeString', $nameArray))."')";
						WCF::getDB()->sendQuery($sql);
					}
				}
			}
		}
	}
	
	/**
	 * @see	PackageInstallationPlugin::uninstall()
	 */
	public function uninstall() {
		// call uninstall event
		EventHandler::fireAction($this, 'uninstall');
		
		// get box positions
		$boxPositionIDs = $boxPositions = array();
		$sql = "SELECT	boxPositionID, boxPosition
			FROM	wcf".WCF_N."_".$this->tableName."
			WHERE	packageID = ".$this->installation->getPackageID();
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$boxPositionIDs[] = $row['boxPositionID'];
			$boxPositions[] = $row['boxPosition'];
		}
		
		if (count($boxPositions)) {			
			// delete box to layouts
			$sql = "DELETE FROM	wcf".WCF_N."_box_to_layout
				WHERE		boxPositionID IN (".implode(',', $boxPositionIDs).")";
			WCF::getDB()->sendQuery($sql);
			
			// delete box closed to user
			// @todo: use boxPositionID here, other packages can install box positions with the same name!
			$sql = "DELETE FROM	wcf".WCF_N."_box_closed_to_user
				WHERE		boxPosition IN ('".implode("','", array_map('escapeString', $boxPositions))."')";
			WCF::getDB()->sendQuery($sql);
			
			// delete box positions
			$sql = "DELETE FROM	wcf".WCF_N."_".$this->tableName."
				WHERE		packageID = ".$this->installation->getPackageID();
			WCF::getDB()->sendQuery($sql);
		}
	}
}
?>