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
 * General GSA data related helper methods library (part of the 'tx_ptgsasocket' extension ) 
 * 
 * $Id: class.tx_ptgsasocket_staticLib.php,v 1.7 2008/03/28 16:04:46 ry37 Exp $
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
require_once t3lib_extMgm::extPath('pt_gsasocket').'res/class.tx_ptgsasocket_paradoxDataAccessor.php'; // accessor class for GSA data stored in Borland Paradox DB files
require_once t3lib_extMgm::extPath('pt_gsasocket').'res/class.tx_ptgsasocket_textfileDataAccessor.php'; // accessor class for GSA data stored in text files of type ".vor"
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_debug.php'; // debugging class with trace() function
require_once t3lib_extMgm::extPath('pt_tools').'res/objects/class.tx_pttools_exception.php'; // general exception class



/**
 * General GSA data related helper methods library
 * 
 * @author      Rainer Kuhn <kuhn@punkt.de>
 * @since       2005-12-16
 * @package     TYPO3
 * @subpackage  tx_ptgsasocket
 */
class tx_ptgsasocket_staticLib {
    
    
    /***************************************************************************
     *  SECTION: GSA RELATED PRESENTATION METHODS
     **************************************************************************/
    
    /**
     * Returns the HTML options for a HTML pulldown selectorbox of all countries (from the GSA paradox file "laender.DB") with country name as option and country post code abbrevation (GSA: KUERZEL) as value
     *
     * @param   string      (optional) post code abbrevation (GSA: KUERZEL) of the country to preselect in selectorbox
     * @param   string      (optional) label for descriptive non-selectable first option (if not passed, there will be no non-selectable first option)
     * @return  string      HTML options for a HTML pulldown selectorbox of all countries
     * @global  
     * @throws  tx_pttools_exception   if no countries are found
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2005-12-16
     */
    public static function generateCountriesOptionsHTML($selectedVal='', $noSelectionLabel='') {
         
        $options = '';
            
        // get countries and throw exception if no countries are found
        $dataArr = tx_ptgsasocket_paradoxDataAccessor::getInstance()->selectCountries(); 
        if (empty($dataArr)) {
            throw new tx_pttools_exception('No countries found for selectorbox', 3);
        }
        
        // create descriptive non-selectable first option (if passed by param only)
        if (!empty($noSelectionLabel)) {
            $options = '<option value="">['.tx_pttools_div::htmlOutput($noSelectionLabel).']</option>'.chr(10);
        }
                        
        for ($i=0; $i<sizeOf($dataArr); $i++) {
            $options .= '<option value="'.tx_pttools_div::htmlOutput($dataArr[$i]['KUERZEL']).'"';
            $options .= (strtoupper($dataArr[$i]['KUERZEL']) == strtoupper($selectedVal) ? ' selected="selected">' : '>');
            $options .= tx_pttools_div::htmlOutput($dataArr[$i]['NAME']);
            $options .= '</option>'.chr(10);
        }
        
        return $options;
        
    }
    
    /**
     * Returns the HTML options for a HTML pulldown selectorbox of all saluations (from the GSA text file "Anrede.vor") with saluation as option and value
     *
     * @param   string      (optional) value of the option to preselect in selectorbox
     * @param   string      (optional) label for descriptive non-selectable first option (if not passed, there will be no non-selectable first option)
     * @return  string      HTML options for a HTML pulldown selectorbox of all saluations
     * @global  
     * @throws  tx_pttools_exception   if no saluations are found
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2005-12-16
     */
    public static function generateSalutationOptionsHTML($selectedVal='', $noSelectionLabel='') {
         
        $options = '';
            
        // get data and throw exception if no data is found
        $dataArr = tx_ptgsasocket_textfileDataAccessor::getInstance()->selectSalutations(); 
        if (empty($dataArr)) {
            throw new tx_pttools_exception('No salutations found for selectorbox', 3);
        }
        
        // create descriptive non-selectable first option (if passed by param only)
        if (!empty($noSelectionLabel)) {
            $options = '<option value="">['.tx_pttools_div::htmlOutput($noSelectionLabel).']</option>'.chr(10);
        }
                        
        for ($i=0; $i<sizeOf($dataArr); $i++) {
            $options .= '<option value="'.tx_pttools_div::htmlOutput($dataArr[$i]['ANREDE']).'"';
            $options .= (strtoupper($dataArr[$i]['ANREDE']) == strtoupper($selectedVal) ? ' selected="selected">' : '>');
            $options .= tx_pttools_div::htmlOutput($dataArr[$i]['ANREDE']);
            $options .= '</option>'.chr(10);
        }
        
        return $options;
        
    }
    
    /**
     * Returns the HTML options for a HTML pulldown selectorbox of all titles (from the GSA text file "Titel.vor") with title as option and value
     *
     * @param   string      (optional) value of the option to preselect in selectorbox
     * @param   string      (optional) label for descriptive non-selectable first option (if not passed, there will be no non-selectable first option)
     * @return  string      HTML options for a HTML pulldown selectorbox of all titles
     * @global  
     * @throws  tx_pttools_exception   if no titles are found
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2005-12-16
     */
    public static function generateTitleOptionsHTML($selectedVal='', $noSelectionLabel='') {
         
        $options = '';
            
        // get data and throw exception if no data is found
        $dataArr = tx_ptgsasocket_textfileDataAccessor::getInstance()->selectTitles(); 
        if (empty($dataArr)) {
            throw new tx_pttools_exception('No titles found for selectorbox', 3);
        }
        
        // create descriptive non-selectable first option (if passed by param only)
        if (!empty($noSelectionLabel)) {
            $options = '<option value="">['.tx_pttools_div::htmlOutput($noSelectionLabel).']</option>'.chr(10);
        }
                        
        for ($i=0; $i<sizeOf($dataArr); $i++) {
            $options .= '<option value="'.tx_pttools_div::htmlOutput($dataArr[$i]['TITEL']).'"';
            $options .= (strtoupper($dataArr[$i]['TITEL']) == strtoupper($selectedVal) ? ' selected="selected">' : '>');
            $options .= tx_pttools_div::htmlOutput($dataArr[$i]['TITEL']);
            $options .= '</option>'.chr(10);
        }
        
        return $options;
        
    }
    
    
    
} // end class



/*******************************************************************************
 *   TYPO3 XCLASS INCLUSION (for class extension/overriding)
 ******************************************************************************/
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsasocket/res/class.tx_ptgsasocket_staticLib.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsasocket/res/class.tx_ptgsasocket_staticLib.php']);
}

?>