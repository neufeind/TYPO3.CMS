<?php

if (!function_exists('xdebug')) {
		// Simple debug function which prints output immediately
	function xdebug($var = '', $debugTitle = 'xdebug') {
			// If you wish to use the debug()-function, and it does not output something,
			// please edit the IP mask in TYPO3_CONF_VARS
		if (!\TYPO3\CMS\Core\Utility\GeneralUtility::cmpIP(\TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('REMOTE_ADDR'), $GLOBALS['TYPO3_CONF_VARS']['SYS']['devIPmask'])) {
			return;
		}
		\TYPO3\CMS\Core\Utility\DebugUtility::debug($var, $debugTitle);
	}
}

if (!function_exists('debug')) {
		// Debug function which calls $GLOBALS['error'] error handler if available
	function debug($variable = '', $name = '*variable*', $line = '*line*', $file = '*file*', $recursiveDepth = 3, $debugLevel = E_DEBUG) {
			// If you wish to use the debug()-function, and it does not output something,
			// please edit the IP mask in TYPO3_CONF_VARS
		if (!\TYPO3\CMS\Core\Utility\GeneralUtility::cmpIP(\TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('REMOTE_ADDR'), $GLOBALS['TYPO3_CONF_VARS']['SYS']['devIPmask'])) {
			return;
		}
		if (is_object($GLOBALS['error']) && @is_callable(array($GLOBALS['error'], 'debug'))) {
			$GLOBALS['error']->debug($variable, $name, $line, $file, $recursiveDepth, $debugLevel);
		} else {
			$title = $name === '*variable*' ? '' : $name;
			$group = $line === '*line*' ? NULL : $line;
			\TYPO3\CMS\Core\Utility\DebugUtility::debug($variable, $title, $group);
		}
	}
}

if (!function_exists('debugBegin')) {
	function debugBegin() {
		if (is_object($GLOBALS['error']) && @is_callable(array($GLOBALS['error'], 'debugBegin'))) {
			$GLOBALS['error']->debugBegin();
		}
	}
}

if (!function_exists('debugEnd')) {
	function debugEnd() {
		if (is_object($GLOBALS['error']) && @is_callable(array($GLOBALS['error'], 'debugEnd'))) {
			$GLOBALS['error']->debugEnd();
		}
	}
}
?>