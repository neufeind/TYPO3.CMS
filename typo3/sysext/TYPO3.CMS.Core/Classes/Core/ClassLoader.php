<?php
namespace TYPO3\CMS\Core\Core;

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
class ClassLoader extends \TYPO3\Flow\Core\ClassLoader {

	/**
	 * A list of namespaces this class loader is definitely responsible for
	 * @var array
	 */
	protected $packageNamespaces = array(
		'TYPO3\Flow' => 10,
		'TYPO3\CMS\Core' => 10
	);

	/**
	 * Loads php files containing classes or interfaces found in the classes directory of
	 * a package and specifically registered classes.
	 *
	 * @param string $className Name of the class/interface to load
	 * @return boolean
	 */
	public function loadClass($className) {
		if ($className[0] === '\\') {
			$className = substr($className, 1);
		}

			// Loads any known proxied class:
		if ($this->classesCache !== NULL && $this->classesCache->requireOnce(str_replace('\\', '_', $className)) !== FALSE) {
			return TRUE;
		}

			// Workaround for Doctrine's annotation parser which does a class_exists() for annotations like "@param" and so on:
		if (isset($this->ignoredClassNames[$className]) || isset($this->ignoredClassNames[substr($className, strrpos($className, '\\') + 1)])) {
			return FALSE;
		}

			// Load classes from the CMS Core package at a very early stage where
			// no packages have been registered yet:
		if ($this->packages === array() && substr($className, 0, 14) === 'TYPO3\CMS\Core') {
			require(PATH_typo3 . 'sysext/TYPO3.CMS.Core/Classes/' . str_replace('\\', '/', substr($className, 15)) . '.php');
			return TRUE;
		}

			// Load classes from the Flow package at a very early stage where
			// no packages have been registered yet:
		if ($this->packages === array() && substr($className, 0, 10) === 'TYPO3\Flow') {
			require(FLOW_PATH_FLOW . 'Classes/TYPO3/Flow/' . str_replace('\\', '/', substr($className, 11)) . '.php');
			return TRUE;
		}

			// Loads any non-proxied class of registered packages:
		foreach ($this->packageNamespaces as $packageNamespace => $packageData) {
			if (substr($className, 0, $packageData['namespaceLength']) === $packageNamespace) {
				if ($this->considerTestsNamespace === TRUE && substr($className, $packageData['namespaceLength'] + 1, 16) === 'Tests\Functional') {
					$classPathAndFilename = $this->packages[str_replace('\\', '.', $packageNamespace)]->getPackagePath() . str_replace('\\', '/', substr($className, $packageData['namespaceLength'] + 1)) . '.php';
					if (file_exists($classPathAndFilename)) {
						require($classPathAndFilename);
						return TRUE;
					}
				} else {

						// The only reason using file_exists here is that Doctrine tries
						// out several combinations of annotation namespaces and thus also triggers
						// autoloading for non-existent classes in a valid package namespace
					$classPathAndFilename = $packageData['classesPath'] . '/'.  str_replace('\\', '/', $className) . '.php';
					if (file_exists($classPathAndFilename)) {
						require ($classPathAndFilename);
						return TRUE;
					}
				}
			}
		}

		return FALSE;
	}

}

?>