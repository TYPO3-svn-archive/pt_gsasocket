<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2005-2008 Rainer Kuhn (kuhn@punkt.de)
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
* 
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/** 
 * Accessor class for GSA data stored in text files (*.ini, *.vor).
 * This class requires all relevant text files of type ".vor" to be imported into the default GSA MySQL-DB (see class comment for details)
 * 
 * $Id: class.tx_ptgsasocket_textfileDataAccessor.php,v 1.12 2008/11/18 16:43:37 ry37 Exp $
 *
 * @author  Rainer Kuhn <kuhn@punkt.de>
 * @since   2005-12-16
 */ 
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 */



/**
 * Inclusion of external resources
 */
require_once t3lib_extMgm::extPath('pt_gsasocket').'res/class.tx_ptgsasocket_gsaDbAccessor.php'; // parent class for all GSA database accessor classes
require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_iSingleton.php'; // interface for Singleton design pattern
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_debug.php'; // debugging class with trace() function
require_once t3lib_extMgm::extPath('pt_tools').'res/objects/class.tx_pttools_exception.php'; // general exception class
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_div.php'; // general helper library class



/**
 * Accessor class for GSA data stored in text files of type ".ini" and ".vor" (based on the ERP's text files structure). 
 * 
 * This class requires all relevant text files of type ".vor" to be imported into the default GSA MySQL-DB with the table name scheme "vor_<name of the ERP .vor text file in lower case letters>" and with only one DB field each, named as "<.vor file name in uppercase letters>". This may be done i.e. by cronjob.
 *
 * @author      Rainer Kuhn <kuhn@punkt.de>
 * @since       2005-12-16
 * @package     TYPO3
 * @subpackage  tx_ptgsasocket
 */
class tx_ptgsasocket_textfileDataAccessor extends tx_ptgsasocket_gsaDbAccessor implements tx_pttools_iSingleton {
    
    /**
     * Properties
     */
    private static $uniqueInstance = NULL; // (tx_ptgsasocket_textfileDataAccessor object) Singleton unique instance
    
    
    
    /***************************************************************************
     *   CONSTRUCTOR & OBJECT HANDLING METHODS
     **************************************************************************/
    
    /**
     * Returns a unique instance (Singleton) of the object. Use this method instead of the private/protected class constructor.
     *
     * @param   void
     * @return  tx_ptgsasocket_textfileDataAccessor      unique instance of the object (Singleton) 
     * @global     
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2005-12-16
     */
    public static function getInstance() {
        
        if (self::$uniqueInstance === NULL) {
            $className = __CLASS__;
            self::$uniqueInstance = new $className;
        }
        
        return self::$uniqueInstance;
        
    }
    
    
    
    /***************************************************************************
     *   SECTION: METHODS FOR .INI-FILES
     **************************************************************************/
    
    /**
     * Returns an 2-D array containing all ERP preferences from a ERP "*.ini" text file  
     * 
     * This method requires the GSA server directory to be configured properly in TYPO3's extension manager (value "dbGSAserverDir").
     * 
     * @param   string      (optional) file name of the GSA ini file to use (default is GSA main ini file 'Datei.ini')
     * @return  array       2-D array containing all ERP preferences from the ERP text file "Datei.ini" (1st level = sections, 2nd level = keys)
     * @throws  tx_pttools_exception   if no extension configuration data found
     * @throws  tx_pttools_exception   if extension config value "pathGSAserverDir" is empty
     * @throws  tx_pttools_exception   if the text file "Datei.ini" cannot be opened
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2006-01-02
     */
    public function getIniPreferencesArray($iniFileName='Datei.ini') {
        
        // get basic extension configuration incl. GSA server directory data from localconf.php (extension configuration in Extension Manager)
        $baseConfArr = tx_pttools_div::returnExtConfArray('pt_gsasocket');
        
        if (empty($baseConfArr['pathGSAserverDir'])) {
            throw new tx_pttools_exception('Extension configuration for GS-AUFTRAG server directory not found', 2);
        }
        if (!($fileHandle = @fopen($baseConfArr['pathGSAserverDir'].$iniFileName, 'r'))) {
            throw new tx_pttools_exception('Cannot open GS-Auftrag ini file', 2, 'ERROR: Cannot open file for reading: '.$baseConfArr['pathGSAserverDir'].$iniFileName);
        }
        
        // create twodimensional ini preferences array from file
        $iniPrefArr = array();
        $sectionKey = 'none';
        while (!feof($fileHandle)) {
            // get line with combined carriage return and line feed (=Windows line end) stripped
            $buffer = str_replace(chr(13).chr(10), '', fgets($fileHandle));
            // process section line
            if (substr($buffer, 0, 1) == '[') {
                $sectionKey = substr($buffer, 1, (strlen($buffer)-2));
                $iniPrefArr[$sectionKey] = array();
            // process key/value line
            } elseif (!empty($buffer)) {
                $lineArr = explode('=', $buffer);
                $iniPrefArr[$sectionKey][$lineArr[0]] = substr($lineArr[1], 0, (strlen($lineArr[1])));
            }
        }
        fclose($fileHandle); 
        
        trace($iniPrefArr, 0, '$iniPrefArr');
        return $iniPrefArr;
        
    }
    
    /**
     * Returns the value of a specified section/key pair of an GSA "*.ini" text file  
     * 
     * @param   string      section name
     * @param   string      key name
     * @param   string      (optional) file name of the GSA ini file to use (default is GSA main ini file 'Datei.ini')
     * @return  string      the value of the specified section/key pair
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2006-01-03
     */
    public function getIniPrefValue($section, $key, $iniFileName='Datei.ini') {
        
        $iniPrefArr = $this->getIniPreferencesArray($iniFileName);
        return $iniPrefArr[$section][$key];
        
    }
    
    
    
    /***************************************************************************
     *   SECTION: METHODS FOR .VOR-FILES
     **************************************************************************/
     
    /**
     * Returns an array containing all salutations from the GSA text file "Anrede.vor" (imported into GSA MySQL-DB) 
     * 
     * This method requires the GSA file "Anrede.vor" to be imported into GSA MySQL-DB as "vor_anrede" with only one DB field named "ANREDE".
     * 
     * @param   void
     * @return  array       2-D array containing all GSA salutations
     * @throws  tx_pttools_exception   if the query fails/returns false
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2005-12-16
     */
    public function selectSalutations() {
        
        // query preparation
        $select  = 'ANREDE';
        $from    = $this->getTableName('vor_anrede');
        $where   = '';
        $groupBy = '';
        $orderBy = '';
        $limit   = '';
        
        // exec query using TYPO3 DB API
        $res = $this->gsaDbObj->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
        if ($res == false) {
            throw new tx_pttools_exception('Query failed', 1, $this->gsaDbObj->sql_error());
        }
        
        $a_result = array();
        while($a_row = $this->gsaDbObj->sql_fetch_assoc($res)) {
            // if enabled, do charset conversion of all non-binary string data 
            if ($this->charsetConvEnabled == 1) {
                $a_row = tx_pttools_div::iconvArray($a_row, $this->gsaCharset, $this->siteCharset);
            }
            $a_result[] = $a_row;
        }
        $this->gsaDbObj->sql_free_result($res);
        
        trace($a_result);
        return $a_result;
        
    }
    
    /**
     * Returns an array containing all titles from the GSA text file "Titel.vor" (imported into GSA MySQL-DB)
     * 
     * This method requires the GSA file "Titel.vor" to be imported into GSA MySQL-DB as "vor_titel" with only one DB field named "TITEL".
     * 
     * @param   void
     * @return  array       2-D array containing all GSA salutations titles
     * @throws  tx_pttools_exception   if the query fails/returns false
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2005-12-16
     */
    public function selectTitles() {
        
        // query preparation
        $select  = 'TITEL';
        $from    = $this->getTableName('vor_titel');
        $where   = '';
        $groupBy = '';
        $orderBy = '';
        $limit   = '';
        
        // exec query using TYPO3 DB API
        $res = $this->gsaDbObj->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
        if ($res == false) {
            throw new tx_pttools_exception('Query failed', 1, $this->gsaDbObj->sql_error());
        }
        
        $a_result = array();
        while($a_row = $this->gsaDbObj->sql_fetch_assoc($res)) {
            // if enabled, do charset conversion of all non-binary string data 
            if ($this->charsetConvEnabled == 1) {
                $a_row = tx_pttools_div::iconvArray($a_row, $this->gsaCharset, $this->siteCharset);
            }
            $a_result[] = $a_row;
        }
        $this->gsaDbObj->sql_free_result($res);
        
        trace($a_result);
        return $a_result;
        
    }
    
    /**
     * Returns an array containing all methods of payment from the GSA text file "Zahlart.vor" (imported into GSA MySQL-DB)
     * 
     * This method requires the GSA file "Zahlart.vor" to be imported into GSA MySQL-DB as "vor_zahlart" with only one DB field named "ZAHLART".
     * 
     * @param   void
     * @return  array       2-D array containing all GSA methods of payment
     * @throws  tx_pttools_exception   if the query fails/returns false
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2005-12-30
     */
    public function selectPaymentMethods() {
        
        // query preparation
        $select  = 'ZAHLART';
        $from    = $this->getTableName('vor_zahlart');
        $where   = '';
        $groupBy = '';
        $orderBy = '';
        $limit   = '';
        
        // exec query using TYPO3 DB API
        $res = $this->gsaDbObj->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
        if ($res == false) {
            throw new tx_pttools_exception('Query failed', 1, $this->gsaDbObj->sql_error());
        }
        
        $a_result = array();
        while($a_row = $this->gsaDbObj->sql_fetch_assoc($res)) {
            // if enabled, do charset conversion of all non-binary string data 
            if ($this->charsetConvEnabled == 1) {
                $a_row = tx_pttools_div::iconvArray($a_row, $this->gsaCharset, $this->siteCharset);
            }
            $a_result[] = $a_row;
        }
        $this->gsaDbObj->sql_free_result($res);
        
        trace($a_result);
        return $a_result;
        
    }
    
    
    
} // end class



/*******************************************************************************
 *   TYPO3 XCLASS INCLUSION (for class extension/overriding)
 ******************************************************************************/
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsasocket/res/class.tx_ptgsasocket_textfileDataAccessor.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsasocket/res/class.tx_ptgsasocket_textfileDataAccessor.php']);
}

?>