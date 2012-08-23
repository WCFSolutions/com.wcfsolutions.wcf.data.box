<?php
// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');
require_once(WCF_DIR.'lib/data/box/Box.class.php');
require_once(WCF_DIR.'lib/data/box/position/BoxPosition.class.php');

/**
 * Represents a box layout.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	data.box.layout
 * @category	Community Framework
 */
class BoxLayout extends DatabaseObject {
	/**
	 * list of registred box positions
	 *
	 * @var	array
	 */
	public static $registredPositions = array();

	/**
	 * list of layout boxes
	 *
	 * @var	array
	 */
	public $layoutBoxes = array();

	/**
	 * list of cached boxes
	 *
	 * @var	array<Box>
	 */
	public $cachedBoxes = null;

	/**
	 * list of box layouts
	 *
	 * @var	array<BoxLayout>
	 */
	protected static $boxLayouts = null;

	/**
	 * list of boxes
	 *
	 * @var	array<Box>
	 */
	protected static $boxes = null;

	/**
	 * list of boxes matched to layouts
	 *
	 * @var	array
	 */
	protected static $boxToLayouts = null;

	/**
	 * list of box positions
	 *
	 * @var	array
	 */
	protected static $positions = null;

	/**
	 * Creates a new BoxLayout object.
	 *
	 * @param	integer		$boxLayoutID
	 * @param 	array<mixed>	$row
	 * @param	BoxLayout	$cacheObject
	 */
	public function __construct($boxLayoutID, $row = null, $cacheObject = null) {
		if ($boxLayoutID !== null) $cacheObject = self::getBoxLayout($boxLayoutID);
		if ($row != null) parent::__construct($row);
		if ($cacheObject != null) parent::__construct($cacheObject->data);
	}

	/**
	 * Returns the title of this box layout.
	 *
	 * @return	string
	 */
	public function __toString() {
		return $this->title;
	}

	/**
	 * Registers the given positions.
	 *
	 * @param	array		$positions
	 */
	public static function registerPositions($positions) {
		self::$registredPositions = array_merge(self::$registredPositions, $positions);
	}

	/**
	 * Returns the boxes of the given position.
	 *
	 * @param	string		$positionName
	 * @return	array<Box>
	 */
	public function getBoxesByPosition($positionName) {
		if ($this->cachedBoxes === null) {
			$this->cacheBoxes();
		}

		if (!isset($this->layoutBoxes[$positionName])) {
			// get position
			if (!isset(self::$positions[$positionName])) return array();
			$position = self::$positions[$positionName];

			// get boxes
			$boxes = array();
			if (isset(self::$boxToLayouts[$this->boxLayoutID][$position->boxPositionID])) {
				$boxIDArray = self::$boxToLayouts[$this->boxLayoutID][$position->boxPositionID];
				foreach ($boxIDArray as $boxID => $showOrder) {
					if (isset($this->cachedBoxes[$boxID])) {
						$boxes[$boxID] = $this->cachedBoxes[$boxID];
					}
				}
			}

			// save boxes
			$this->layoutBoxes[$positionName] = $boxes;
		}

		return $this->layoutBoxes[$positionName];
	}

	/**
	 * Caches the registred boxes.
	 */
	public function cacheBoxes() {
		if (self::$boxes === null) self::$boxes = WCF::getCache()->get('box-'.PACKAGE_ID);
		if (self::$boxToLayouts === null) self::$boxToLayouts = WCF::getCache()->get('boxLayout-'.PACKAGE_ID, 'boxes');
		if (self::$positions === null) self::$positions = WCF::getCache()->get('boxPosition-'.PACKAGE_ID);

		// get required boxes
		$boxIDArray = array();
		foreach (self::$registredPositions as $positionName) {
			if (!isset(self::$positions[$positionName])) continue;
			$position = self::$positions[$positionName];

			// add boxes
			if (isset(self::$boxToLayouts[$this->boxLayoutID][$position->boxPositionID])) {
				$boxIDArray = array_merge($boxIDArray, array_keys(self::$boxToLayouts[$this->boxLayoutID][$position->boxPositionID]));
			}
		}

		// cache boxes
		$this->cachedBoxes = array();
		foreach ($boxIDArray as $boxID) {
			$box = self::$boxes[$boxID];

			// cache box tabs
			$boxTabs = $box->getBoxTabs();
			if (!count($boxTabs)) continue;

			// save box
			$this->cachedBoxes[$boxID] = $box;
		}
	}

	/**
	 * Returns the box layout with the given box layout id from cache.
	 *
	 * @param 	integer		$boxLayoutID
	 * @return	BoxLayout
	 */
	public static function getBoxLayout($boxLayoutID) {
		if (self::$boxLayouts == null) {
			self::$boxLayouts = WCF::getCache()->get('boxLayout-'.PACKAGE_ID, 'layouts');
		}

		if (!isset(self::$boxLayouts[$boxLayoutID])) {
			throw new IllegalLinkException();
		}

		return self::$boxLayouts[$boxLayoutID];
	}

	/**
	 * Returns a list of all box layouts.
	 *
	 * @return 	array
	 */
	public static function getBoxLayouts() {
		if (self::$boxLayouts == null) {
			self::$boxLayouts = WCF::getCache()->get('boxLayout-'.PACKAGE_ID, 'layouts');
		}

		return self::$boxLayouts;
	}
}
?>