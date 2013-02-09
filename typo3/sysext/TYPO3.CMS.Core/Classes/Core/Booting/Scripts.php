<?php
namespace TYPO3\CMS\Core\Core\Booting;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Thomas Maroschik <tmaroschik@dfau.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 */
class Scripts extends \TYPO3\Flow\Core\Booting\Scripts {

	/**
	 *
	 */
	static public function defineConstants() {
		// This version, branch and copyright
		define('TYPO3_version', '6.1-dev');
		define('TYPO3_branch', '6.1');
		define('TYPO3_copyright_year', '1998-2012');
		// TYPO3 external links
		define('TYPO3_URL_GENERAL', 'http://typo3.org/');
		define('TYPO3_URL_ORG', 'http://typo3.org/');
		define('TYPO3_URL_LICENSE', 'http://typo3.org/licenses');
		define('TYPO3_URL_EXCEPTION', 'http://typo3.org/go/exception/v4/');
		define('TYPO3_URL_MAILINGLISTS', 'http://lists.typo3.org/cgi-bin/mailman/listinfo');
		define('TYPO3_URL_DOCUMENTATION', 'http://typo3.org/documentation/');
		define('TYPO3_URL_DOCUMENTATION_TSREF', 'http://typo3.org/documentation/document-library/core-documentation/doc_core_tsref/current/view/');
		define('TYPO3_URL_DOCUMENTATION_TSCONFIG', 'http://typo3.org/documentation/document-library/core-documentation/doc_core_tsconfig/current/view/');
		define('TYPO3_URL_CONSULTANCY', 'http://typo3.org/support/professional-services/');
		define('TYPO3_URL_CONTRIBUTE', 'http://typo3.org/contribute/');
		define('TYPO3_URL_SECURITY', 'http://typo3.org/teams/security/');
		define('TYPO3_URL_DOWNLOAD', 'http://typo3.org/download/');
		define('TYPO3_URL_SYSTEMREQUIREMENTS', 'http://typo3.org/about/typo3-the-cms/system-requirements/');
		define('TYPO3_URL_DONATE', 'http://typo3.org/donate/online-donation/');
		// A tabulator, a linefeed, a carriage return, a CR-LF combination
		define('TAB', chr(9));
		define('LF', chr(10));
		define('CR', chr(13));
		define('CRLF', CR . LF);
		// Security related constant: Default value of fileDenyPattern
		define('FILE_DENY_PATTERN_DEFAULT', '\\.(php[3-6]?|phpsh|phtml)(\\..*)?$|^\\.htaccess$');
		// Security related constant: List of file extensions that should be registered as php script file extensions
		define('PHP_EXTENSIONS_DEFAULT', 'php,php3,php4,php5,php6,phpsh,inc,phtml');
		// List of extensions required to run the core
		define('REQUIRED_EXTENSIONS', 'TYPO3.Flow,TYPO3.CMS.Core,backend,frontend,cms,lang,sv,extensionmanager,recordlist,extbase,TYPO3.CMS.Fluid,cshmanual,TYPO3.Party');
		// Operating system identifier
		// Either "WIN" or empty string
		define('TYPO3_OS', (!stristr(PHP_OS, 'darwin') && stristr(PHP_OS, 'win')) ? 'WIN' : '');
		static::defineFlowConstants();
	}

	/**
	 *
	 */
	static protected function defineFlowConstants() {
		if (defined('FLOW_SAPITYPE')) {
			return;
		}
		define('FLOW_SAPITYPE', (PHP_SAPI === 'cli' ? 'CLI' : 'Web'));

		//@TODO This one has to be replaced everytime FLOW is beeing updated
		define('FLOW_VERSION_BRANCH', '2.0');
	}

	/**
	 * @param string $relativePathPart
	 */
	static public function definePathConstants(\TYPO3\Flow\Core\ApplicationContext $context) {
		// Relative path from document root to typo3/ directory
		// Hardcoded to "typo3/"
		define('TYPO3_mainDir', 'typo3/');
		// Absolute path of the entry script that was called
		// All paths are unified between Windows and Unix, so the \ of Windows is substituted to a /
		// Example "/var/www/instance-name/htdocs/typo3conf/ext/wec_map/mod1/index.php"
		// Example "c:/var/www/instance-name/htdocs/typo3/backend.php" for a path in Windows
		define('PATH_thisScript', Scripts::getPathThisScript());
		// Absolute path of the document root of the instance with trailing slash
		// Example "/var/www/instance-name/htdocs/"
		$relativePathPart = '';
		switch (array_slice(explode('/', (string) $context), 1, 1)) {
			case 'CLI':
			case 'Backend':
				$relativePathPart = TYPO3_mainDir;
				break;
		}
		define('PATH_site', Scripts::getPathSite($relativePathPart));
		// Absolute path of the typo3 directory of the instance with trailing slash
		// Example "/var/www/instance-name/htdocs/typo3/"
		define('PATH_typo3', PATH_site . TYPO3_mainDir);
		// Relative path (from the PATH_typo3) to a BE module NOT using mod.php dispatcher with trailing slash
		// Example "sysext/perms/mod/" for an extension installed in typo3/sysext/
		// Example "install/" for the install tool entry script
		// Example "../typo3conf/ext/templavoila/mod2/ for an extension installed in typo3conf/ext/
		define('PATH_typo3_mod', defined('TYPO3_MOD_PATH') ? TYPO3_MOD_PATH : '');
		// Absolute path to the t3lib directory with trailing slash
		// Example "/var/www/instance-name/htdocs/t3lib/"
		define('PATH_t3lib', PATH_site . 't3lib/');
		// Absolute path to the typo3conf directory with trailing slash
		// Example "/var/www/instance-name/htdocs/typo3conf/"
		define('PATH_typo3conf', PATH_site . 'typo3conf/');
		// Absolute path to the tslib directory with trailing slash
		// Example "/var/www/instance-name/htdocs/typo3/sysext/cms/tslib/"
		define('PATH_tslib', PATH_typo3 . 'sysext/cms/tslib/');
		static::defineFlowPathConstants();
	}

	/**
	 *
	 */
	static protected function defineFlowPathConstants() {
		if (!defined('FLOW_PATH_FLOW')) {
			define('FLOW_PATH_FLOW', PATH_typo3 . 'sysext/TYPO3.Flow/');
		}

		if (!defined('FLOW_PATH_ROOT')) {
			$rootPath = PATH_site;
			if ($rootPath !== FALSE) {
				$testPath = \TYPO3\Flow\Utility\Files::getUnixStylePath(realpath(\TYPO3\Flow\Utility\Files::concatenatePaths(array(PATH_typo3, 'sysext/TYPO3.Flow')))) . '/';
				$expectedPath = \TYPO3\Flow\Utility\Files::getUnixStylePath(realpath(FLOW_PATH_FLOW)) . '/';
				if ($testPath !== $expectedPath) {
					echo('Flow: Invalid root path. (Error #1248964375)' . PHP_EOL . '"' . $testPath . '" does not lead to' . PHP_EOL . '"' . $expectedPath .'"' . PHP_EOL);
					exit(1);
				}
				define('FLOW_PATH_ROOT', $rootPath);
				unset($rootPath);
				unset($testPath);
			}
		}

		if (FLOW_SAPITYPE === 'CLI') {
			if (!defined('FLOW_PATH_ROOT')) {
				echo('Flow: No root path defined in environment variable FLOW_ROOTPATH (Error #1248964376)' . PHP_EOL);
				exit(1);
			}
			if (!defined('FLOW_PATH_WEB')) {
				if (isset($_SERVER['FLOW_WEBPATH']) && is_dir($_SERVER['FLOW_WEBPATH'])) {
					define('FLOW_PATH_WEB', \TYPO3\Flow\Utility\Files::getUnixStylePath(realpath($_SERVER['FLOW_WEBPATH'])) . '/');
				} else {
					define('FLOW_PATH_WEB', FLOW_PATH_ROOT . 'Web/');
				}
			}
		} else {
			if (!defined('FLOW_PATH_ROOT')) {
				define('FLOW_PATH_ROOT', \TYPO3\Flow\Utility\Files::getUnixStylePath(realpath(dirname($_SERVER['SCRIPT_FILENAME']) . '/../')) . '/');
			}
			define('FLOW_PATH_WEB', \TYPO3\Flow\Utility\Files::getUnixStylePath(realpath(dirname($_SERVER['SCRIPT_FILENAME']))) . '/');
		}

		define('FLOW_PATH_CONFIGURATION', PATH_typo3conf);
		define('FLOW_PATH_DATA', FLOW_PATH_ROOT . 'uploads/');
		define('FLOW_PATH_PACKAGES', PATH_typo3conf . 'Packages/');
	}

	/**
	 *
	 */
	static public function ensureLinkedFlowDirectories() {
		if (!is_dir(FLOW_PATH_CONFIGURATION) && !is_link(FLOW_PATH_CONFIGURATION)) {
			if (!@mkdir(FLOW_PATH_CONFIGURATION)) {
				echo('TYPO3 CMS could not create the directory "' . FLOW_PATH_CONFIGURATION . '". Please check the file permissions manually.');
				exit(1);
			}
		}
		if (!is_dir(FLOW_PATH_PACKAGES) && !is_link(FLOW_PATH_PACKAGES)) {
			if (!@mkdir(FLOW_PATH_PACKAGES)) {
				echo('TYPO3 CMS could not create the directory "' . FLOW_PATH_PACKAGES . '". Please check the file permissions manually.');
				exit(1);
			}
		}
		if (!is_link(FLOW_PATH_PACKAGES . 'Application')) {
			if (!@symlink(PATH_typo3conf . 'ext/', FLOW_PATH_PACKAGES . 'Application')) {
				echo('TYPO3 CMS could not link the directory "' . FLOW_PATH_PACKAGES . 'Application". Please check the file permissions manually.');
				exit(1);
			}
		}
		if (!is_link(FLOW_PATH_PACKAGES . 'Framework')) {
			if (!@symlink(PATH_typo3 . 'sysext/', FLOW_PATH_PACKAGES . 'Framework')) {
				echo('TYPO3 CMS could not link the directory "' . FLOW_PATH_PACKAGES . 'Framework". Please check the file permissions manually.');
				exit(1);
			}
		}
		if (!is_dir(FLOW_PATH_PACKAGES . 'Libraries') && !is_link(FLOW_PATH_PACKAGES . 'Libraries')) {
			if (!@mkdir(FLOW_PATH_PACKAGES . 'Libraries')) {
				echo('TYPO3 CMS could not create the directory "' . FLOW_PATH_PACKAGES . 'Libraries". Please check the file permissions manually.');
				exit(1);
			}
		}
		if (!is_dir(FLOW_PATH_DATA) && !is_link(FLOW_PATH_DATA)) {
			if (!@mkdir(FLOW_PATH_DATA)) {
				echo('TYPO3 CMS could not link the directory "' . FLOW_PATH_DATA . '". Please check the file permissions manually.');
				exit(1);
			}
		}
		if (!is_link(FLOW_PATH_DATA . 'Persistent')) {
			if (!@symlink(FLOW_PATH_DATA, FLOW_PATH_DATA . 'Persistent')) {
				echo('TYPO3 CMS could not link the directory "' . FLOW_PATH_DATA . 'Persistent". Please check the file permissions manually.');
				exit(1);
			}
		}
		if (!is_link(FLOW_PATH_DATA . 'Temporary')) {
			if (!@symlink(PATH_site . 'typo3temp/', FLOW_PATH_DATA . 'Temporary')) {
				echo('TYPO3 CMS could not link the directory "' . FLOW_PATH_DATA . 'Temporary". Please check the file permissions manually.');
				exit(1);
			}
		}
	}

	/**
	 * Initializes the Class Loader
	 *
	 * @param \TYPO3\Flow\Core\Bootstrap $bootstrap
	 * @return void
	 */
	static public function initializeClassLoader(\TYPO3\Flow\Core\Bootstrap $bootstrap) {
		require_once(PATH_typo3 . 'sysext/TYPO3.CMS.Core/Classes/Core/ClassLoader.php');
		$classLoader = new \TYPO3\CMS\Core\Core\ClassLoader();
		spl_autoload_register(array($classLoader, 'loadClass'), TRUE, TRUE);
		$bootstrap->setEarlyInstance('TYPO3\Flow\Core\ClassLoader', $classLoader);
	}


	/**
	 * Populate the local configuration.
	 * Merge default TYPO3_CONF_VARS with content of typo3conf/LocalConfiguration.php,
	 * execute typo3conf/AdditionalConfiguration.php, define database related constants.
	 *
	 * @param \TYPO3\Flow\Core\Bootstrap $bootstrap
	 * @return \TYPO3\CMS\Core\Core\Bootstrap
	 */
	static public function initializeLocalConfiguration(\TYPO3\Flow\Core\Bootstrap $bootstrap) {
		try {
			// We need an early instance of the configuration manager.
			// Since makeInstance relies on the object configuration, we create it here with new instead
			// and register it as singleton instance for later use.
			$configuarationManager = new \TYPO3\CMS\Core\Configuration\ConfigurationManager();
			$bootstrap->setEarlyInstance('TYPO3\CMS\Core\Configuration\ConfigurationManager', $configuarationManager);
			$configuarationManager->exportConfiguration();
		} catch (\Exception $e) {
			die($e->getMessage());
		}
	}

	/**
	 * Initializes the package system and loads the package configuration and settings
	 * provided by the packages.
	 *
	 * @param \TYPO3\Flow\Core\Bootstrap $bootstrap
	 * @return void
	 */
	static public function initializePackageManagement(\TYPO3\Flow\Core\Bootstrap $bootstrap) {
		$packageManager = new \TYPO3\CMS\Core\Package\PackageManager();
		$bootstrap->setEarlyInstance('TYPO3\Flow\Package\PackageManagerInterface', $packageManager);
		$packageManager->injectClassLoader($bootstrap->getEarlyInstance('TYPO3\Flow\Core\ClassLoader'));
		$packageManager->initialize($bootstrap);
	}

	/**
	 * Calculate PATH_thisScript
	 *
	 * First step in path calculation: Goal is to find the absolute path of the entry script
	 * that was called without resolving any links. This is important since the TYPO3 entry
	 * points are often linked to a central core location, so we can not use the php magic
	 * __FILE__ here, but resolve the called script path from given server environments.
	 *
	 * This path is important to calculate the document root (PATH_site). The strategy is to
	 * find out the script name that was called in the first place and to subtract the local
	 * part from it to find the document root.
	 *
	 * @return string Absolute path to entry script
	 */
	static public function getPathThisScript() {
		if (defined('TYPO3_cliMode') && TYPO3_cliMode === TRUE) {
			return static::getPathThisScriptCli();
		} else {
			return static::getPathThisScriptNonCli();
		}
	}

	/**
	 * Calculate path to entry script if not in cli mode.
	 *
	 * Depending on the environment, the script path is found in different $_SERVER variables.
	 *
	 * @return string Absolute path to entry script
	 */
	static protected function getPathThisScriptNonCli() {
		$cgiPath = '';
		if (isset($_SERVER['ORIG_PATH_TRANSLATED'])) {
			$cgiPath = $_SERVER['ORIG_PATH_TRANSLATED'];
		} elseif (isset($_SERVER['PATH_TRANSLATED'])) {
			$cgiPath = $_SERVER['PATH_TRANSLATED'];
		}
		if ($cgiPath && (PHP_SAPI === 'fpm-fcgi' || PHP_SAPI === 'cgi' || PHP_SAPI === 'isapi' || PHP_SAPI === 'cgi-fcgi')) {
			$scriptPath = $cgiPath;
		} else {
			if (isset($_SERVER['ORIG_SCRIPT_FILENAME'])) {
				$scriptPath = $_SERVER['ORIG_SCRIPT_FILENAME'];
			} else {
				$scriptPath = $_SERVER['SCRIPT_FILENAME'];
			}
		}
		// Replace \ to / for Windows
		$scriptPath = str_replace('\\', '/', $scriptPath);
		// Replace double // to /
		$scriptPath = str_replace('//', '/', $scriptPath);
		return $scriptPath;
	}

	/**
	 * Calculate path to entry script if in cli mode.
	 *
	 * First argument of a cli script is the path to the script that was called. If the script does not start
	 * with / (or A:\ for Windows), the path is not absolute yet, and the current working directory is added.
	 *
	 * @return string Absolute path to entry script
	 */
	static protected function getPathThisScriptCli() {
		// Possible relative path of the called script
		if (isset($_SERVER['argv'][0])) {
			$scriptPath = $_SERVER['argv'][0];
		} elseif (isset($_ENV['_'])) {
			$scriptPath = $_ENV['_'];
		} else {
			$scriptPath = $_SERVER['_'];
		}
		// Find out if path is relative or not
		$isRelativePath = FALSE;
		if (TYPO3_OS === 'WIN') {
			if (!preg_match('/^([A-Z]:)?\\\\/', $scriptPath)) {
				$isRelativePath = TRUE;
			}
		} else {
			if (substr($scriptPath, 0, 1) !== '/') {
				$isRelativePath = TRUE;
			}
		}
		// Concatenate path to current working directory with relative path and remove "/./" constructs
		if ($isRelativePath) {
			if (isset($_SERVER['PWD'])) {
				$workingDirectory = $_SERVER['PWD'];
			} else {
				$workingDirectory = getcwd();
			}
			$scriptPath = $workingDirectory . '/' . preg_replace('/\\.\\//', '', $scriptPath);
		}
		return $scriptPath;
	}

	/**
	 * Calculate the document root part to the instance from PATH_thisScript
	 *
	 * There are two ways to hint correct calculation:
	 * Either an explicit specified sub path or the defined constant TYPO3_MOD_PATH. Which one is
	 * used depends on which entry script was called in the first place.
	 *
	 * We have two main scenarios for entry points:
	 * - Directly called documentRoot/index.php (-> FE call or eiD include): index.php sets $relativePathPart to
	 * empty string to hint this code that the document root is identical to the directory the script is located at.
	 * - An indirect include of typo3/init.php (-> a backend module, the install tool, or scripts like thumbs.php).
	 * If init.php is included we distinguish two cases:
	 * -- A backend module defines 'TYPO3_MOD_PATH': This is the case for "old" modules that are not called through
	 * "mod.php" dispatcher, and in the install tool. The TYPO3_MOD_PATH defines the relative path to the typo3/
	 * directory. This is taken as base to calculate the document root.
	 * -- A script includes init.php and does not define 'TYPO3_MOD_PATH': This is the case for the mod.php dispatcher
	 * and other entry scripts like 'cli_dispatch.phpsh' or 'thumbs.php' that are located parallel to init.php. In
	 * this case init.php sets 'typo3/' as $relativePathPart as base to calculate the document root.
	 *
	 * This basically boils down to the following code:
	 * If TYPO3_MOD_PATH is defined, subtract this 'local' part from the entry point directory, else use
	 * $relativePathPart to subtract this from the the script entry point to find out the document root.
	 *
	 * @param string $relativePathPart Relative directory part from document root to script path if TYPO3_MOD_PATH is not used
	 * @return string Absolute path to document root of installation
	 */
	static public function getPathSite($relativePathPart) {
		// If end of path is not "typo3/" and TYPO3_MOD_PATH is given
		if (defined('TYPO3_MOD_PATH')) {
			return static::getPathSiteByTypo3ModulePath();
		} else {
			return static::getPathSiteByRelativePathPart($relativePathPart);
		}
	}

	/**
	 * Calculate document root by TYPO3_MOD_PATH
	 *
	 * TYPO3_MOD_PATH can have the following values:
	 * - "sysext/extensionName/path/entryScript.php" -> extension is below 'docRoot'/typo3/sysext
	 * - "ext/extensionName/path/entryScript.php" -> extension is below 'docRoot'/typo3/ext
	 * - "../typo3conf/ext/extensionName/path/entryScript.php" -> extension is below 'docRoot'/typo3conf/ext
	 * - "install/index.php" -> install tool in 'docRoot'/typo3/install/
	 *
	 * The method unifies the above and subtracts the calculated path part from PATH_thisScript
	 *
	 * @return string Absolute path to document root of installation
	 */
	static protected function getPathSiteByTypo3ModulePath() {
		if (substr(TYPO3_MOD_PATH, 0, strlen('sysext/')) === 'sysext/' || substr(TYPO3_MOD_PATH, 0, strlen('ext/')) === 'ext/' || substr(TYPO3_MOD_PATH, 0, strlen('install/')) === 'install/') {
			$pathPartRelativeToDocumentRoot = TYPO3_mainDir . TYPO3_MOD_PATH;
		} elseif (substr(TYPO3_MOD_PATH, 0, strlen('../typo3conf/')) === '../typo3conf/') {
			$pathPartRelativeToDocumentRoot = substr(TYPO3_MOD_PATH, 3);
		} else {
			die('Unable to determine TYPO3 document root.');
		}
		$entryScriptDirectory = static::getUnifiedDirectoryNameWithTrailingSlash(PATH_thisScript);
		return substr($entryScriptDirectory, 0, -strlen($pathPartRelativeToDocumentRoot));
	}

	/**
	 * Find out document root by subtracting $relativePathPart from PATH_thisScript
	 *
	 * @param string $relativePathPart Relative part of script from document root
	 * @return string Absolute path to document root of installation
	 */
	static protected function getPathSiteByRelativePathPart($relativePathPart) {
		$entryScriptDirectory = static::getUnifiedDirectoryNameWithTrailingSlash(PATH_thisScript);
		if (strlen($relativePathPart) > 0) {
			$pathSite = substr($entryScriptDirectory, 0, -strlen($relativePathPart));
		} else {
			$pathSite = $entryScriptDirectory;
		}
		return $pathSite;
	}

	/**
	 * Remove file name from script path and unify for Windows and Unix
	 *
	 * @param string $absolutePath Absolute path to script
	 * @return string Directory name of script file location, unified for Windows and Unix
	 */
	static protected function getUnifiedDirectoryNameWithTrailingSlash($absolutePath) {
		$directory = dirname($absolutePath);
		if (TYPO3_OS === 'WIN') {
			$directory = str_replace('\\', '/', $directory);
		}
		return $directory . '/';
	}

}


?>