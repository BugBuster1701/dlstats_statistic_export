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
 * Class DLStatsStatExport
 *
 * @copyright	Glen Langer 2014 <http://www.contao.glen-langer.de>
 * @author      Glen Langer (BugBuster)
 * @package     DLStatsStatisticExport 
 */
class DLStatsStatExport extends \System
{
    protected $year   = 0;
    protected $month  = 0;
    protected $format = 'xlsx';
    protected $BrowserAgent ='NOIE';
    protected $export_from  = 0;
    protected $export_to    = 0;
    
    /**
     */
    function __construct()
    {
        parent::__construct();
        $this->year   = \Input::post('dlstats_export_year'  ,true);
        $this->month  = \Input::post('dlstats_export_month' ,true);
        $this->format = \Input::post('dlstats_export_format',true);
        //IE or other?
        $ua = \Environment::get('agent')->shorty;
        if ($ua == 'ie')
        {
            $this->BrowserAgent = 'IE';
        }
    }
    
    public function run()
    {
        $this->setExportPeriod();
        
        switch ($this->format) 
        {
            case 'xlsx':
                $this->exportXLSX();
                break;
            case 'ods':
                $this->exportODS();
                break;
            case 'csv':
                $this->exportCSV();
                break;
        	default:
                break;
        }
        return;
    }
    
    /**
     * Set Export Period In Timestamp Values
     * $this->export_from
     * $this->export_to
     * 
     */
    protected function setExportPeriod()
    {
        if ($this->year != 'all') 
        {
        	$this->export_from = mktime(0 ,0 ,0 ,1 ,1 ,$this->year);
        	$this->export_to   = mktime(23,59,59,12,31,$this->year);
        }
        //Default zur Sicherheit
        if ($this->year == 'all' && $this->month != 'all') 
        {
            $this->year = date('Y');
        }
        //wenn Monat dann muss auch Jahr gesetzt sein
        if ($this->month != 'all' && $this->year != 'all') 
        {
            $this->export_from = mktime(0 ,0 ,0 ,$this->month  ,1 ,$this->year);
        	$this->export_to   = mktime(23,59,59,$this->month+1,0 ,$this->year);
        }
        return ;
    }
    
    protected function exportXLSX()
    {
        return ;
    }
    
    protected function exportODS()
    {
        return ;
    }
    
    protected function exportCSV()
    {
        $where = 'WHERE 1';
        if ($this->export_from != 0) 
        {
        	$where = ' WHERE det.`tstamp` BETWEEN '.$this->export_from.' AND '.$this->export_to.' ';
        }
        $objStatistic = \Database::getInstance()
                            ->prepare("SELECT 
                                            dl.`filename`,
                                            det.`tstamp`, 
                                            det.`ip`, 
                                            det.`username`, 
                                            det.`domain`, 
                                            det.`page_host`, 
                                            det.`page_id`,
                                            det.`browser_lang`
                                       FROM 
                                            `tl_dlstats` dl
                                       INNER JOIN 
                                            `tl_dlstatdets` det on dl.`id` = det.`pid`
                                       ".$where."
                                       ORDER BY 
                                            det.`tstamp`, 
                                            dl.`filename`
                                        ")
                            ->execute();
        
        $objPHPExcel = new \PHPExcel();
        /*$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                    ->setLastModifiedBy("Maarten Balliauw")
                    ->setTitle("Office 2007 XLSX Test Document")
                    ->setSubject("Office 2007 XLSX Test Document")
                    ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                    ->setKeywords("office 2007 openxml php")
                    ->setCategory("Test result file");
        */
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Filename');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Date');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'IP');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Username');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Domain');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Page Host');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Page Alias');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Browser Language');
        
        $row = 1;
        while ($objStatistic->next())
        {
            $row++;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $objStatistic->filename);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, date($GLOBALS['TL_CONFIG']['datimFormat'], $objStatistic->tstamp));
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $objStatistic->ip);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $objStatistic->username);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $objStatistic->domain);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $objStatistic->page_host);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$row, $this->getPageAliasById($objStatistic->page_id)); // Alias
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$row, $objStatistic->browser_lang);
        }
        
        //EXPORT
        header('Content-Type: text/csv; charset=' . $GLOBALS['TL_CONFIG']['characterSet']);
        header('Content-Disposition: attachment;filename="dlstatistic-export.utf8.csv"');
        header('Cache-Control: max-age=0');
        if ($this->BrowserAgent == 'IE')
        {
            // If you're serving to IE 9, then the following may be needed
            //header('Cache-Control: max-age=1');
        
            // If you're serving to IE over SSL, then the following may be needed
            header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0
        }
        
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV')
                    ->setDelimiter(',')
                    ->setEnclosure('"')
                    ->setLineEnding("\r\n")
                    ->setSheetIndex(0);
        $objWriter->save('php://output');
        $objWriter = null;
        unset($objWriter);
        $objPHPExcel = null;
        unset($objPHPExcel);
        exit;
        return ;
    }
    
    protected function getPageAliasById($page_id)
    {
        if ((int)$page_id == 0)
        {
            return '';
        }
        $objAlias = \Database::getInstance()->prepare("SELECT
                                                         `alias`
                                                       FROM
                                                         `tl_page`
                                                       WHERE
                                                         `id`=?")
                                             ->limit(1)
                                             ->execute($page_id);
        $intRows = $objAlias->numRows;
        if ($intRows>0)
        {
            return $objAlias->alias;
        }
        else
        {
            return $page_id;
        }
    }
}

/**
 	// Check if zip class exists
// if (!class_exists($zipClass, FALSE)) {
// throw new PHPExcel_Reader_Exception($zipClass . " library is not enabled");
// }
 This allows the writing of Excel2007 files, even without ZipArchive enabled (it does require zlib), or when php_zip is one of the buggy PHP 5.2.6 or 5.2.8 versions
It can be enabled using PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);

 *  
*/