<?php 
/**
 * Contao Open Source CMS, Copyright (C) 2005-2014 Leo Feyer
 * 
 * Modul DLStats Statistic Export Config - Backend
 *
 *
 * @copyright	Glen Langer 2014 <http://www.contao.glen-langer.de>
 * @author      Glen Langer (BugBuster)
 * @package     DLStatsStatisticExport 
 * @license     LGPL 
 * @filesource
 */

define('DLSTATS_STAT_EXPORT_VERSION', '1.0');
define('DLSTATS_STAT_EXPORT_BUILD'  , '0');

/**
 * -------------------------------------------------------------------------
 * DLSTATS HOOKS
 * -------------------------------------------------------------------------
 */
$GLOBALS['TL_DLSTATS_HOOKS']['addStatisticPanelLine'][] = array('DLStats\Stat\Export\DLStatsStatPanel', 'getPanelLine');


