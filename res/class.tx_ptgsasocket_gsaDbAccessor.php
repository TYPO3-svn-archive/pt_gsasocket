<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2005-2008 Rainer Kuhn (kuhn@punkt.de), Wolfgang Zenker <zenker@punkt.de>
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
 * Parent abstract class for all GSA database accessor classes, part of the 'pt_gsasocket' extension
 *
 * $Id: class.tx_ptgsasocket_gsaDbAccessor.php,v 1.35 2008/11/18 16:43:37 ry37 Exp $
 *
 * @author  Rainer Kuhn <kuhn@punkt.de>, Wolfgang Zenker <zenker@punkt.de>
 * @since   2005-11-10
 */ 
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 */



/**
 * Inclusion of extension specific resources
 */
require_once t3lib_extMgm::extPath('pt_gsasocket').'res/class.tx_ptgsasocket_gsaDbConnector.php'; // GSA database connection class

/**
 * Inclusion of external resources
 */
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_debug.php'; // debugging class with trace() function
require_once t3lib_extMgm::extPath('pt_tools').'res/objects/class.tx_pttools_exception.php'; // general exception class
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_assert.php';



/**
 * Parent abstract class for all GSA database accessor classes (uses modified Singleton design pattern)
 *
 * @author      Rainer Kuhn <kuhn@punkt.de>, Wolfgang Zenker <zenker@punkt.de>
 * @since       2005-11-10
 * @package     TYPO3
 * @subpackage  tx_ptgsashop
 */
abstract class tx_ptgsasocket_gsaDbAccessor implements tx_pttools_iSingleton {
    
    
    
    /***************************************************************************
     *   PROPERTIES
     **************************************************************************/
    
    /**
     * @var tx_ptgsasocket_gsaDbAccessor   Singleton unique instance to use in inheriting class
     */
    #private static $uniqueInstance = NULL;
    
    /**
     * @var tx_ptgsasocket_gsaDbConnector    GSA database object (extended from TYPO3 database object t3lib_db)
     */
    protected $gsaDbObj = NULL;
    
    /**
     * @var boolean     flag whether the data retrieved from the GSA databse should be converted to another charset
     */
    protected $charsetConvEnabled = false;
    
    /**
     * @var string      Charset used by the GSA database (this property will not set if $charsetConvEnabled is set to false)
     */
    protected $gsaCharset = '';
    
    /**
     * @var string      Charset used by the website (TYPO3 FE / BE) (this property will not set if $charsetConvEnabled is set to false)
     */
    protected $siteCharset = '';
    
    /**
     * @var boolean     flag whether the GSA database should be used within the TYPO3 database
     */
    protected $useGsaTablesInTypo3Database = false; 
    
    
    
    /***************************************************************************
     *   CLASS CONSTANTS
     **************************************************************************/
    
    const GSA_IN_T3DB_PREFIX = 'tx_ptgsaminidb_'; // (string) prefix for GSA database tables used within the TYPO3 database (to be used if appropriate database configuration is set in Extension Manager)
    
    const ERFARTLIST = 'AN,AU,LI,RE,GU,ST,BE,BS,WE,MA,BA,SO,RA,RL,KA,WD';    // (string) possible fieldname-headings in table NUMMERN (AF excluded because of datetime)
    const WN_ENDLOS  = 0;   // (integer) unlimited sequential number
    const WN_TAG     = 1;   // (integer) daily sequential number
    const WN_MONAT   = 2;   // (integer) monthly sequential number
    const WN_JAHR    = 3;   // (integer) yearly sequential number
    
    
    
    
    /***************************************************************************
     *   ABSTRACT METHODS
     **************************************************************************/
     
    /**
     * To be implemented as public static method in inheriting class: Returns a unique instance of the inheriting class object, use this method instead of the private/protected class constructor.
     *
     * @return  tx_ptgsasocket_gsaDbAccessor      unique instance of the inheriting class object
     * @since   2005-11-10
     */
    #abstract public static function getInstance();  // implementation of getInstance() for child classes is now defined in interface tx_pttools_iSingleton, so no abstract method needed here anymore
    
    
    
    /***************************************************************************
     *   IMPLEMENTED METHODS
     **************************************************************************/
     
    /**
     * Class constructor: sets the database object for the accessor. Inherited to child class: must not be called directly in order to use getInstance() to get the unique instance of the object.
     *
     * @param   void
     * @return  void
     * @throws  tx_pttools_exceptionAssertion   if $this->gsaCharset or $this->siteCharset is not set with a non-empty string
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2005-11-10
     */
    protected function __construct() {
    
        trace('***** Creating new '.__CLASS__.' object. *****');
        
        $this->gsaDbObj = tx_ptgsasocket_gsaDbConnector::getInstance();
        
        // set charset conversion properties
        $extConfArr = tx_pttools_div::returnExtConfArray('pt_gsasocket'); // get basic extension configuration data from localconf.php (configurable in Extension Manager)
        if (isset($extConfArr['enableGsaCharsetConv'])) {
            $this->charsetConvEnabled = (boolean)$extConfArr['enableGsaCharsetConv'];
        }
        if ($this->charsetConvEnabled == true) {
            $this->gsaCharset = $extConfArr['gsaCharset'];
            $this->siteCharset = strtoupper(tx_pttools_div::getSiteCharsetEncoding());
            tx_pttools_assert::isNotEmptyString($this->gsaCharset, array('message' => 'Invalid value "'.$this->gsaCharset.'" set as GSA database charset in pt_gsasocket'));
            tx_pttools_assert::isNotEmptyString($this->siteCharset, array('message' => 'Invalid value "'.$this->siteCharset.'" for site charset pt_gsasocket'));
        }
        
        // set database handling
        if (isset($extConfArr['useGsaTablesInTypo3Database'])) {
            $this->useGsaTablesInTypo3Database = (boolean)$extConfArr['useGsaTablesInTypo3Database'];
        }
        
    }
    
    /**
     * Final method to prevent object cloning (using 'clone') of the inheriting class, in order to use only the singleton unique instance of the object.
     * 
     * @param   void
     * @return  void
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2005-11-10
     */
    public final function __clone() {
        
        trigger_error('Clone is not allowed for '.get_class($this).' (Singleton)', E_USER_ERROR);
        
    }
    
    /**
     * Returns the name of the GSA database table to use depending on individual configuration of pt_gsasocket
     * IMPORTANT: to be compatible with the new config option "useGsaTablesInTypo3Database", from ext. version 0.3.0 this method has to be used by all other GSA based extensions when accessing GSA database table names!
     *
     * @param   string      name of the table in original GSA database
     * @return  string      name of the table to use in individual installation (depending on configuration of pt_gsasocket)
     * @throws  tx_pttools_exception   if an mpty GSA table name is given as param
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2008-11-14 
     */
    protected function getTableName($originalGsaDbTableName) {
        
        tx_pttools_assert::isNotEmptyString($originalGsaDbTableName, array('message'=>'Empty GSA table name given.'));
        
        $tablePrefix = '';
        
        // prefix table only if extension is configured to GSA tables with the TYPO3 database *AND* if the given table name is not already prefixed (e.g. due to bnested calls)
        if ($this->useGsaTablesInTypo3Database == true && (stripos($originalGsaDbTableName, self::GSA_IN_T3DB_PREFIX) === false)) {
            $tablePrefix = self::GSA_IN_T3DB_PREFIX;
        }
        $tablename = $tablePrefix . $originalGsaDbTableName;
        
        return $tablename;
        
    }
    
    /**
     * Returns the next available UID to use for INSERT statements for a specified GSA database table
     * 
     * @param   string      GSA database table name to look up last used ID (GSA database field "SYNEWNUMBER.TABLENAME")
     * @param   integer     (optional) start value for a new SYNEWNUMBER record if there is no record yet for the given $tableName
     * @return  integer     next available UID to use for the specified database table
     * @author  Rainer Kuhn <kuhn@punkt.de>, Wolfgang Zenker <zenker@punkt.de>
     * @since   2005-11-17
     */
    protected function getNextId($tableName, $newRecordStartValue=0) {
        
        $lastId = $this->selectLastId($tableName);
        
        // if no last used id is returned, set it to 0 (or an optionally given starting value) and insert a new record in table SYNEWNUMBER
        if (is_null($lastId)) {
            $lastId = $newRecordStartValue;
            $this->insertSynewnumberRecord($tableName, $lastId);
        } 
        
        // create next id by increasing last used id per one and update SYNEWNUMBER with next id
        $nextId = $this->incrementSynewnumberRecord($tableName);
        
        // return next id
        return $nextId;
        
    }
    
    /**
     * Returns the next available sequential number for a specified *integer or double* field of the GSA database table NUMMERN. 
     * Use this method for non-"Erfassungsart" values only (e.g. POSZAEHLER, VORGANG, NUMMER) - use updateNextNumber() for all "Erfassungsart" values! 
     * 
     * @param   string      field name from the GSA system database table NUMMERN to retrieve its next available sequential number
     * @return  integer     next available sequential number to use for a specified field of the GSA database table NUMMERN
     * @throws  tx_pttools_exception   if retrieved value from DB table NUMMERN is not numeric
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2005-11-24
     */
    protected function getNextNumber($fieldName) {
        
        // get next number by using special function on NUMMERN
        $nextNo = $this->incrementNumberValue($fieldName);
        
        // return next number
        return $nextNo;
        
    }
 
    /**
     * Updates all relevant fields for the given "Erfassungsart" in table NUMMERN by resetting if neccessary, safely incrementing all required fields and returning the requested number
     * Use this method for all "Erfassungsart" values only - use getNextNumber() for all non-"Erfassungsart" values (e.g. POSZAEHLER, VORGANG, NUMMER)! 
     * 
     * @param   string      "Erfassungsart" (e.g. 'RE' for Invoice), must be in constant self::ERFARTLIST
     * @param   integer     (optional) designates requested number; see self::WN_* constants
     * @return  integer     next available number of requested type
     * @throws  tx_pttools_exception   if first parameter is not in list
     * @throws  tx_pttools_exception   if database operation fails
     * @author  Wolfgang Zenker <zenker@punkt.de>
     * @since   2007-06-25
     */
    public function updateNextNumber($erfart, $whatnum = self::WN_ENDLOS) {
        
        // check parameters
        if (! t3lib_div::inList(self::ERFARTLIST, $erfart)) {
            throw new tx_pttools_exception('Parameter error', 3, $erfart.' is not in list of allowed values');
        }
        
        switch ($whatnum) {
            case self::WN_ENDLOS:
            case self::WN_TAG:
            case self::WN_MONAT:
            case self::WN_JAHR:
                break;
            default:
                throw new tx_pttools_exception('Parameter error', 3, 'Illegal number type requested');
        }

        // get current values
        $select  =  $erfart.'ENDLOS AS endlos, '.
                    $erfart.'JAHR AS jahr, '.
                    $erfart.'MONAT AS monat, '.
                    $erfart.'TAG AS tag, '.
                    $erfart.'DATUM AS datum, '.
                    'CURDATE() AS today';
        $table   = $this->getTableName('NUMMERN');
        $where   = '';
        $groupBy = '';
        $orderBy = '';
        $limit   = '';
        $res = $this->gsaDbObj->exec_SELECTquery($select, $table, $where);
        if ($res == false) {
            throw new tx_pttools_exception('Query failed', 1, $this->gsaDbObj->sql_error());
        }
        $oldvalues = $this->gsaDbObj->sql_fetch_assoc($res);
        $this->gsaDbObj->sql_free_result($res);
        trace($oldvalues);

        // special case: check for empty NUMMERN table
        if (! (is_array($oldvalues) || (count($oldvalues) == 0))) {
            // insert empty record
            $table           = $this->getTableName('NUMMERN');
            $insertFieldsArr = array('VORGANG' => 0);

            // exec query using TYPO3 DB API
            $res = $this->gsaDbObj->exec_INSERTquery($table, $insertFieldsArr);
            if ($res == false) {
                throw new tx_pttools_exception('Query failed', 1, $this->gsaDbObj->sql_error());
            }

            // simulate successful read
            $oldvalues = array(
                'datum' => '0000-00-00',
                'today' => date('Y-m-d'),
            );
        }

        // perform necessary resets
        if ($oldvalues['datum'] < $oldvalues['today']) {
            // last update was before today, so find out which fields have to be reset
            $updateFieldsArr = array();
            if (strncmp($oldvalues['datum'],  $oldvalues['today'], 4) != 0) {
                // different year, so reset tag, monat, jahr
                $updateFieldsArr[$erfart.'JAHR'] = 0;
                $oldvalues['jahr'] = 0;
                $updateFieldsArr[$erfart.'MONAT'] = 0;
                $oldvalues['monat'] = 0;
                $updateFieldsArr[$erfart.'TAG'] = 0;
                $oldvalues['tag'] = 0;
            } else if (strncmp($oldvalues['datum'],  $oldvalues['today'], 7) != 0) {
                // same year, different month
                $updateFieldsArr[$erfart.'MONAT'] = 0;
                $oldvalues['monat'] = 0;
                $updateFieldsArr[$erfart.'TAG'] = 0;
                $oldvalues['tag'] = 0;
            } else {
                // only day needs to reset
                $updateFieldsArr[$erfart.'TAG'] = 0;
                $oldvalues['tag'] = 0;
            }
            // date of last update needs to be updated in any case
            $updateFieldsArr[$erfart.'DATUM'] = $oldvalues['today'];
            // someone else could have performed a reset since we have read the field values, so we add a where clause to make sure there are no double resets
            $where = '('.$erfart.'DATUM < "'.$oldvalues['today'].'")'.
                     ' OR ('.$erfart.'DATUM IS NULL)';
            $res = $this->gsaDbObj->exec_UPDATEquery($table, $where, $updateFieldsArr);
            if ($res == false) {
                throw new tx_pttools_exception('Counter reset failed', 1, $this->gsaDbObj->sql_error());
            }
        }

        // increment fields:
        // because there is only one row in NUMMERN, an atomic update is safe
        $where = '';
        $updateFieldsArr = array();
        $noQuoteFields = array();
        $fname = $erfart.'ENDLOS';
        $updateFieldsArr[$fname] = 'LAST_INSERT_ID(IFNULL('.$fname.', 0) + 1)';
        $noQuoteFields[] = $fname;
        $fname = $erfart.'TAG';
        $updateFieldsArr[$fname] = 'IFNULL('.$fname.', 0) + 1';
        $noQuoteFields[] = $fname;
        $fname = $erfart.'MONAT';
        $updateFieldsArr[$fname] = 'IFNULL('.$fname.', 0) + 1';
        $noQuoteFields[] = $fname;
        $fname = $erfart.'JAHR';
        $updateFieldsArr[$fname] = 'IFNULL('.$fname.', 0) + 1';
        $noQuoteFields[] = $fname;
        $res = $this->gsaDbObj->exec_UPDATEquery($table, $where, $updateFieldsArr, $noQuoteFields);
        if ($res == false) {
            throw new tx_pttools_exception('Increment failed', 1, $this->gsaDbObj->sql_error());
        }

        // return requested value:
        // because someone else might have updated simultaneously, we can not simply read back the current values. Instead we use LAST_INSERT_ID() to retrieve our unique value for ENDLOS and use it to calculate the other values from $oldvalues
        $res = $this->gsaDbObj->sql_query('SELECT LAST_INSERT_ID()');
        if ($res == false) {
            throw new tx_pttools_exception('Query failed', 1, $this->gsaDbObj->sql_error());
        }
        $row = $this->gsaDbObj->sql_fetch_row($res);
        $this->gsaDbObj->sql_free_result($res);
        $endlos = intval($row[0]);
        $added = $endlos - intval($oldvalues['endlos']);
        switch ($whatnum) {
            case self::WN_ENDLOS:
                $result = $endlos;
                break;
            case self::WN_TAG:
                $result = $added + intval($oldvalues['tag']);
                break;
            case self::WN_MONAT:
                $result = $added + intval($oldvalues['monat']);
                break;
            case self::WN_JAHR:
                $result = $added + intval($oldvalues['jahr']);
                break;
            default:
                throw new tx_pttools_exception('Parameter error', 3, 'Illegal number type requested');
        }
        
        trace($result);
        return $result;
        
    }
 
    /**
     * Selects and returns the last used ID from the GSA system database table SYNEWNUMBER for a specified database table 
     *
     * @param   string      GSA database table name to look up last used ID (GSA database field "SYNEWNUMBER.TABLENAME")
     * @return  integer     last used UID for the specified database table (GSA database field "SYNEWNUMBER.LASTNUMBER")
     * @throws  tx_pttools_exception   if the query fails/returns false
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2005-11-17
     */
    protected function selectLastId($tableName) {
        
        // query preparation
        $select  = 'LASTNUMBER';
        $from    = $this->getTableName('SYNEWNUMBER');
        $where   = 'TABLENAME LIKE '.$this->gsaDbObj->fullQuoteStr($tableName, $from);
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
        
        trace($a_row[$select]); 
        return $a_row[$select];
        
    }
     
    /**
     * Savely increments the GSA system database table SYNEWNUMBER record for a specified database table 
     * Attention: uses special MySQL-Function LAST_INSERT_ID() to remember the atomicly updated value.
     * see MySQL 3.23-4.1 Manual section 12.9.3
     * we need to use lowlevel mysql_query-function to read this value.
     *
     * @param   string      GSA database table name to update the SYNEWNUMBER record (GSA database field "SYNEWNUMBER.TABLENAME")
     * @return  integer     New value of the incremented field
     * @throws  tx_pttools_exception   if the query fails/returns false
     * @author  Wolfgang Zenker <zenker@punkt.de>
     * @since   2006-04-26 
     */
    protected function incrementSynewnumberRecord($tableName) {
        
        // query preparation
        $table           = $this->getTableName('SYNEWNUMBER');
        $where           = 'TABLENAME LIKE '.$this->gsaDbObj->fullQuoteStr($tableName, $table);
        $updateFieldsArr = array('LASTNUMBER' => 'LAST_INSERT_ID(LASTNUMBER+1)');
        $noQuoteFields = array('LASTNUMBER');
        
        // exec query using TYPO3 DB API
        $res = $this->gsaDbObj->exec_UPDATEquery($table, $where, $updateFieldsArr, $noQuoteFields);
        if ($res == false) {
            throw new tx_pttools_exception('Increment failed', 1, $this->gsaDbObj->sql_error());
        }
        
        $res = $this->gsaDbObj->sql_query('SELECT LAST_INSERT_ID()');
        if ($res == false) {
            throw new tx_pttools_exception('Query failed', 1, $this->gsaDbObj->sql_error());
        }
        $a_row = $this->gsaDbObj->sql_fetch_row($res);
        $this->gsaDbObj->sql_free_result($res);
        trace($a_row[0]);
        return $a_row[0];
        
    }
    
    /**
     * Inserts a new GSA system database table SYNEWNUMBER record for a specified database table 
     *
     * @param   string      GSA database table name to update the SYNEWNUMBER record (GSA database field "SYNEWNUMBER.TABLENAME")
     * @param   integer     last used UID for the specified database table (GSA database field "SYNEWNUMBER.LASTNUMBER")
     * @return  boolean     TRUE on success or FALSE on error
     * @throws  tx_pttools_exception   if the query fails/returns false
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2005-11-17 
     */
    protected function insertSynewnumberRecord($tableName, $lastUsedId) {
        
        // query preparation
        $table           = $this->getTableName('SYNEWNUMBER');
        $insertFieldsArr = array('TABLENAME'  => $tableName, 
                                 'LASTNUMBER' => intval($lastUsedId)
                           );
        
        // exec query using TYPO3 DB API
        $res = $this->gsaDbObj->exec_INSERTquery($table, $insertFieldsArr);
        if ($res == false) {
            throw new tx_pttools_exception('Query failed', 1, $this->gsaDbObj->sql_error());
        }
        
        trace($res); 
        return $res;
        
    }
    
    /**
     * Selects and returns the last value of a specified field from the GSA database table NUMMERN
     *
     * @param   string      field name from the GSA system database table NUMMERN to retrieve its value
     * @return  mixed       (integer, double or date string) last value of a specified field from the GSA system database table NUMMERN
     * @throws  tx_pttools_exception   if the query fails/returns false
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2005-11-24
     */
    protected function selectLastNumberValue($fieldName) {
        
        // query preparation
        $select  = addslashes($fieldName);
        $from    = $this->getTableName('NUMMERN');
        $where   = '';
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
        
        trace($a_row[$fieldName]); 
        return $a_row[$fieldName];
        
    }
     
    /**
     * Savely increments  a specified field of the GSA database table NUMMERN
     * Attention: may only be called for numeric values!
     * uses same mechanism as incrementSynewnumberRecord()
     *
     * @param   string      name of the field to update in the GSA database table NUMMERN
     * @return  integer     new value of field
     * @throws  tx_pttools_exception   if the query fails/returns false
     * @author  Wolfgang Zenker <zenker@punkt.de>
     * @since   2006-04-25 
     */
    protected function incrementNumberValue($fieldName) {
        
        // check for empty table first
        $select     = $fieldName;
        $table      = $this->getTableName('NUMMERN');
        $where      = '';
        $res = $this->gsaDbObj->exec_SELECTquery($select, $table, $where);
        if ($res == false) {
            throw new tx_pttools_exception('Query failed', 1, $this->gsaDbObj->sql_error());
        }
        $oldvalues = $this->gsaDbObj->sql_fetch_assoc($res);
        $this->gsaDbObj->sql_free_result($res);
        trace($oldvalues);
        if (! (is_array($oldvalues) || (count($oldvalues) == 0))) {
            // NUMMERN is empty, insert new empty record
            $insertFieldsArr = array($fieldName => 0);
            $res = $this->gsaDbObj->exec_INSERTquery($table, $insertFieldsArr);
            if ($res == false) {
                throw new tx_pttools_exception('Query failed', 1, $this->gsaDbObj->sql_error());
            }
        }
        
        // query preparation
        $table           = $this->getTableName('NUMMERN');
        $where           = '';
        $updateFieldsArr = array($fieldName => 'LAST_INSERT_ID(IFNULL('.$fieldName.', 0) + 1)');
        $noQuoteFields = array($fieldName);
        
        // exec query using TYPO3 DB API
        $res = $this->gsaDbObj->exec_UPDATEquery($table, $where, $updateFieldsArr, $noQuoteFields);
        if ($res == false) {
            throw new tx_pttools_exception('Query failed', 1, $this->gsaDbObj->sql_error());
        }
        
        $res = $this->gsaDbObj->sql_query('SELECT LAST_INSERT_ID()');
        if ($res == false) {
            throw new tx_pttools_exception('Query failed', 1, $this->gsaDbObj->sql_error());
        }
        $a_row = $this->gsaDbObj->sql_fetch_row($res);
        trace($a_row[0]);
        return $a_row[0];
        
    }
    
    
    
} // end class



/*******************************************************************************
 *   TYPO3 XCLASS INCLUSION (for class extension/overriding)
 ******************************************************************************/
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsasocket/res/class.tx_ptgsasocket_gsaDbAccessor.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsasocket/res/class.tx_ptgsasocket_gsaDbAccessor.php']);
}

?>
