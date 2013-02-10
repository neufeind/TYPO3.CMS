<?php

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
 * Bootstrap for the command line
 */

if (PHP_SAPI !== 'cli') {
	echo(sprintf("The TYPO3 CMS command line script or sub process was executed with a '%s' PHP binary. Make sure that you specified a CLI capable PHP binary in your PATH or CMS's Settings.yaml.", PHP_SAPI) . PHP_EOL);
	exit(1);
}

require(__DIR__ . '/../Classes/Core/Bootstrap.php');

$context = trim(getenv('FLOW_CONTEXT'), '"\' ') ?: 'Development';
$_SERVER['FLOW_ROOTPATH'] = trim(getenv('FLOW_ROOTPATH'), '"\' ') ?: dirname($_SERVER['PHP_SELF']);

$bootstrap = new \TYPO3\CMS\Core\Core\Bootstrap($context);
$bootstrap->run();

?>