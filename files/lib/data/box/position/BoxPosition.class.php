<?php
// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');

/**
 * Represents a box position.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	data.box.position
 * @category	Community Framework
 */
class BoxPosition extends DatabaseObject {
	/**
	 * list of box positions
	 *
	 * @var	array<BoxPosition>
	 */
	protected static $boxPositions = null;

	/**
	 * Creates a new BoxPosition object.
	 *
	 * @param	integer		$boxPositionID
	 * @param 	array<mixed>	$row
	 * @param	BoxPosition	$cacheObject
	 */
	public function __construct($boxPositionID, $row = null, $cacheObject = null) {
		if ($boxPositionID !== null) $cacheObject = self::getBoxPosition($boxPositionID);
		if ($row != null) parent::__construct($row);
		if ($cacheObject != null) parent::__construct($cacheObject->data);
	}

	/**
	 * @see	BoxPosition::getTitle()
	 */
	public function __toString() {
		return $this->getTitle();
	}

	/**
	 * Returns the title of this box position.
	 *
	 * @return	string
	 */
	public function getTitle() {
		return WCF::getLanguage()->get('wcf.box.position.'.$this->boxPosition);
	}

	/**
	 * Returns a box position select list.
	 *
	 * @todo	use BoxPosition::getBoxPositions() instead
	 * @return 	array
	 */
	public static function getBoxPositionOptions() {
		$boxPositionOptions = array();
		$boxPositions = self::getBoxPositions();
		foreach ($boxPositions as $boxPosition) {
			$boxPositionOptions[$boxPosition->boxPositionID] = WCF::getLanguage()->get('wcf.box.position.'.$boxPosition->boxPosition);
		}
		return $boxPositionOptions;
	}

	/**
	 * Returns the box position with the given box position id from cache.
	 *
	 * @param 	integer		$boxPositionID
	 * @return	BoxPosition
	 */
	public static function getBoxPosition($boxPositionID) {
		if (self::$boxPositions == null) {
			self::$boxPositions = WCF::getCache()->get('boxPosition-'.PACKAGE_ID);
		}

		foreach (self::$boxPositions as $boxPosition) {
			if ($boxPosition->boxPositionID == $boxPositionID) return $boxPosition;
		}

		throw new IllegalLinkException();
	}

	/**
	 * Returns a list of all box positions.
	 *
	 * @return 	array
	 */
	public static function getBoxPositions() {
		if (self::$boxPositions == null) {
			self::$boxPositions = WCF::getCache()->get('boxPosition-'.PACKAGE_ID);
		}

		return self::$boxPositions;
	}
}
?>