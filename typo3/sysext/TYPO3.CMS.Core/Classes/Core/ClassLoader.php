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
	 * @var ClassAliasMap
	 */
	protected $classAliasMap;

	/**
	 * @var ClassAliasMap
	 */
	static protected $staticAliasMap;

	/**
	 * A list of namespaces this class loader is definitely responsible for
	 * @var array
	 */
	protected $packageNamespaces = array(
		'TYPO3\Flow' => 10,
		'TYPO3\CMS\Core' => 14
	);

	/**

	 * @param ClassAliasMap
	 */
	public function injectClassAliasMap(ClassAliasMap $classAliasMap) {
		$this->classAliasMap = $classAliasMap;
		static::$staticAliasMap = $classAliasMap;
	}

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

		$realClassName = $this->classAliasMap->getClassNameForAlias($className);
		$aliasClassName = $this->classAliasMap->getAliasForClassName($className);
		$hasAliasClassName = ($aliasClassName !== $className);
		$lookUpClassName = ($hasRealClassName = $className !== $realClassName) ? $realClassName : $className;
		$classLoaded = FALSE;

		// Loads any known proxied class:
		if ($this->classesCache !== NULL && $this->classesCache->requireOnce(str_replace('\\', '_', $lookUpClassName)) !== FALSE) {
			$classLoaded = TRUE;
		}

		// Workaround for Doctrine's annotation parser which does a class_exists() for annotations like "@param" and so on:
		if (!$classLoaded && (isset($this->ignoredClassNames[$lookUpClassName]) || isset($this->ignoredClassNames[substr($lookUpClassName, strrpos($lookUpClassName, '\\') + 1)]))) {
			return FALSE;
		}

		// Load classes from the CMS Core package at a very early stage where
		// no packages have been registered yet:
		if (!$classLoaded && $this->packages === array() && substr($lookUpClassName, 0, 14) === 'TYPO3\CMS\Core') {
			require(PATH_typo3 . 'sysext/TYPO3.CMS.Core/Classes/' . str_replace('\\', '/', substr($lookUpClassName, 15)) . '.php');
			$classLoaded = TRUE;
		}

		// Load classes from the Flow package at a very early stage where
		// no packages have been registered yet:
		if (!$classLoaded && $this->packages === array() && substr($lookUpClassName, 0, 10) === 'TYPO3\Flow') {
			require(FLOW_PATH_FLOW . 'Classes/TYPO3/Flow/' . str_replace('\\', '/', substr($lookUpClassName, 11)) . '.php');
			$classLoaded = TRUE;
		}

		if (!$classLoaded) {
			// Loads any non-proxied class of registered packages:
			foreach ($this->packageNamespaces as $packageNamespace => $packageData) {
				if (substr($lookUpClassName, 0, $packageData['namespaceLength']) === $packageNamespace) {
					if ($this->considerTestsNamespace === TRUE && substr($lookUpClassName, $packageData['namespaceLength'] + 1, 16) === 'Tests\Functional') {
						$classPathAndFilename = $this->packages[str_replace('\\', '.', $packageNamespace)]->getPackagePath() . str_replace('\\', '/', substr($lookUpClassName, $packageData['namespaceLength'] + 1)) . '.php';
						if (file_exists($classPathAndFilename)) {
							require($classPathAndFilename);
							$classLoaded = TRUE;
							break;
						}
					} else {

							// The only reason using file_exists here is that Doctrine tries
							// out several combinations of annotation namespaces and thus also triggers
							// autoloading for non-existent classes in a valid package namespace
						if ($packageData['substituteNamespaceInPath']) {
							$classPathAndFilename = $packageData['classesPath'] . '/'.  str_replace('\\', '/', substr($lookUpClassName, $packageData['namespaceLength'])) . '.php';
						} else {
							$classPathAndFilename = $packageData['classesPath'] . '/'.  str_replace('\\', '/', $lookUpClassName) . '.php';
						}
						if (file_exists($classPathAndFilename)) {
							require ($classPathAndFilename);
							$classLoaded = TRUE;
							break;
						}
					}
				}
			}
		}

		if ($hasRealClassName && !class_exists($className, FALSE)) {
			class_alias($realClassName, $className);
		}
		if ($hasAliasClassName && !class_exists($aliasClassName, FALSE)) {
			class_alias($className, $aliasClassName);
		}
		return $classLoaded;
	}

	/**
	 * Sets the available packages
	 *
	 * @param array $packages An array of \TYPO3\Flow\Package\Package objects
	 * @return void
	 */
	public function setPackages(array $packages) {
		$this->classAliasMap->setPackages($packages);
		$this->packages = $packages;
		foreach ($packages as $package) {
			$this->packageNamespaces[$package->getNamespace()] = array(
				'namespaceLength' => strlen($package->getNamespace()),
				'classesPath' => $package->getClassesPath(),
				'substituteNamespaceInPath' => ($package instanceof \TYPO3\CMS\Core\Package\Package)
			);
		}

			// sort longer package namespaces first, to find specific matches before generic ones
		uksort($this->packageNamespaces, function($a, $b) {
			if (strlen($a) === strlen($b)) {
				return strcmp($a, $b);
			}
			return (strlen($a) > strlen($b)) ? -1 : 1;
		});
	}


	/**
	 * @param string $alias
	 * @return mixed
	 */
	static public function getClassNameForAlias($alias) {
		return static::$staticAliasMap->getClassNameForAlias($alias);
	}


	/**
	 * @param string $className
	 * @return mixed
	 */
	static public function getAliasForClassName($className) {
		return static::$staticAliasMap->getAliasForClassName($className);
	}

}

?>