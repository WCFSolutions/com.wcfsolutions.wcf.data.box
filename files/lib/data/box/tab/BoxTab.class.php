<?php
// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');

/**
 * Represents a box tab.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	data.box.tab
 * @category	Community Framework
 */
class BoxTab extends DatabaseObject {
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
	public static $availableBoxTabTypes = null;
	
	/**
	 * list of box tabs
	 * 
	 * @var	array<BoxTab>
	 */
	protected static $boxTabs = null;
	
	/**
	 * list of box tabs matched to types.
	 * 
	 * @var	array
	 */
	protected static $boxTabToTypes = null;
	
	/**
	 * list of box tab options
	 * 
	 * @var	array
	 */
	protected $boxTabOptions = null;
	
	/**
	 * Creates a new BoxTab object.
	 * 
	 * @param	integer		$boxTabID
	 * @param 	array<mixed>	$row
	 * @param	BoxTab		$cacheObject
	 */
	public function __construct($boxTabID, $row = null, $cacheObject = null) {
		if ($boxTabID !== null) $cacheObject = self::getBoxTab($boxTabID);
		if ($row != null) parent::__construct($row);
		if ($cacheObject != null) parent::__construct($cacheObject->data);
	}
	
	/**
	 * Returns the title of this box tab.
	 * 
	 * @return	string
	 */
	public function getTitle() {
		return WCF::getLanguage()->get('wcf.box.tab.'.$this->boxTab);
	}
	
	/**
	 * Returns the box tab type of this box tab.
	 * 
	 * @return	BoxType
	 */
	public function getBoxTabType() {
		return self::getBoxTabTypeObject($this->boxTabType);
	}
	
	/**
	 * Returns the value of the box tab option with the given name.
	 * 
	 * @param	string		$name
	 * @return	mixed
	 */
	public function getBoxTabOption($name) {
		if ($this->boxTabOptions === null) {
			$options = WCF::getCache()->get('boxTab-'.PACKAGE_ID, 'options');
			if (isset($options[$this->boxTabID])) {
				$this->boxTabOptions = $options[$this->boxTabID];
			}
		}
		
		if (isset($this->boxTabOptions[$name])) {
			return $this->boxTabOptions[$name];
		}
		
		return null;
	}
	
	/**
	 * @see DatabaseObject::__get()
	 */
	public function __get($name) {
		$value = parent::__get($name);
		if ($value === null) $value = $this->getBoxTabOption($name);
		return $value;
	}
	
	/**
	 * Returns the box with the given box id from cache.
	 * 
	 * @param 	integer		$boxTabID
	 * @return	Box
	 */
	public static function getBoxTab($boxTabID) {
		if (self::$boxTabs == null) {
			self::$boxTabs = WCF::getCache()->get('boxTab-'.PACKAGE_ID, 'tabs');
		}
		
		if (!isset(self::$boxTabs[$boxTabID])) {
			throw new IllegalLinkException();
		}
		
		return self::$boxTabs[$boxTabID];
	}
	
	/**
	 * Returns the object of a box tab type.
	 * 
	 * @param	string		$boxTabType
	 * @return	BoxType
	 */
	public static function getBoxTabTypeObject($boxTabType) {
		$types = self::getAvailableBoxTabTypes();
		if (!isset($types[$boxTabType])) {
			throw new SystemException("Unknown box tab type '".$boxTabType."'", 11000);
		}	
		return $types[$boxTabType];
	}
	
	/**
	 * Resets the cache of the box tabs with the given box tab type.
	 * 
	 * @param	string		$boxTabType
	 */
	public static function resetBoxTabCacheByBoxTabType($boxTabType) {
		$boxTabs = self::getBoxTabsByBoxTabType($boxTabType);
		foreach ($boxTabs as $boxTab) {
			$boxTabType = $boxTab->getBoxTabType();
			$boxTabType->cache($boxTab);
			$boxTabType->resetCache($boxTab);
		}
	}
	
	/**
	 * Returns the box tabs with the given box tab type.
	 * 
	 * @param	string		$boxTabType
	 * @return	array<BoxTab>
	 */
	public static function getBoxTabsByBoxTabType($boxTabType) {
		$type = self::getBoxTabTypeObject($boxTabType);
		if (self::$boxTabToTypes === null) {
			self::$boxTabToTypes = WCF::getCache()->get('boxTab-'.PACKAGE_ID, 'types');
		}
		
		// get box tabs
		if (!isset(self::$boxTabToTypes[$boxTabType])) return array();
		$boxTabs = array();
		foreach (self::$boxTabToTypes[$boxTabType] as $boxTabID) {
			$boxTabs[] = new BoxTab($boxTabID);
		}
		return $boxTabs;
	}
	
	/**
	 * Returns a list of available box types.
	 * 
	 * @return	array<BoxType>
	 */
	public static function getAvailableBoxTabTypes() {
		if (self::$availableBoxTabTypes === null) {
			WCF::getCache()->addResource('boxTabTypes-'.PACKAGE_ID, WCF_DIR.'cache/cache.boxTabTypes-'.PACKAGE_ID.'.php', WCF_DIR.'lib/system/cache/CacheBuilderBoxTabTypes.class.php');
			$types = WCF::getCache()->get('boxTabTypes-'.PACKAGE_ID);
			foreach ($types as $type) {
				// get path to class file
				if (empty($type['packageDir'])) {
					$path = WCF_DIR;
				}
				else {						
					$path = FileUtil::getRealPath(WCF_DIR.$type['packageDir']);
				}
				$path .= $type['classFile'];
				
				// include class file
				if (!class_exists($type['className'])) {
					if (!file_exists($path)) {
						throw new SystemException("Unable to find class file '".$path."'", 11000);
					}
					require_once($path);
				}
				
				// instance object
				if (!class_exists($type['className'])) {
					throw new SystemException("Unable to find class '".$type['className']."'", 11001);
				}
				self::$availableBoxTabTypes[$type['boxTabType']] = new $type['className'];
			}
		}
		return self::$availableBoxTabTypes;
	}
}
?>