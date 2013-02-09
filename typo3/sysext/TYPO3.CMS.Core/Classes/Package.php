<?php
namespace TYPO3\CMS\Core;

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
	 * @var boolean
	 */
	protected $protected = TRUE;

	/**
	 * Invokes custom PHP code directly after the package manager has been initialized.
	 *
	 * @param \TYPO3\Flow\Core\Bootstrap $bootstrap The current bootstrap
	 * @return void
	 */
	public function boot(\TYPO3\Flow\Core\Bootstrap $bootstrap) {
//		$bootstrap->registerRequestHandler(new \TYPO3\Flow\Cli\SlaveRequestHandler($bootstrap));
//		$bootstrap->registerRequestHandler(new \TYPO3\Flow\Cli\CommandRequestHandler($bootstrap));
//		$bootstrap->registerRequestHandler(new \TYPO3\Flow\Http\RequestHandler($bootstrap));
	}
}

?>
