<?php
namespace TYPO3\CMS\Core\Package;

use TYPO3\Flow\Annotations as Flow;

/**
 * The default TYPO3 Package Manager
 *
 * @api
 * @Flow\Scope("singleton")
 */
class PackageManager extends \TYPO3\Flow\Package\PackageManager {

	/**
	 * @var array
	 */
	protected $extAutoloadClassFiles;

	/**
	 * @var array
	 */
	protected $packagesBasePaths = array();

	/**
	 * @var array
	 */
	protected $packageAliasMap = array();


	public function __construct() {
		$this->packagesBasePaths = array(
			'local'     => PATH_typo3conf . 'ext',
			'global'    => PATH_typo3 . 'ext',
			'sysext'    => PATH_typo3 . 'sysext',
			'composer'  => FLOW_PATH_PACKAGES . 'Libraries',
		);
	}

	/**
	 * Initializes the package manager
	 *
	 * @param \TYPO3\Flow\Core\Bootstrap $bootstrap The current bootstrap
	 * @param string $packagesBasePath Absolute path of the Packages directory
	 * @param string $packageStatesPathAndFilename
	 * @return void
	 */
	public function initialize(\TYPO3\Flow\Core\Bootstrap $bootstrap, $packagesBasePath = FLOW_PATH_PACKAGES, $packageStatesPathAndFilename = '') {
		$this->systemLogger = new \TYPO3\Flow\Log\EarlyLogger();

		$this->bootstrap = $bootstrap;
		$this->packagesBasePath = $packagesBasePath;
		$this->packageStatesPathAndFilename = ($packageStatesPathAndFilename === '') ? FLOW_PATH_CONFIGURATION . 'PackageStates.php' : $packageStatesPathAndFilename;
		$this->packageFactory = new PackageFactory($this);

		$this->loadPackageStates();

		foreach ($this->packages as $packageKey => $package) {
			if ($package->isProtected() || (isset($this->packageStatesConfiguration['packages'][$packageKey]['state']) && $this->packageStatesConfiguration['packages'][$packageKey]['state'] === 'active')) {
				$this->activePackages[$packageKey] = $package;
			}
		}

		$this->classLoader->setPackages($this->activePackages);

		foreach ($this->activePackages as $package) {
			$package->boot($bootstrap);
		}

	}

	/**
	 * Scans all directories in the packages directories for available packages.
	 * For each package a Package object is created and stored in $this->packages.
	 *
	 * @return void
	 * @throws \TYPO3\Flow\Package\Exception\DuplicatePackageException
	 */
	protected function scanAvailablePackages() {
		$previousPackageStatesConfiguration = $this->packageStatesConfiguration;

		if (isset($this->packageStatesConfiguration['packages'])) {
			foreach ($this->packageStatesConfiguration['packages'] as $packageKey => $configuration) {
				if (!file_exists($this->packagesBasePath . $configuration['packagePath'])) {
					unset($this->packageStatesConfiguration['packages'][$packageKey]);
				}
			}
		} else {
			$this->packageStatesConfiguration['packages'] = array();
		}

		$packagePaths = $this->scanLoadedExtensions() + $this->scanPackagesInPath(FLOW_PATH_PACKAGES . 'Libraries');

		foreach ($packagePaths as $packagePath => $composerManifestPath) {
			$packagesBasePath = FLOW_PATH_PACKAGES;
			foreach ($this->packagesBasePaths as $basePath) {
				if (strpos($packagePath, $basePath) === 0) {
					$packagesBasePath = $basePath;
					break;
				}
			}
			try {
				$composerManifest = self::getComposerManifest($composerManifestPath);
				$packageKey = \TYPO3\CMS\Core\Package\PackageFactory::getPackageKeyFromManifest($composerManifest, $packagePath, $packagesBasePath);
				$this->composerNameToPackageKeyMap[strtolower($composerManifest->name)] = $packageKey;
				$this->packageStatesConfiguration['packages'][$packageKey]['manifestPath'] = substr($composerManifestPath, strlen($packagePath)) ?: '';
				$this->packageStatesConfiguration['packages'][$packageKey]['composerName'] = $composerManifest->name;
			} catch (\TYPO3\Flow\Package\Exception\MissingPackageManifestException $exception) {
				$relativePackagePath = substr($packagePath, strlen($packagesBasePath));
				$packageKey = substr($relativePackagePath, strpos($relativePackagePath, '/') + 1, -1);
			}
			if (!isset($this->packageStatesConfiguration['packages'][$packageKey]['state'])) {
				/**
 				 * @todo doesn't work, settings not available at this time
				 */
				if (is_array($this->settings['package']['inactiveByDefault']) && in_array($packageKey, $this->settings['package']['inactiveByDefault'], TRUE)) {
					$this->packageStatesConfiguration['packages'][$packageKey]['state'] = 'inactive';
				} else {
					$this->packageStatesConfiguration['packages'][$packageKey]['state'] = 'active';
				}
			}

			$this->packageStatesConfiguration['packages'][$packageKey]['packagePath'] = str_replace($this->packagesBasePath, '', $packagePath);

				// Change this to read the target from Composer or any other source
			$this->packageStatesConfiguration['packages'][$packageKey]['classesPath'] = \TYPO3\Flow\Package\Package::DIRECTORY_CLASSES;;
		}

		$this->registerPackagesFromConfiguration();

		if ($this->packageStatesConfiguration != $previousPackageStatesConfiguration) {
			$this->sortAndsavePackageStates();
		}
	}

	/**
	 * @return array
	 */
	protected function scanLoadedExtensions() {
		$loadedExtensions = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getLoadedExtensionListArray();
		// Clear file status cache to make sure we get good results from is_dir()
		clearstatcache();
		$loadedExtensionPaths = array();
		foreach ($loadedExtensions as $extensionKey) {
			// Determine if extension is installed locally, globally or system (in this order)
			foreach ($this->packagesBasePaths as $type => $packageBasePath) {
				if (@is_dir(($extensionPath = $packageBasePath . '/' . $extensionKey . '/'))) {
					$loadedExtensionPaths[$extensionPath] = $extensionPath;
					break;
				}
			}
		}
		return $loadedExtensionPaths;
	}

	/**
	 * Requires and registers all packages which were defined in packageStatesConfiguration
	 *
	 * @return void
	 * @throws \TYPO3\Flow\Package\Exception\CorruptPackageException
	 */
	protected function registerPackagesFromConfiguration() {
		foreach ($this->packageStatesConfiguration['packages'] as $packageKey => $stateConfiguration) {

			$packagePath = isset($stateConfiguration['packagePath']) ? $stateConfiguration['packagePath'] : NULL;
			$classesPath = isset($stateConfiguration['classesPath']) ? $stateConfiguration['classesPath'] : NULL;
			$manifestPath = isset($stateConfiguration['manifestPath']) ? $stateConfiguration['manifestPath'] : NULL;

			try {
				$package = $this->packageFactory->create($this->packagesBasePath, $packagePath, $packageKey, $classesPath, $manifestPath);
			} catch (\TYPO3\Flow\Package\Exception\InvalidPackagePathException $exception) {
				$this->unregisterPackageByPackageKey($packageKey);
				$this->systemLogger->log('Package ' . $packageKey . ' could not be loaded, it has been unregistered. Error description: "' . $exception->getMessage() . '" (' . $exception->getCode() . ')', LOG_WARNING);
				continue;
			}

			$this->registerPackage($package, FALSE);

			if (!$this->packages[$packageKey] instanceof \TYPO3\Flow\Package\PackageInterface) {
				throw new \TYPO3\Flow\Package\Exception\CorruptPackageException(sprintf('The package class in package "%s" does not implement PackageInterface.', $packageKey), 1300782487);
			}

			$this->packageKeys[strtolower($packageKey)] = $packageKey;
			if ($stateConfiguration['state'] === 'active') {
				$this->activePackages[$packageKey] = $this->packages[$packageKey];
			}
		}
	}

	/**
	 * Register a native Flow package
	 *
	 * @param string $packageKey The Package to be registered
	 * @param boolean $sortAndSave allows for not saving packagestates when used in loops etc.
	 * @return \TYPO3\Flow\Package\PackageInterface
	 * @throws \TYPO3\Flow\Package\Exception\CorruptPackageException
	 */
	public function registerPackage(\TYPO3\Flow\Package\PackageInterface $package, $sortAndSave = TRUE) {
		$packageKey = $package->getPackageKey();
		if ($this->isPackageAvailable($packageKey)) {
			throw new \TYPO3\Flow\Package\Exception\InvalidPackageStateException('Package "' . $packageKey . '" is already registered.', 1338996122);
		}

		$this->packages[$package->getPackageKey()] = $package;

		if ($package instanceof PackageInterface) {
			foreach ($package->getPackageReplacementKeys() as $packageToReplace => $versionConstraint) {
				$this->packageAliasMap[strtolower($packageToReplace)] = $package->getPackageKey();
			}
		}

		$this->packageStatesConfiguration['packages'][$packageKey]['packagePath'] = str_replace($this->packagesBasePath, '', $package->getPackagePath());
		$this->packageStatesConfiguration['packages'][$packageKey]['classesPath'] = str_replace($package->getPackagePath(), '', $package->getClassesPath());

		if ($sortAndSave === TRUE) {
			$this->sortAndSavePackageStates();
		}

		return $package;
	}

	/**
	 * Resolves a Flow package key from a composer package name.
	 *
	 * @param string $composerName
	 * @return string
	 * @throws \TYPO3\Flow\Package\Exception\InvalidPackageStateException
	 */
	public function getPackageKeyFromComposerName($composerName) {
		if (isset($this->packageAliasMap[$composerName])) {
			return $this->packageAliasMap[$composerName];
		}
		if (count($this->composerNameToPackageKeyMap) === 0) {
			foreach ($this->packageStatesConfiguration['packages'] as $packageKey => $packageStateConfiguration) {
				$this->composerNameToPackageKeyMap[strtolower($packageStateConfiguration['composerName'])] = $packageKey;
			}
		}
		$lowercasedComposerName = strtolower($composerName);
		if (!isset($this->composerNameToPackageKeyMap[$lowercasedComposerName])) {
			throw new \TYPO3\Flow\Package\Exception\InvalidPackageStateException('Could not find package with composer name "' . $composerName . '" in PackageStates configuration.', 1352320649);
		}
		return $this->composerNameToPackageKeyMap[$lowercasedComposerName];
	}

	/**
	 * @return array
	 */
	public function getExtAutoloadRegistry() {
		if (!isset($this->extAutoloadClassFiles)) {
			$classRegistry = array();
			foreach ($this->activePackages as $packageKey => $packageData) {
				try {
					$extensionAutoloadFile = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($packageKey, 'ext_autoload.php');
					if (@file_exists($extensionAutoloadFile)) {
						$classRegistry = array_merge($classRegistry, require $extensionAutoloadFile);
					}
				} catch (\BadFunctionCallException $e) {
				}
			}
			$this->extAutoloadClassFiles = $classRegistry;
		}
		return $this->extAutoloadClassFiles;
	}

	/**
	 * Returns a PackageInterface object for the specified package.
	 * A package is available, if the package directory contains valid MetaData information.
	 *
	 * @param string $packageKey
	 * @return \TYPO3\Flow\Package\PackageInterface The requested package object
	 * @throws \TYPO3\Flow\Package\Exception\UnknownPackageException if the specified package is not known
	 * @api
	 */
	public function getPackage($packageKey) {
		if (isset($this->packageAliasMap[$lowercasedPackageKey = strtolower($packageKey)])) {
			$packageKey = $this->packageAliasMap[$lowercasedPackageKey];
		}
		if (!$this->isPackageAvailable($packageKey)) {
			throw new \TYPO3\Flow\Package\Exception\UnknownPackageException('Package "' . $packageKey . '" is not available. Please check if the package exists and that the package key is correct (package keys are case sensitive).', 1166546734);
		}
		return $this->packages[$packageKey];
	}

}

?>