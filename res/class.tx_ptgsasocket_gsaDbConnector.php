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
 * GSA database handler class for the 'pt_gsasocket' extension
 *
 * $Id: class.tx_ptgsasocket_gsaDbConnector.php,v 1.18 2008/11/18 16:43:37 ry37 Exp $
 *
 * @author  Rainer Kuhn <kuhn@punkt.de>
 * @since   2005-07-20
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 */


/**
 * Inclusion of TYPO3 libraries
 *
 * @see t3lib_db
 */
require_once(PATH_t3lib.'class.t3lib_db.php');

/**
 * Inclusion of external resources
 */
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_debug.php'; // debugging class with trace() function
require_once t3lib_extMgm::extPath('pt_tools').'res/objects/class.tx_pttools_exception.php'; // general exception class
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_div.php'; // general helper library class
require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_iSingleton.php'; // interface for Singleton design pattern
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_assert.php';



/**
 * Creates a database connection object for the GSA database, implements the Singleton pattern and provides all methods of t3lib_db
 *
 * @author      Rainer Kuhn <kuhn@punkt.de>
 * @since       2005-11-09, based on code from 2005-07-20
 * @package     TYPO3
 * @subpackage  tx_ptgsasocket
 */
class tx_ptgsasocket_gsaDbConnector extends t3lib_db implements tx_pttools_iSingleton {
    
    /***************************************************************************
     *   PROPERTIES
     **************************************************************************/
    
    private static $uniqueInstance = NULL; // (tx_ptgsasocket_gsaDbConnector object) Singleton unique instance
    
    protected $extKey = 'pt_gsasocket'; // (string) the extension key
    
    protected $host = '';           // (string)
    protected $database = '';       // (string)
    protected $user = '';           // (string)
    protected $pass = '';           // (string)
    
    protected $logEnabled = false;  // (boolean)
    protected $logDirPath = '';     // (string)
    
    protected $connection = NULL;     // (resource)
    protected $selectDbResult = NULL; // (boolean) 
    
    
    
    /***************************************************************************
     *   CONSTRUCTOR & OBJECT HANDLING METHODS
     **************************************************************************/
    
    /**
     * Class constructor (protected): Sets this object's properties and connects to the GSA database. Use getInstance() to get the unique instance of this object.
     *
     * @param   void
     * @return  void
     * @global  $GLOBALS['TYPO3_CONF_VARS']['SYS']['setDBinit']
     * @throws  tx_pttools_exception   if no DB configuration data found
     * @throws  tx_pttools_exception   if DB connection fails
     * @throws  tx_pttools_exception   if DB selection fails
     * @throws  tx_pttools_exception   if required database parameters are empty
     * @see     tx_ptgsasocket_gsaDbConnector::getInstance()
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2005-07-19
     */
    private function __construct() {
    
        trace('***** Creating new '.__CLASS__.' object. *****');
        
        // for TYPO3 3.8.0+: enable storage of last built SQL query in $this->debug_lastBuiltQuery for all query building functions of class t3lib_DB
        $this->store_lastBuiltQuery = true;
        
        // get basic extension configuration incl. GSA DB access data from localconf.php (extension configuration in Extension Manager)
        $extConfArr = tx_pttools_div::returnExtConfArray('pt_gsasocket');
        
        $this->logEnabled = $extConfArr['logEnabled'];
        $this->logDirPath = $extConfArr['logDirPath'];
        $this->logAdminEmail = $extConfArr['logAdminEmail'];
        $this->logHostName = $extConfArr['logHostName'];
        if (isset($extConfArr['useGsaTablesInTypo3Database'])) {
            $this->useGsaTablesInTypo3Database = (boolean)$extConfArr['useGsaTablesInTypo3Database'];
        }
        
        // get database connection data
        if ($extConfArr['useGsaTablesInTypo3Database'] == false) {
            // use external GSA database
            $this->host     = $extConfArr['dbGSAhost'];
            $this->database = $extConfArr['dbGSAname'];
            $this->user     = $extConfArr['dbGSAuser']; 
            $this->pass     = $extConfArr['dbGSApwd'];
            // allow individual database intialization
            $TYPO3setDBinit = $GLOBALS['TYPO3_CONF_VARS']['SYS']['setDBinit']; 
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['setDBinit'] = $extConfArr['dbGSAsetDBinit']; // this will be used now for GSA DB initialization in t3lib_db::sql_pconnect() below
        } else {
            // use TYPO3 database (constants used below are defined in t3lib/config_default.php)
            $this->host     = TYPO3_db_host;
            $this->database = TYPO3_db;
            $this->user     = TYPO3_db_username; 
            $this->pass     = TYPO3_db_password;
        }
        
        // connect to database server and select database
        tx_pttools_assert::isNotEmptyString($this->host, array('message'=>'No database host found for GSA DB.'));
        tx_pttools_assert::isNotEmptyString($this->database, array('message'=>'No database name found for GSA DB.'));
        tx_pttools_assert::isNotEmptyString($this->user, array('message'=>'No database user found for GSA DB.'));  // note: password my be empty, this is not an error
        $php_errormsg = '';
        $this->connection = @$this->sql_pconnect($this->host, $this->user, $this->pass);
        if ($this->connection == false) {
            throw new tx_pttools_exception('Could not connect to database server', 0, $php_errormsg);
        }
        $this->selectDbResult = $this->sql_select_db($this->database);
        if ($this->selectDbResult == false) {
            throw new tx_pttools_exception('Could not select database', 1, $this->sql_error());
        }
        
        // trace results
        trace('Connected successfully to GSA-DB: '.($extConfArr['useGsaTablesInTypo3Database'] == true ? '***** Using TYPO3 DB for GSA tables! *****' : 'Using additional GSA database.'));
        trace($this->connection, 0, '$this->connection');
        trace($this->selectDbResult, 0, '$this->selectDbResult');
        
        // re-set original TYPO3 database intialization if overwritten for an external GSA database
        if ($extConfArr['useGsaTablesInTypo3Database'] == false) {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['setDBinit'] = $TYPO3setDBinit; // perform all other connects with original TYPO3 setting
        }
        
    }
    
    /**
     * Returns a unique instance (Singleton) of the object. Use this method instead of the private/protected class constructor.
     *
     * @param   void
     * @return  tx_ptgsasocket_gsaDbConnector      unique instance of the object (Singleton) 
     * @global     
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2005-07-19
     */
    public static function getInstance() {
        
        if (self::$uniqueInstance === NULL) {
            $className = __CLASS__;
            self::$uniqueInstance = new $className;
        }
        
        return self::$uniqueInstance;
        
    }
    
    /**
     * Final method to prevent object cloning (using 'clone'), in order to use only the singleton unique instance of the object.
     * @param   void
     * @return  void
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2005-09-15
     */
    public final function __clone() {
        
        trigger_error('Clone is not allowed for '.get_class($this).' (Singleton)', E_USER_ERROR);
        
    }
    
    /** 
     * Class destructor: Does nothing currently since the DB connection is persistant (non-persistant DB connection should be disconnected here)
     *
     * @param   void 
     * @return  void
     * @global  
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2005-08-10 
     */
    public function __destruct() {
        
        /*
         * a non-persistant DB connection should be disconnected here, e.g. for MySQL using mysql_close($this->connection)
         */
        trace('***** '.__CLASS__.' object destroyed. *****');
        
    }
    
    
    
    /***************************************************************************
     *   BUSINESS LOGIC METHODS
     **************************************************************************/
    
    /** 
     * Logs a given SQL query to the extensions' log file (if logging is enabled in extension config)
     *
     * @param   string      SQL query to log
     * @return  void
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2007-02-21 
     */
    protected function logQuery($sqlQuery) {
              
        static $adminMailSent = false;  // static var to remember status while script execution
        $logMsg = '';
        
        if ($this->logEnabled == true) {
            
            // remove tabs from original query ans add timestamp
            $logDate = '['.date('D Y-m-d H:i:s').']';
            $logMsg .= $logDate.' '."\n".str_replace(chr(9), '', $sqlQuery);
            
            // try logging (dir exists? access rights ok?)
            if (@error_log($logMsg."\n\n", 3, $this->logDirPath.$this->extKey.'_log')) {
                $adminMailSent = false; // unset adminMailSent-Flag
                trace($logDate. ' The following query will be logged by '.__METHOD__);
            // if logging not possible AND logging error mail not sent before: try to sent error mails to admin email address
            } elseif ($adminMailSent == false && strlen($this->logAdminEmail) > 0) {
                if ($adminMailSent == false) {
                    $mailRecipient  = $this->logAdminEmail;
                    $mailHeaders    = "From: ".$this->extKey."@".$this->logHostName."\r\n".
                                      "Content-Type: text/plain; charset=iso-8859-1\r\n".
                                      "Content-Transfer-Encoding: 8bit\r\n".
                                      "MIME-Version: 1.0";
                    trace('LOGGING ERROR! Sending error mail to admin '.$this->logAdminEmail);
                    $mailSubject    = "Logging Error (".$this->extKey.") on ".$this->logHostName;
                    $mailMessage    = "Logging for ".$this->extKey." on ".$this->logHostName." not possible in\n".
                                      $this->logDirPath.$this->extKey."_log.\n\n".
                                      "Please check directory path and access rights.\n\n";
                    mail($mailRecipient, $mailSubject, $mailMessage, $mailHeaders);
                    $adminMailSent = true; // set adminMailSent-Flag
                }
            }
    
        }
          
    }
    
    
    
    /***************************************************************************
     *   REDECLARED PARENT CLASS METHODS (from t3lib_DB)
     **************************************************************************/
    
    /** 
     * Calls the parent class method and adds tracing/logging for the resulting query
     *
     * @param   see t3lib_DB::INSERTquery()
     * @param   see t3lib_DB::INSERTquery()
     * @param   see t3lib_DB::INSERTquery()
     * @return  see t3lib_DB::INSERTquery()
     * @see     t3lib_DB::INSERTquery()
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2007-02-21 
     */
    public function INSERTquery($table, $fields_values, $no_quote_fields=FALSE) {
        
        $query = parent::INSERTquery($table, $fields_values, $no_quote_fields);
        
        // log query
        $debugQuery = str_replace(chr(9), '', $query); // removes tabs from query for better readability
        trace($debugQuery);
        $this->logQuery($debugQuery);
        
        return $query;
        
    }
    
    /** 
     * Calls the parent class method and adds tracing/logging for the resulting query
     *
     * @param   see t3lib_DB::UPDATEquery()
     * @param   see t3lib_DB::UPDATEquery()
     * @param   see t3lib_DB::UPDATEquery()
     * @param   see t3lib_DB::UPDATEquery()
     * @return  see t3lib_DB::UPDATEquery()
     * @see     t3lib_DB::UPDATEquery()
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2007-02-21 
     */
    public function UPDATEquery($table, $where, $fields_values, $no_quote_fields=FALSE) {
        
        $query = parent::UPDATEquery($table, $where, $fields_values, $no_quote_fields);
        
        // log query
        $debugQuery = str_replace(chr(9), '', $query); // removes tabs from query for better readability
        trace($debugQuery);
        $this->logQuery($debugQuery);
        
        return $query;
        
    }
    
    /** 
     * Calls the parent class method and adds tracing/logging for the resulting query
     *
     * @param   see t3lib_DB::DELETEquery()
     * @param   see t3lib_DB::DELETEquery()
     * @return  see t3lib_DB::DELETEquery()
     * @see     t3lib_DB::DELETEquery()
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2007-02-21 
     */
    public function DELETEquery($table, $where) {
        
        $query = parent::DELETEquery($table, $where);
        
        // log query  
        $debugQuery = str_replace(chr(9), '', $query); // removes tabs from query for better readability
        trace($debugQuery);
        $this->logQuery($debugQuery);  
        
        return $query;
        
    }
    
    /** 
     * Calls the parent class method and adds tracing for the resulting query
     *
     * @param   see t3lib_DB::SELECTquery()
     * @param   see t3lib_DB::SELECTquery()
     * @param   see t3lib_DB::SELECTquery()
     * @param   see t3lib_DB::SELECTquery()
     * @param   see t3lib_DB::SELECTquery()
     * @param   see t3lib_DB::SELECTquery()
     * @return  see t3lib_DB::SELECTquery()
     * @see     t3lib_DB::SELECTquery()
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2007-02-21 
     */
    public function SELECTquery($select_fields, $from_table, $where_clause, $groupBy='', $orderBy='', $limit='') {
        
        $query = parent::SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
        
        // trace query
        $debugQuery = str_replace(chr(9), '', $query); // removes tabs from query for better readability
        trace($debugQuery);
        #$this->logQuery($debugQuery);  // activate for debugging purposes only - no logging by default for non-modifying queries like selects!
        
        return $query;
        
    }
    
    
    
} // end class



/*******************************************************************************
 *   TYPO3 XCLASS INCLUSION (for class extension/overriding)
 ******************************************************************************/
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsasocket/res/class.tx_ptgsasocket_gsaDbConnector.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsasocket/res/class.tx_ptgsasocket_gsaDbConnector.php']);
}

?>