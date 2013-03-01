<?php
namespace TYPO3\CMS\Extensionmanager;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\CMS\Core\Package\Package as BasePackage;

/**
 * The TYPO3 Flow Package
 *
 */
class Package extends BasePackage {

	/**
	 * @var array
	 */
	protected $ignoredClassNames = array(
		'TYPO3\\CMS\\Extensionmanager\\Exception\\ExtensionManager',
		'TYPO3\\CMS\\Extensionmanager\\Service\\Management',
		'TYPO3\\CMS\\Extensionmanager\\Utility\\Connection\\Ter',
	);

}

?>
