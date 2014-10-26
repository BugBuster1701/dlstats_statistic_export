<?php

/**
 * Contao Open Source CMS, Copyright (C) 2005-2014 Leo Feyer
 * 
 * Modul DLStats Statistic Export - Panel Hook 
 * 
 * @copyright	Glen Langer 2014 <http://www.contao.glen-langer.de>
 * @author      Glen Langer (BugBuster)
 * @package     DLStatsStatisticExport 
 * @license     LGPL 
 * @filesource
 */

/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace BugBuster\DLStats\Stat\Export; 

/**
 * Class DLStatsStatPanel
 *
 * @copyright	Glen Langer 2014 <http://www.contao.glen-langer.de>
 * @author      Glen Langer (BugBuster)
 * @package     DLStatsStatisticExport 
 */
class DLStatsStatPanel extends \System
{
   /**
    * Current object instance
    * @var object
    */
    protected static $instance = null;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        \System::loadLanguageFile('tl_dlstats_stat_export'); 
        
        if (\Input::post('act',true)=='export') //action DLStats Export
        {
            $this->generateExport(); 
        }
    }
    
    /**
     * Return the current object instance (Singleton)
     * @return BannerStatPanel
     */
    public static function getInstance()
    {
        if (self::$instance == null)
        {
            self::$instance = new DLStatsStatPanel();
        }
    
        return self::$instance;
    }

    /**
     * Hook: addStatisticPanelLine 
     * 
     * @param insteger    Category ID
     * @return string
     */
    public function getPanelLine()
    {
        $pre = '
<style type="text/css">
/* <![CDATA[ */
.unix.chrome input.tl_submit {
    padding: 2px 12px 2px 13px;
}
.win.chrome input.tl_submit {
    padding: 3px 12px 3px 13px;
}
.opera input.tl_submit {
    padding: 2px 12px 2px 13px;
}
.win.ie input.tl_submit {
    padding: 3px 12px 3px 13px;
}
            
/* ]]> */
</style>
<div class="tl_panel">
<form enctype="application/x-www-form-urlencoded" method="post" class="tl_form" id="tl_article" action="contao/main.php?do=dlstats">
<input type="hidden" name="REQUEST_TOKEN" value="'.REQUEST_TOKEN.'">
<input type="hidden" name="act" value="export">    
';

        $text = '
    <div class="tl_limit" style="float:left; padding-left: 22px; line-height: 21px;">
            <strong>'.$GLOBALS['TL_LANG']['tl_dlstats_stat_export']['statistics_export'].':</strong> 
    </div>
';
        
        $objYear = \Database::getInstance()->query("SELECT distinct(FROM_UNIXTIME(`tstamp`,'%Y')) AS Year FROM `tl_dlstatdets` WHERE 1 ORDER BY Year DESC");
        $year = '
    <div class="tl_limit tl_subpanel">
        <strong>'.$GLOBALS['TL_LANG']['tl_dlstats_stat_export']['year'].':</strong> 
        <div class="styled_select tl_select">
          <span>'.$GLOBALS['TL_LANG']['tl_dlstats_stat_export']['all_years'].'</span>
        </div>
        <select class="tl_select" name="dlstats_export_year" style="opacity: 0;">
          <option value="all">'.$GLOBALS['TL_LANG']['tl_dlstats_stat_export']['all_years'].'</option>
';
        while ($objYear->next())
        {
        	$year .= '<option value="'.$objYear->Year.'">'.$objYear->Year.'</option>';
        }
        $year .='
        </select> 
    </div>
';
        $month = '
    <div class="tl_limit tl_subpanel">
        <strong>'.$GLOBALS['TL_LANG']['tl_dlstats_stat_export']['month'].':</strong> 
        <div class="styled_select tl_select">
          <span>'.$GLOBALS['TL_LANG']['tl_dlstats_stat_export']['all_months'].'</span>
        </div>
        <select class="tl_select" name="dlstats_export_month" style="opacity: 0;">
          <option value="all">'.$GLOBALS['TL_LANG']['tl_dlstats_stat_export']['all_months'].'</option>
';
        for ($i = 1; $i < 13; $i++) 
        {
        	$month .= '<option value="'.$i.'">'.$i.'</option>';
        }

        $month .= '
        </select> 
    </div>
';
        $format = '
    <div class="tl_limit tl_subpanel">
        <strong>'.$GLOBALS['TL_LANG']['tl_dlstats_stat_export']['format'].':</strong> 
        <div class="styled_select tl_select">
          <span>'.$GLOBALS['TL_LANG']['tl_dlstats_stat_export']['excel'].'</span>
        </div>
        <select class="tl_select" name="dlstats_export_format" style="opacity: 0;">
          <option value="xlsx">'.$GLOBALS['TL_LANG']['tl_dlstats_stat_export']['excel'].'</option>
          <option value="csv" >'.$GLOBALS['TL_LANG']['tl_dlstats_stat_export']['csv'].'</option>
        </select> 
    </div>
';
        //XLSX needs the library php_zip
        if (!class_exists('ZipArchive', FALSE))
        {
            $format = '
    <div class="tl_limit tl_subpanel">
        <strong>'.$GLOBALS['TL_LANG']['tl_dlstats_stat_export']['format'].':</strong> 
        <div class="styled_select tl_select">
          <span>'.$GLOBALS['TL_LANG']['tl_dlstats_stat_export']['csv'].'</span>
        </div>
        <select class="tl_select" name="dlstats_export_format" style="opacity: 0;">
          <option value="csv" >'.$GLOBALS['TL_LANG']['tl_dlstats_stat_export']['csv'].'</option>
        </select> 
    </div>
';            
        }
     
        $submit = '
    <div class="tl_subpanel" style="padding-right: 14px;">
        <input type="submit" value="'.$GLOBALS['TL_LANG']['tl_dlstats_stat_export']['export'].'" class="tl_submit" id="save" name="dlstats_export_submit">
    </div>
';
        
        $suf = '
  <div class="clear"></div>
</form>
</div>';
        
        
        return $pre.$text.$submit.$format.$month.$year.$suf;
    } // getPanelLine
    
    
    protected function generateExport()
    {
        $export = new \DLStats\Stat\Export\DLStatsStatExport;
        return $export->run();
    }
} // class
