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
 * Class BannerStatPanel
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
        \System::loadLanguageFile('tl_dlstats_stat_export'); //TODO
        
        if (\Input::post('act',true)=='export') //action DLStats Export
        {
            $this->generateExport(); //TODO
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
        //TODO textvariablen
        $pre = '
<div class="tl_panel">
<form enctype="application/x-www-form-urlencoded" method="post" class="tl_form" id="tl_article" action="contao/main.php?do=dlstats">
<input type="hidden" name="REQUEST_TOKEN" value="'.REQUEST_TOKEN.'">
<input type="hidden" name="act" value="export">    
';

        $text = '
    <div class="tl_limit" style="float:left; padding-left: 22px;">
            <strong>Statistik Export:</strong> 
    </div>
';
        
        $year = '
    <div class="tl_limit tl_subpanel">
        <strong>Jahr:</strong> 
        <div class="styled_select tl_select">
          <span>Alle Jahre</span>
        </div>
        <select class="tl_select" name="dlstats_export_year" style="opacity: 0;">
          <option value="all">Alle Jahre</option>
          <option value="2014">2014</option>
          <option value="2013">2013</option>
          <option value="2012">2012</option>
        </select> 
    </div>
';
        $month = '
    <div class="tl_limit tl_subpanel">
        <strong>Monat:</strong> 
        <div class="styled_select tl_select">
          <span>Alle Monate</span>
        </div>
        <select class="tl_select" name="dlstats_export_month" style="opacity: 0;">
          <option value="all">Alle Monate</option>
          <option value="12">12</option>
          <option value="11">11</option>
          <option value="10">10</option>
        </select> 
    </div>
';
        
        $submit = '
    <div class="tl_subpanel">
        <input type="submit" value="Export" class="tl_submit" id="save" name="dlstats_export_submit">
    </div>
';
        
        $suf = '
  <div class="clear"></div>
</form>
</div>';
        
        
        return $pre.$text.$submit.$month.$year.$suf;
    } // getPanelLine
    
    
    protected function generateExport()
    {
        return ;
    }
} // class
