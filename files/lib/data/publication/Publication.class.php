<?php
// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');

/**
 * Represents a publication.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	data.publication
 * @category	Infinite Portal
 */
class Publication {
	/**
	 * list of available publication types
	 *
	 * @var	array<PublicationType>
	 */
	public static $availablePublicationTypes = null;

	/**
	 * Inits the given publication type.
	 *
	 * @param	string		$publicationType
	 */
	public static function initPublicationType($publicationType) {
		$publicationType = self::getPublicationTypeObject($publicationType);
		if ($publicationType->getBoxLayoutID() != 0) {
			// change box layout
			BoxLayoutManager::changeBoxLayout($publicationType->getBoxLayoutID());
		}
	}

	/**
	 * Returns a list of available publication types.
	 *
	 * @return	array<PublicationType>
	 */
	public static function getAvailablePublicationTypes() {
		if (self::$availablePublicationTypes === null) {
			WCF::getCache()->addResource('publicationTypes', WSIP_DIR.'cache/cache.publicationTypes.php', WSIP_DIR.'lib/system/cache/CacheBuilderPublicationTypes.class.php');
			$types = WCF::getCache()->get('publicationTypes');
			foreach ($types as $type) {
				// get path to class file
				$path = WSIP_DIR.$type['classFile'];

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
				self::$availablePublicationTypes[$type['publicationType']] = new $type['className'];
			}
		}

		return self::$availablePublicationTypes;
	}

	/**
	 * Returns the object of a publication type.
	 *
	 * @param	string		$publicationType
	 * @return	PublicationType
	 */
	public static function getPublicationTypeObject($publicationType) {
		$types = self::getAvailablePublicationTypes();
		if (!isset($types[$publicationType])) {
			throw new SystemException("Unknown publication type '".$publicationType."'", 11000);
		}
		return $types[$publicationType];
	}
}
?>