<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2012 Kasper Skårhøj (kasperYYYY@typo3.com)
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
 * This is the MAIN DOCUMENT of the TypoScript driven standard frontend.
 * Basically this is the "index.php" script which all requests for TYPO3
 * delivered pages goes to in the frontend (the website)
 *
 * @author René Fritz <r.fritz@colorcube.de>
 */
//xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
function xhprof_end() {
	// Profiling stoppen
	$xhprof_data = xhprof_disable();

	// einbinden der XHProf-GUI Dateien
	include_once "xhprof_lib/utils/xhprof_lib.php";
	include_once "xhprof_lib/utils/xhprof_runs.php";

	$xhprof_runs = new XHProfRuns_Default();

	// speichern der Daten - gibt die dazugehörige ID zurück
	$run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_foo");

	// Link zur GUI-Seite
	echo '<a href="http://xhprof_html.dev.tom/index.php?run=' . $run_id . '&source=xhprof_foo">Profiling Data</a>';
	die();

}

require 'typo3/sysext/TYPO3.CMS.Core/Classes/Core/Bootstrap.php';

call_user_func(function() {
	$bootstrap = new \TYPO3\CMS\Core\Core\Bootstrap('Production');
	$bootstrap->run();
});

?>
