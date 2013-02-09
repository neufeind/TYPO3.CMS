<?php
namespace TYPO3\CMS\Core\Core\ClassLoader;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

require_once(FLOW_PATH_FLOW . 'Classes/TYPO3/Flow/Core/ClassLoader.php');

use TYPO3\Flow\Annotations as Flow;

/**
 * Class Loader implementation which loads .php files found in the classes
 * directory of an object.
 *
 * @Flow\Proxy(false)
 * @Flow\Scope("singleton")
 */
class EarlyClassLoaderStrategy extends \TYPO3\Flow\Core\ClassLoader {

	/**
	 * Loads php files containing classes or interfaces found in the classes directory of
	 * a package and specifically registered classes.
	 *
	 * @param string $className Name of the class/interface to load
	 * @return boolean
	 */
	public function loadClass($className) {
		// Load classes from the CMS Core and Flow package at a very early stage
		if (substr($className, 0, 14) === 'TYPO3\CMS\Core') {
			require(PATH_typo3 . 'sysext/TYPO3.CMS.Core/Classes/' . str_replace('\\', '/', substr($className, 15)) . '.php');
			return TRUE;
		}
		if (substr($className, 0, 10) === 'TYPO3\Flow') {
			require(FLOW_PATH_FLOW . 'Classes/TYPO3/Flow/' . str_replace('\\', '/', substr($className, 11)) . '.php');
			return TRUE;
		}
		return FALSE;
	}

}

?>