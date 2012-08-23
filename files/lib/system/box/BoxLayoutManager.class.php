<?php
// wcf imports
require_once(WCF_DIR.'lib/data/box/layout/BoxLayout.class.php');

/**
 * Manages the active box layouts.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	system.box
 * @category	Community Framework
 */
class BoxLayoutManager {
	/**
	 * active box layout object
	 *
	 * @var	BoxLayout
	 */
	protected static $boxLayout = null;

	/**
	 * Changes the active box layout.
	 *
	 * @param	integer		$boxLayoutID
	 */
	public static final function changeBoxLayout($boxLayoutID = 0) {
		if (self::$boxLayout != null && self::$boxLayout->boxLayoutID == $boxLayoutID) return;

		// get cache
		$boxLayouts = WCF::getCache()->get('boxLayout-'.PACKAGE_ID, 'layouts');

		// fallback to default box layout
		if (!isset($boxLayouts[$boxLayoutID])) {
			// get default box layout id
			$defaultBoxLayoutID = WCF::getCache()->get('boxLayout-'.PACKAGE_ID, 'default');
			if ($defaultBoxLayoutID != 0) {
				$boxLayoutID = $defaultBoxLayoutID;
			}

			// no default box layout
			if (!isset($boxLayouts[$boxLayoutID])) {
				throw new SystemException('no default box layout defined', 100000);
			}
		}

		// set box layout
		self::setBoxLayout($boxLayouts[$boxLayoutID]);
	}

	/**
	 * Sets the box layout directly.
	 *
	 * @param	BoxLayout	$boxLayout
	 */
	public static function setBoxLayout(BoxLayout $boxLayout) {
		self::$boxLayout = $boxLayout;
	}

	/**
	 * Returns the active box layout.
	 *
	 * @return	BoxLayout
	 */
	public static function getBoxLayout() {
		return self::$boxLayout;
	}
}
?>