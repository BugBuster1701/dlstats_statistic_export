<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Dlstats_statistic_export
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'BugBuster',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'BugBuster\DLStats\Stat\Export\DLStatsStatPanel'  => 'system/modules/dlstats_statistic_export/classes/DLStatsStatPanel.php',
	'BugBuster\DLStats\Stat\Export\DLStatsStatExport' => 'system/modules/dlstats_statistic_export/classes/DLStatsStatExport.php',
));