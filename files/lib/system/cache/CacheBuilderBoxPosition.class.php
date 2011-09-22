<?php
// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Caches the box positions.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	system.cache
 * @category	Community Framework
 */
class CacheBuilderBoxPosition implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		list($cache, $packageID) = explode('-', $cacheResource['cache']); 
		$data = array();
		
		// get box layout ids
		$boxPositionIDArray = array();
		$sql = "SELECT		boxPositionID 
			FROM		wcf".WCF_N."_box_position box_position,
					wcf".WCF_N."_package_dependency package_dependency
			WHERE 		box_position.packageID = package_dependency.dependency
					AND package_dependency.packageID = ".$packageID."
			ORDER BY	package_dependency.priority";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$boxPositionIDArray[] = $row['boxPositionID'];
		}
		
		if (count($boxPositionIDArray) > 0) {
			require_once(WCF_DIR.'lib/data/box/layout/BoxLayout.class.php');
			$sql = "SELECT		box_position.*, package.packageDir
				FROM		wcf".WCF_N."_box_position box_position
				LEFT JOIN	wcf".WCF_N."_package package
				ON		(package.packageID = box_position.packageID)
				WHERE		box_position.boxPositionID IN (".implode(',', $boxPositionIDArray).")
				ORDER BY	box_position.boxPositionID ASC";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$data[$row['boxPosition']] = new BoxPosition(null, $row);
			}
		}
		
		return $data;
	}
}
?>