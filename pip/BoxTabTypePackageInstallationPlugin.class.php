<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/package/plugin/AbstractXMLPackageInstallationPlugin.class.php');

/**
 * This PIP installs, updates or deletes box tab types.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.package.plugin
 * @category	Community Framework
 */
class BoxTabTypePackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin {
	public $tagName = 'boxtabtype';
	public $tableName = 'box_tab_type';

	/**
	 * @see PackageInstallationPlugin::install()
	 */
	public function install() {
		parent::install();

		if (!$xml = $this->getXML()) {
			return;
		}

		// Create an array with the data blocks (import or delete) from the xml file.
		$boxTabTypeXML = $xml->getElementTree('data');

		// Loop through the array and install or uninstall items.
		foreach ($boxTabTypeXML['children'] as $key => $block) {
			if (count($block['children'])) {
				// Handle the import instructions
				if ($block['name'] == 'import') {
					// Loop through items and create or update them.
					foreach ($block['children'] as $boxTabType) {
						// Extract item properties.
						foreach ($boxTabType['children'] as $child) {
							if (!isset($child['cdata'])) continue;
							$boxTabType[$child['name']] = $child['cdata'];
						}

						// default values
						$name = $classFile = '';

						// get values
						if (isset($boxTabType['name'])) $name = $boxTabType['name'];
						if (isset($boxTabType['classfile'])) $classFile = $boxTabType['classfile'];

						// insert items
						$sql = "INSERT INTO			wcf".WCF_N."_box_tab_type
											(packageID, boxTabType, classFile)
							VALUES				(".$this->installation->getPackageID().",
											'".escapeString($name)."',
											'".escapeString($classFile)."')
							ON DUPLICATE KEY UPDATE 	classFile = VALUES(classFile)";
						WCF::getDB()->sendQuery($sql);
					}
				}
				// Handle the delete instructions.
				else if ($block['name'] == 'delete' && $this->installation->getAction() == 'update') {
					// Loop through items and delete them.
					$nameArray = array();
					foreach ($block['children'] as $boxTabType) {
						// Extract item properties.
						foreach ($boxTabType['children'] as $child) {
							if (!isset($child['cdata'])) continue;
							$boxTabType[$child['name']] = $child['cdata'];
						}

						if (empty($boxTabType['name'])) {
							throw new SystemException("Required 'name' attribute for box tab type is missing", 13023);
						}
						$nameArray[] = $boxTabType['name'];
					}
					if (count($nameArray)) {
						$sql = "DELETE FROM	wcf".WCF_N."_box_tab_type
							WHERE		packageID = ".$this->installation->getPackageID()."
									AND boxTabType IN ('".implode("','", array_map('escapeString', $nameArray))."')";
						WCF::getDB()->sendQuery($sql);
					}
				}
			}
		}
	}
}
?>