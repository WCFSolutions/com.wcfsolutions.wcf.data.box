<?php
// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Caches the boxes.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	system.cache
 * @category	Community Framework
 */
class CacheBuilderBox implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		list($cache, $packageID) = explode('-', $cacheResource['cache']);
		$data = array();

		// get box ids
		$boxIDArray = array();
		$sql = "SELECT		boxID
			FROM		wcf".WCF_N."_box box,
					wcf".WCF_N."_package_dependency package_dependency
			WHERE 		box.packageID = package_dependency.dependency
					AND package_dependency.packageID = ".$packageID."
			ORDER BY	package_dependency.priority";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$boxIDArray[] = $row['boxID'];
		}

		if (count($boxIDArray) > 0) {
			require_once(WCF_DIR.'lib/data/box/Box.class.php');
			$sql = "SELECT		box.*, package.packageDir
				FROM		wcf".WCF_N."_box box
				LEFT JOIN	wcf".WCF_N."_package package
				ON		(package.packageID = box.packageID)
				WHERE		box.boxID IN (".implode(',', $boxIDArray).")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$data[$row['boxID']] = new Box(null, $row);
			}
		}

		return $data;
	}
}
?>