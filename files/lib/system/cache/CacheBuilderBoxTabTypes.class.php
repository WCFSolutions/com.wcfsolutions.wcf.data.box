<?php
// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Caches the box tab types.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	system.cache
 * @category	Community Framework
 */
class CacheBuilderBoxTabTypes implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		list($cache, $packageID) = explode('-', $cacheResource['cache']);
		$data = array();

		// get box tab type ids
		$boxTabTypeIDArray = array();
		$sql = "SELECT		boxTabTypeID
			FROM		wcf".WCF_N."_box_tab_type box_tab_type,
					wcf".WCF_N."_package_dependency package_dependency
			WHERE 		box_tab_type.packageID = package_dependency.dependency
					AND package_dependency.packageID = ".$packageID."
			ORDER BY	package_dependency.priority";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$boxTabTypeIDArray[] = $row['boxTabTypeID'];
		}

		if (count($boxTabTypeIDArray) > 0) {
			$sql = "SELECT		box_tab_type.*, package.packageDir
				FROM		wcf".WCF_N."_box_tab_type box_tab_type
				LEFT JOIN	wcf".WCF_N."_package package
				ON		(package.packageID = box_tab_type.packageID)
				WHERE		box_tab_type.boxTabTypeID IN (".implode(',', $boxTabTypeIDArray).")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$row['className'] = StringUtil::getClassName($row['classFile']);
				$data[] = $row;
			}
		}

		return $data;
	}
}
?>