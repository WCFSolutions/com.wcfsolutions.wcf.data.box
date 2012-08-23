<?php
// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Caches the box tabs.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	system.cache
 * @category	Community Framework
 */
class CacheBuilderBoxTab implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		list($cache, $packageID) = explode('-', $cacheResource['cache']);
		$data = array('tabs' => array(), 'boxes' => array(), 'types' => array(), 'options' => array());

		// get box tab ids
		$boxTabIDArray = array();
		$sql = "SELECT		boxTabID
			FROM		wcf".WCF_N."_box_tab box_tab,
					wcf".WCF_N."_package_dependency package_dependency
			WHERE 		box_tab.packageID = package_dependency.dependency
					AND package_dependency.packageID = ".$packageID."
			ORDER BY	package_dependency.priority";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$boxTabIDArray[] = $row['boxTabID'];
		}

		if (count($boxTabIDArray) > 0) {
			require_once(WCF_DIR.'lib/data/box/tab/BoxTab.class.php');
			$sql = "SELECT		box_tab.*, package.packageDir
				FROM		wcf".WCF_N."_box_tab box_tab
				LEFT JOIN	wcf".WCF_N."_package package
				ON		(package.packageID = box_tab.packageID)
				WHERE		box_tab.boxTabID IN (".implode(',', $boxTabIDArray).")
				ORDER BY	box_tab.boxID, box_tab.showOrder";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$data['tabs'][$row['boxTabID']] = new BoxTab(null, $row);
				if (!isset($data['boxes'][$row['boxID']])) {
					$data['boxes'][$row['boxID']] = array();
				}
				$data['boxes'][$row['boxID']][] = $row['boxTabID'];
				if (!isset($data['types'][$row['boxTabType']])) {
					$data['types'][$row['boxTabType']] = array();
				}
				$data['types'][$row['boxTabType']][] = $row['boxTabID'];
			}
		}

		// get all options and filter options with low priority
		$boxTabOptionIDs = array();
		$sql = "SELECT		optionID
			FROM		wcf".WCF_N."_box_tab_option option_table,
					wcf".WCF_N."_package_dependency package_dependency
			WHERE 		option_table.packageID = package_dependency.dependency
					AND package_dependency.packageID = ".PACKAGE_ID."
			ORDER BY	package_dependency.priority";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$boxTabOptionIDs[] = $row['optionID'];
		}

		if (count($boxTabOptionIDs)) {
			$sql = "SELECT		box_tab_option.optionName, option_value.boxTabID, option_value.optionValue
				FROM		wcf".WCF_N."_box_tab_option_value option_value
				LEFT JOIN	wcf".WCF_N."_box_tab_option box_tab_option
				ON		(box_tab_option.optionID = option_value.optionID)
				WHERE		option_value.optionID IN (".implode(',', $boxTabOptionIDs).")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if (!isset($data['options'][$row['boxTabID']])) {
					$data['options'][$row['boxTabID']] = array();
				}
				$data['options'][$row['boxTabID']][$row['optionName']] = $row['optionValue'];
			}
		}

		return $data;
	}
}
?>