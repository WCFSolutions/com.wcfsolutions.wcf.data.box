<?php
// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Caches the box layouts.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	system.cache
 * @category	Community Framework
 */
class CacheBuilderBoxLayout implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		list($cache, $packageID) = explode('-', $cacheResource['cache']);
		$data = array('layouts' => array(), 'boxes' => array(), 'default' => 0);

		// get box layout ids
		$boxLayoutIDArray = array();
		$sql = "SELECT		boxLayoutID
			FROM		wcf".WCF_N."_box_layout box_layout,
					wcf".WCF_N."_package_dependency package_dependency
			WHERE 		box_layout.packageID = package_dependency.dependency
					AND package_dependency.packageID = ".$packageID."
			ORDER BY	package_dependency.priority";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$boxLayoutIDArray[] = $row['boxLayoutID'];
		}

		if (count($boxLayoutIDArray) > 0) {
			require_once(WCF_DIR.'lib/data/box/layout/BoxLayout.class.php');
			$sql = "SELECT		*
				FROM		wcf".WCF_N."_box_layout
				WHERE		boxLayoutID IN (".implode(',', $boxLayoutIDArray).")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if ($row['isDefault'] || $data['default'] == 0) $data['default'] = $row['boxLayoutID'];
				$data['layouts'][$row['boxLayoutID']] = new BoxLayout(null, $row);
			}

			// get boxes to layout
			$sql = "SELECT		*
				FROM		wcf".WCF_N."_box_to_layout
				WHERE		boxLayoutID IN (".implode(',', $boxLayoutIDArray).")
				ORDER BY	showOrder";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if (!isset($data['boxes'][$row['boxLayoutID']][$row['boxPositionID']])) {
					$data['boxes'][$row['boxLayoutID']][$row['boxPositionID']] = array();
				}
				$data['boxes'][$row['boxLayoutID']][$row['boxPositionID']][$row['boxID']] = $row['showOrder'];
			}
		}

		return $data;
	}
}
?>