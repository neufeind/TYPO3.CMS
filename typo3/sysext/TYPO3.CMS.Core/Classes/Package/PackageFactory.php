<?php
namespace TYPO3\CMS\Core\Package;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */


/**
 * Class for building Packages
 */
class PackageFactory extends \TYPO3\Flow\Package\PackageFactory {


	/**
	 * Returns a package instance.
	 *
	 * @param string $packagesBasePath the base install path of packages,
	 * @param string $packagePath path to package, relative to base path
	 * @param string $packageKey key / name of the package
	 * @param string $classesPath path to the classes directory, relative to the package path
	 * @param string $manifestPath path to the package's Composer manifest, relative to package path, defaults to same path
	 * @return \TYPO3\Flow\Package\PackageInterface
	 * @throws \TYPO3\Flow\Package\Exception\CorruptPackageException
	 */
	public function create($packagesBasePath, $packagePath, $packageKey, $classesPath, $manifestPath = '') {
		$packageClassPathAndFilename = \TYPO3\Flow\Utility\Files::concatenatePaths(array($packagesBasePath, $packagePath, 'Classes/' . str_replace('.', '/', $packageKey) . '/Package.php'));
		$alternativeClassPathAndFilename = \TYPO3\Flow\Utility\Files::concatenatePaths(array($packagesBasePath, $packagePath, 'Classes/Package.php'));

		$packageClassPathAndFilename = file_exists($alternativeClassPathAndFilename) ? $alternativeClassPathAndFilename : $packageClassPathAndFilename;

		if (file_exists($packageClassPathAndFilename)) {
			require_once($packageClassPathAndFilename);
			/**
			 * @todo there should be a general method for getting Namespace from $packageKey
			 * @todo it should be tested if the package class implements the interface
			 */
			$packageClassName = str_replace('.', '\\', $packageKey) . '\Package';
			if (!class_exists($packageClassName)) {
				throw new \TYPO3\Flow\Package\Exception\CorruptPackageException(sprintf('The package "%s" does not contain a valid package class. Check if the file "%s" really contains a class called "%s".', $packageKey, $packageClassPathAndFilename, $packageClassName), 1327587091);
			}
		} else {
			$packageClassName = 'TYPO3\CMS\Core\Package\Package';
		}
		$packagePath = \TYPO3\Flow\Utility\Files::concatenatePaths(array($packagesBasePath, $packagePath)) . '/';

		/** @var $package \TYPO3\Flow\Package\PackageInterface */
		$package = new $packageClassName($this->packageManager, $packageKey, $packagePath, $classesPath, $manifestPath);

		return $package;
	}

}
?>