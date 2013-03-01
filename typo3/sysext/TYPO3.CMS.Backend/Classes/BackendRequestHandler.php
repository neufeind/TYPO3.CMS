<?php
namespace TYPO3\CMS\Backend;
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
 * Enter descriptions here
 *
 * @package $PACKAGE$
 * @subpackage $SUBPACKAGE$
 * @scope prototype
 * @entity
 * @api
 */
class BackendRequestHandler extends \TYPO3\Flow\Http\RequestHandler {

	/**
	 * @var \TYPO3\CMS\Core\Core\Bootstrap
	 */
	protected $bootstrap;

	/**
	 * Handles a raw request
	 *
	 * @return void
	 * @api
	 */
	public function handleRequest() {
		// Create the request very early so the Resource Management has a chance to grab it:
		$this->request = \TYPO3\Flow\Http\Request::createFromEnvironment();
		$this->response = new \TYPO3\Flow\Http\Response();
		$this->boot();

		$this->bootstrap
			->startOutputBuffering()
			->loadConfigurationAndInitialize()
			->loadTypo3LoadedExtAndExtLocalconf(TRUE)
			->applyAdditionalConfigurationSettings()
			->initializeTypo3DbGlobal(FALSE)
			->checkLockedBackendAndRedirectOrDie()
			->checkBackendIpOrDie()
			->checkSslBackendAndRedirectIfNeeded()
			->redirectToInstallToolIfDatabaseCredentialsAreMissing()
			->checkValidBrowserOrDie()
			->establishDatabaseConnection()
			->loadExtensionTables(TRUE)
			->initializeSpriteManager()
			->initializeBackendUser()
			->initializeBackendUserMounts()
			->initializeLanguageObject()
			->initializeModuleMenuObject()
			->initializeBackendTemplate()
			->endOutputBufferingAndCleanPreviousOutput()
			->initializeOutputCompression();
	}

	/**
	 * Boots up Flow to runtime
	 *
	 * @return void
	 */
	protected function boot() {
//		$sequence = $this->bootstrap->buildEssentialsSequence('runtime');
//		$sequence->invoke($this->bootstrap);
		parent::boot();
	}

	/**
	 * Checks if the request handler can handle the current request.
	 *
	 * @return mixed TRUE or an integer > 0 if it can handle the request, otherwise FALSE or an integer < 0
	 * @api
	 */
	public function canHandleRequest() {
		return TYPO3_MODE === 'BE' && !defined('TYPO3_cliMode');
	}

	/**
	 * Returns the priority - how eager the handler is to actually handle the
	 * request. An integer > 0 means "I want to handle this request" where
	 * "100" is default. "0" means "I am a fallback solution".
	 *
	 * @return integer The priority of the request handler
	 * @api
	 */
	public function getPriority() {
		return 125;
	}
}
