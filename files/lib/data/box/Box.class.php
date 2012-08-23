<?php
// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');
require_once(WCF_DIR.'lib/data/box/tab/BoxTab.class.php');

/**
 * Represents a box.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	data.box
 * @category	Community Framework
 */
class Box extends DatabaseObject {
	/**
	 * list of all boxes
	 *
	 * @var	array<Box>
	 */
	protected static $boxes = null;

	/**
	 * list of closed boxes
	 *
	 * @var	array
	 */
	public static $closedBoxes = null;

	/**
	 * list of available box types
	 *
	 * @var	array<BoxType>
	 */
	public static $availableBoxTypes = null;

	/**
	 * list of box tabs matched to boxes
	 *
	 * @var	array
	 */
	protected static $boxTabsToBoxes = null;

	/**
	 * Creates a new Box object.
	 *
	 * @param	integer		$boxID
	 * @param 	array<mixed>	$row
	 * @param	Box		$cacheObject
	 */
	public function __construct($boxID, $row = null, $cacheObject = null) {
		if ($boxID !== null) $cacheObject = self::getBox($boxID);
		if ($row != null) parent::__construct($row);
		if ($cacheObject != null) parent::__construct($cacheObject->data);
	}

	/**
	 * @see	Box::getTitle()
	 */
	public function __toString() {
		return $this->getTitle();
	}

	/**
	 * Returns the title of this box.
	 *
	 * @return	string
	 */
	public function getTitle() {
		return WCF::getLanguage()->get('wcf.box.'.$this->box);
	}

	/**
	 * Returns the formatted description of this box.
	 *
	 * @return	string
	 */
	public function getFormattedDescription() {
		return nl2br(StringUtil::encodeHTML(WCF::getLanguage()->get('wcf.box.'.$this->box.'.description')));
	}

	/**
	 * Returns true, if this box has box tabs.
	 *
	 * @return	boolean
	 */
	public function hasBoxTabs() {
		if (self::$boxTabsToBoxes === null) self::$boxTabsToBoxes = WCF::getCache()->get('boxTab-'.PACKAGE_ID, 'boxes');
		if (isset(self::$boxTabsToBoxes[$this->boxID])) {
			return true;
		}
		return false;
	}

	/**
	 * Returns a list of all box tabs of this box.
	 *
	 * @return 	array<BoxTab>
	 */
	public function getBoxTabs() {
		if (self::$boxTabsToBoxes === null) self::$boxTabsToBoxes = WCF::getCache()->get('boxTab-'.PACKAGE_ID, 'boxes');
		if (!isset(self::$boxTabsToBoxes[$this->boxID])) return array();

		$boxTabs = array();
		$boxTabIDArray = self::$boxTabsToBoxes[$this->boxID];
		foreach ($boxTabIDArray as $boxTabID) {
			$boxTab = new BoxTab($boxTabID);

			// check box tab type
			try {
				$boxTabType = $boxTab->getBoxTabType();
			}
			catch (SystemException $e) {
				continue;
			}
			$boxTabType->cache($boxTab);

			// continue if box tab is not accessible
			if (!$boxTabType->isAccessible($boxTab)) continue;

			$boxTabs[] = $boxTab;
		}
		return $boxTabs;
	}

	/**
	 * Returns the first box tab id.
	 *
	 * @return	integer
	 */
	public function getFirstBoxTabID() {
		if (self::$boxTabsToBoxes === null) self::$boxTabsToBoxes = WCF::getCache()->get('boxTab-'.PACKAGE_ID, 'boxes');
		if (!isset(self::$boxTabsToBoxes[$this->boxID])) return;

		$boxTabIDArray = self::$boxTabsToBoxes[$this->boxID];
		foreach ($boxTabIDArray as $boxTabID) {
			return $boxTabID;
		}
		return 0;
	}

	/**
	 * Returns a list of all boxes.
	 *
	 * @return 	array<Box>
	 */
	public static function getBoxes() {
		if (self::$boxes == null) {
			self::$boxes = WCF::getCache()->get('box-'.PACKAGE_ID);
		}

		return self::$boxes;
	}

	/**
	 * Returns the box options.
	 *
	 * @param	array		$ignoredBoxes
	 * @return	array
	 * */
	public static function getBoxOptions($ignoredBoxes = array()) {
		$boxes = self::getBoxes();
		$boxOptions = array();
		foreach ($boxes as $boxID => $box) {
			if (in_array($boxID, $ignoredBoxes)) continue;
			$boxOptions[$boxID] = $box->getTitle();
		}
		return $boxOptions;
	}

	/**
	 * Reads the closed boxes for the active user or guest.
	 */
	public static function readClosedBoxes() {
		if (self::$closedBoxes === null) {
			if (WCF::getUser()->userID) {
				self::$closedBoxes = array();
				$sql = "SELECT 	*
					FROM 	wcf".WCF_N."_box_closed_to_user
					WHERE 	userID = ".WCF::getUser()->userID;
				$result = WCF::getDB()->sendQuery($sql);
				while ($row = WCF::getDB()->fetchArray($result)) {
					if (!isset(self::$closedBoxes[$row['boxID']])) self::$closedBoxes[$row['boxID']] = array();
					self::$closedBoxes[$row['boxID']][$row['boxPosition']] = $row['isClosed'];
				}
			}
			else {
				self::$closedBoxes = WCF::getSession()->getVar('closedBoxes');
				if (self::$closedBoxes === null) self::$closedBoxes = array();
			}
		}
	}

	/**
	 * Returns true, if this box is closed by this user or guest.
	 *
	 * @return	integer
	 */
	public function isClosed($boxPosition) {
		self::readClosedBoxes();

		if (!isset(self::$closedBoxes[$this->boxID][$boxPosition])) return 0;
		return self::$closedBoxes[$this->boxID][$boxPosition];
	}

	/**
	 * Closes this box for this user or guest.
	 *
	 * @param	integer		$close		1 closes the box
	 *						-1 opens the box
	 */
	public function close($boxPosition, $close = 1) {
		self::readClosedBoxes();

		if (!$this->isClosable) {
			throw new IllegalLinkException();
		}

		if (WCF::getUser()->userID) {
			$sql = "REPLACE INTO	wcf".WCF_N."_box_closed_to_user
						(boxID, userID, boxPosition, isClosed)
				VALUES		(".$this->boxID.",
						".WCF::getUser()->userID.",
						'".escapeString($boxPosition)."',
						".$close.")";
			WCF::getDB()->sendQuery($sql);

			self::$closedBoxes[$this->boxID][$boxPosition] = $close;
		}
		else {
			self::$closedBoxes[$this->boxID][$boxPosition] = $close;
			WCF::getSession()->register('closedBoxes', self::$closedBoxes);
		}
	}

	/**
	 * Returns the box with the given box id from cache.
	 *
	 * @param 	integer		$boxID
	 * @return	Box
	 */
	public static function getBox($boxID) {
		if (self::$boxes == null) {
			self::$boxes = WCF::getCache()->get('box-'.PACKAGE_ID);
		}

		if (!isset(self::$boxes[$boxID])) {
			throw new IllegalLinkException();
		}

		return self::$boxes[$boxID];
	}

	/**
	 * @deprecated
	 */
	public static function resetBoxCacheByBoxType($boxType) {
		BoxTab::resetBoxTabCacheByBoxTabType($boxType);
	}
}
?>