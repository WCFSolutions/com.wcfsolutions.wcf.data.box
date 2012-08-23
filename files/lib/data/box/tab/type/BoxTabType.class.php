<?php
/**
 * All box tab type classes should implement this interface.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	data.box.tab.type
 * @category	Community Framework
 */
interface BoxTabType {
	/**
	 * Caches all necessary box tab data to save performance.
	 *
	 * @param	BoxTab		$boxTab
	 */
	public function cache(BoxTab $boxTab);

	/**
	 * Returns the data of the given box tab object.
	 *
	 * @param	BoxTab		$boxTab
	 * @return	array
	 */
	public function getData(BoxTab $boxTab);

	/**
	 * Resets the cache of the given box tab object.
	 *
	 * @param	BoxTab		$boxTab
	 */
	public function resetCache(BoxTab $boxTab);

	/**
	 * Returns true, if the given box tab is accessible.
	 *
	 * @param	BoxTab		$boxTab
	 * @return	boolean
	 */
	public function isAccessible(BoxTab $boxTab);

	/**
	 * Returns the name of the template.
	 *
	 * @return	string
	 */
	public function getTemplateName();
}
?>