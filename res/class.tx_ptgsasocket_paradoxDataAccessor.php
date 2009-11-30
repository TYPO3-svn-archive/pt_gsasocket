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
 * Accessor class for GSA data stored in Borland Paradox DB files.
 * This class requires all relevant GSA Paradox DB files to be imported into the default GSA MySQL-DB. This may be done i.e. by cronjob using PXTOOLS (see http://jan.kneschke.de/projects/pxtools/).
 * 
 * $Id: class.tx_ptgsasocket_paradoxDataAccessor.php,v 1.11 2008/11/18 16:43:37 ry37 Exp $
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
 * Accessor class for ERP data stored in Borland Paradox DB files and imported into GSA MySQL-DB (based on ERP's paradox database structure)
 * 
 * This class requires all relevant ERP Paradox DB files to be imported into the default GSA MySQL-DB with the table name scheme "px_<name of the GSA paradox .DB file in lower case letters>". This may be done i.e. by cronjob using PXTOOLS (see http://jan.kneschke.de/projects/pxtools/).
 *
 * @author      Rainer Kuhn <kuhn@punkt.de>
 * @since       2005-12-16
 * @package     TYPO3
 * @subpackage  tx_ptgsasocket
 */
class tx_ptgsasocket_paradoxDataAccessor extends tx_ptgsasocket_gsaDbAccessor implements tx_pttools_iSingleton {
    
    /**
     * Properties
     */
    private static $uniqueInstance = NULL; // (tx_ptgsasocket_paradoxDataAccessor object) Singleton unique instance
    
    
    
    /***************************************************************************
     *   CONSTRUCTOR & OBJECT HANDLING METHODS
     **************************************************************************/
    
    /**
     * Returns a unique instance (Singleton) of the object. Use this method instead of the private/protected class constructor.
     *
     * @param   void
     * @return  tx_ptgsasocket_paradoxDataAccessor      unique instance of the object (Singleton) 
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
     *   GENERAL METHODS
     **************************************************************************/
    
    /**
     * Returns an 2-D array containing the data of all countries GSA paradox file "laender.DB"
     * 
     * This method requires the paradox file "laender.DB" to be imported into GSA MySQL-DB as "px_laender". This may be done i.e. by cronjob using PXTOOLS (see http://jan.kneschke.de/projects/pxtools/).
     * 
     * @param   void
     * @return  array       2-D array containing the data of all GSA countries
     * @throws  tx_pttools_exception   if the query fails/returns false
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2005-12-16
     */
    public function selectCountries() {
        
        // query preparation
        $select  = 'NUMMER, KUERZEL, NAME, WEBLKZ, VWREIN, VWRAUS';
        $from    = $this->getTableName('px_laender');
        $where   = '';
        $groupBy = '';
        $orderBy = 'NAME';
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
     * Returns the name of a country specified by post code abbrevation (GSA: KUERZEL) from the GSA paradox file "laender.DB"
     *
     * This method requires the paradox file "laender.DB" to be imported into GSA MySQL-DB as "px_laender". This may be done i.e. by cronjob using PXTOOLS (see http://jan.kneschke.de/projects/pxtools/).
     * 
     * @param   string      post code abbrevation (GSA: KUERZEL) of the requested country
     * @return  string      
     * @throws  tx_pttools_exception   if the query fails/returns false
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2005-12-16
     */
    public function selectCountryName($countryAbbr) {
        
        // query preparation
        $select  = 'NAME';
        $from    = $this->getTableName('px_laender');
        $where   = 'KUERZEL LIKE '.$GLOBALS['TYPO3_DB']->fullQuoteStr(strtoupper($countryAbbr), $from);
        $groupBy = '';
        $orderBy = '';
        $limit   = '';
        
        // exec query using TYPO3 DB API
        $res = $this->gsaDbObj->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
        if ($res == false) {
            throw new tx_pttools_exception('Query failed', 1, $this->gsaDbObj->sql_error());
        }
        $a_row = $this->gsaDbObj->sql_fetch_assoc($res);
        $this->gsaDbObj->sql_free_result($res);
        
        // if enabled, do charset conversion of all non-binary string data 
        if ($this->charsetConvEnabled == 1) {
            $a_row = tx_pttools_div::iconvArray($a_row, $this->gsaCharset, $this->siteCharset);
        }
        
        trace($a_row[$select]); 
        return $a_row[$select];
        
    } 
    
    /**
     * Returns the post code abbrevation (GSA: KUERZEL) of a country specified by internet toplevel domain from the GSA paradox file "laender.DB"
     *
     * This method requires the paradox file "laender.DB" to be imported into GSA MySQL-DB as "px_laender". This may be done i.e. by cronjob using PXTOOLS (see http://jan.kneschke.de/projects/pxtools/).
     * 
     * @param   string      internet toplevel domain
     * @return  string      post code abbrevation (GSA: KUERZEL) of the requested country
     * @throws  tx_pttools_exception   if the query fails/returns false
     * @author  Wolfgang Zenker <zenker@punkt.de>
     * @since   2006-05-15
     */
    public function getAbbrByTld($tld) {
        
        // query preparation
        $select  = 'KUERZEL';
        $from    = $this->getTableName('px_laender');
        $where   = 'WEBLKZ = '.$GLOBALS['TYPO3_DB']->fullQuoteStr(strtoupper($tld), $from);
        $groupBy = '';
        $orderBy = '';
        $limit   = '';
        
        // exec query using TYPO3 DB API
        $res = $this->gsaDbObj->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
        if ($res == false) {
            throw new tx_pttools_exception('Query failed', 1, $this->gsaDbObj->sql_error());
        }
        $a_row = $this->gsaDbObj->sql_fetch_assoc($res);
        $this->gsaDbObj->sql_free_result($res);
        
        // if enabled, do charset conversion of all non-binary string data 
        if ($this->charsetConvEnabled == 1) {
            $a_row = tx_pttools_div::iconvArray($a_row, $this->gsaCharset, $this->siteCharset);
        }
        
        trace($a_row[$select]); 
        return $a_row[$select];
        
    } 
    
    /**
     * Returns the internet toplevel domain of a country specified by post code abbrevation (GSA: KUERZEL) from the GSA paradox file "laender.DB"
     *
     * This method requires the paradox file "laender.DB" to be imported into GSA MySQL-DB as "px_laender". This may be done i.e. by cronjob using PXTOOLS (see http://jan.kneschke.de/projects/pxtools/).
     * 
     * @param   string      post code abbrevation (GSA: KUERZEL) of the requested country
     * @return  string      Toplevel domain
     * @throws  tx_pttools_exception   if the query fails/returns false
     * @author  Wolfgang Zenker <zenker@punkt.de>
     * @since   2006-05-15
     */
    public function getTldByAbbr($countryAbbr) {
        
        // query preparation
        $select  = 'WEBLKZ';
        $from    = $this->getTableName('px_laender');
        $where   = 'KUERZEL = '.$GLOBALS['TYPO3_DB']->fullQuoteStr(strtoupper($countryAbbr), $from);
        $groupBy = '';
        $orderBy = '';
        $limit   = '';
        
        // exec query using TYPO3 DB API
        $res = $this->gsaDbObj->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
        if ($res == false) {
            throw new tx_pttools_exception('Query failed', 1, $this->gsaDbObj->sql_error());
        }
        $a_row = $this->gsaDbObj->sql_fetch_assoc($res);
        $this->gsaDbObj->sql_free_result($res);
        
        // if enabled, do charset conversion of all non-binary string data 
        if ($this->charsetConvEnabled == 1) {
            $a_row = tx_pttools_div::iconvArray($a_row, $this->gsaCharset, $this->siteCharset);
        }
        
        trace($a_row[$select]); 
        return $a_row[$select];
        
    }
    
    
    
} // end class



/*******************************************************************************
 *   TYPO3 XCLASS INCLUSION (for class extension/overriding)
 ******************************************************************************/
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsasocket/res/class.tx_ptgsasocket_paradoxDataAccessor.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsasocket/res/class.tx_ptgsasocket_paradoxDataAccessor.php']);
}

?>
