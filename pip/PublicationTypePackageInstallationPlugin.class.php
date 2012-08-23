<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/package/plugin/AbstractXMLPackageInstallationPlugin.class.php');

/**
 * This PIP installs, updates or deletes publication types.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsip
 * @subpackage	acp.package.plugin
 * @category 	Infinite Portal
 */
class PublicationTypePackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin {
	public $tagName = 'publicationtype';
	public $tableName = 'publication_type';

	/**
	 * @see PackageInstallationPlugin::install()
	 */
	public function install() {
		parent::install();

		if (!$xml = $this->getXML()) {
			return;
		}

		// get instance no
		$instanceNo = WCF_N.'_'.$this->getApplicationPackage()->getInstanceNo();

		// Create an array with the data blocks (import or delete) from the xml file.
		$publicationTypeXML = $xml->getElementTree('data');

		// Loop through the array and install or uninstall items.
		foreach ($publicationTypeXML['children'] as $key => $block) {
			if (count($block['children'])) {
				// Handle the import instructions
				if ($block['name'] == 'import') {
					// Loop through items and create or update them.
					foreach ($block['children'] as $publicationType) {
						// Extract item properties.
						foreach ($publicationType['children'] as $child) {
							if (!isset($child['cdata'])) continue;
							$publicationType[$child['name']] = $child['cdata'];
						}

						// default values
						$name = $classFile = '';

						// get values
						if (isset($publicationType['name'])) $name = $publicationType['name'];
						if (isset($publicationType['classfile'])) $classFile = $publicationType['classfile'];

						// insert items
						$sql = "INSERT INTO			wsip".$instanceNo."_publication_type
											(packageID, publicationType, classFile)
							VALUES				(".$this->installation->getPackageID().",
											'".escapeString($name)."',
											'".escapeString($classFile)."')
							ON DUPLICATE KEY UPDATE 	classFile = VALUES(classFile)";
						WCF::getDB()->sendQuery($sql);
					}
				}
				// Handle the delete instructions.
				else if ($block['name'] == 'delete' && $this->installation->getAction() == 'update') {
					// Loop through items and delete them.
					$nameArray = array();
					foreach ($block['children'] as $publicationType) {
						// Extract item properties.
						foreach ($publicationType['children'] as $child) {
							if (!isset($child['cdata'])) continue;
							$publicationType[$child['name']] = $child['cdata'];
						}

						if (empty($publicationType['name'])) {
							throw new SystemException("Required 'name' attribute for publication type is missing", 13023);
						}
						$nameArray[] = $publicationType['name'];
					}
					if (count($nameArray)) {
						$sql = "DELETE FROM	wsip".$instanceNo."_publication_type
							WHERE		packageID = ".$this->installation->getPackageID()."
									AND publicationType IN ('".implode("','", array_map('escapeString', $nameArray))."')";
						WCF::getDB()->sendQuery($sql);
					}
				}
			}
		}
	}

	/**
	 * @see	 PackageInstallationPlugin::hasUninstall()
	 */
	public function hasUninstall() {
		if (($package = $this->getApplicationPackage()) !== null && $package->getPackage() == 'com.wcfsolutions.wsip') {
			try {
				$instanceNo = WCF_N.'_'.$package->getInstanceNo();
				$sql = "SELECT	COUNT(*) AS count
					FROM	wsip".$instanceNo."_publication_type
					WHERE	packageID = ".$this->installation->getPackageID();
				$installationCount = WCF::getDB()->getFirstRow($sql);
				return $installationCount['count'];
			}
			catch (Exception $e) {
				return false;
			}
		}
		else return false;
	}

	/**
	 * @see	 PackageInstallationPlugin::uninstall()
	 */
	public function uninstall() {
		if (($package = $this->getApplicationPackage()) !== null && $package->getPackage() == 'com.wcfsolutions.wsip') {
			$instanceNo = WCF_N.'_'.$package->getInstanceNo();

			// get publication types
			$publicationTypes = array();
			$sql = "SELECT	publicationType
				FROM	wsip".$instanceNo."_publication_type
				WHERE	packageID = ".$this->installation->getPackageID();
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$publicationTypes[] = $row['publicationType'];
			}

			if (count($publicationTypes)) {
				// delete comments
				$sql = "DELETE FROM	wsip".$instanceNo."_publication_object_comment
					WHERE		publicationType IN ('".implode("','", array_map('escapeString', $publicationTypes))."')";
				WCF::getDB()->sendQuery($sql);

				// delete category to publication types
				$sql = "DELETE FROM	wsip".$instanceNo."_category_to_publication_type
					WHERE		publicationType IN ('".implode("','", array_map('escapeString', $publicationTypes))."')";
				WCF::getDB()->sendQuery($sql);

				// delete publication types
				$sql = "DELETE FROM	wsip".$instanceNo."_publication_type
					WHERE		packageID = ".$this->installation->getPackageID();
				WCF::getDB()->sendQuery($sql);
			}
		}
	}

	/**
	 * Returns the show order value.
	 *
	 * @param	integer		$showOrder
	 * @param	string		$parentName
	 * @param	string		$columnName
	 * @param	string		$tableNameExtension
	 * @return	integer 	new show order
	 */
	protected function getShowOrder($showOrder, $parentName = null, $columnName = null, $tableNameExtension = '') {
		$instanceNo = WCF_N.'_'.$this->getApplicationPackage()->getInstanceNo();

		if ($showOrder === null) {
	        	// get greatest showOrder value
	          	$sql = "SELECT	MAX(showOrder) AS showOrder
				FROM	wsip".$instanceNo."_publication_type".$tableNameExtension."
				".($columnName !== null ? "WHERE ".$columnName." = '".escapeString($parentName)."'" : "");
			$maxShowOrder = WCF::getDB()->getFirstRow($sql);
			if (is_array($maxShowOrder) && isset($maxShowOrder['showOrder'])) {
				return $maxShowOrder['showOrder'] + 1;
			}
			else {
				return 1;
			}
	    }
	    else {
			// increase all showOrder values which are >= $showOrder
			$sql = "UPDATE	wsip".$instanceNo."_publication_type".$tableNameExtension."
				SET	showOrder = showOrder+1
				WHERE	showOrder >= ".$showOrder."
				".($columnName !== null ? "AND ".$columnName." = '".escapeString($parentName)."'" : "");
			WCF::getDB()->sendQuery($sql);
			// return the wanted showOrder level
			return $showOrder;
		}
	}

	/**
	 * Returns the application package instance.
	 *
	 * @return	Package
	 */
	protected function getApplicationPackage() {
		if ($this->installation->getPackage()->getParentPackage() !== null) {
			return $this->installation->getPackage()->getParentPackage();
		}
		return $this->installation->getPackage();
	}
}
?>