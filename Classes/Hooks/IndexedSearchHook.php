<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2017 Sebastian Schmal - INGENIUMDESIGN <info@ingeniumdesign.de>
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
 * Plugin 'hook' for the 'indexed_search' extension.
 *
 * @author    Sebastian Schmal - INGENIUMDESIGN <info@ingeniumdesign.de>
 * @package    TYPO3
 * @subpackage    tx_indexed_search_autocomplete
 */
 
class tx_indexed_search_autocomplete_pi1 {
	/**
	 * Hook from indexed_search
	 */
	function initialize_postProc() {
		$on_search_page = 1;
		return $this->main($on_search_page);
	}

	/**
	 * Add the Js and stylesheet
	 */
	function main($on_search_page = 0) {


		$this->key = 'indexed_search_autocomplete';

		// alternative labels (waiting for real i18n support)
		$altResultLabel = str_replace("'", "", $GLOBALS['TSFE']->tmpl->setup['plugin.'][$this->key . '.']['altResultLabel']);
		$altResultsLabel = str_replace("'", "", $GLOBALS['TSFE']->tmpl->setup['plugin.'][$this->key . '.']['altResultsLabel']);
		
		// Should we auto-submit the search form?
		$autoSubmit = intval($GLOBALS['TSFE']->tmpl->setup['plugin.'][$this->key . '.']['autoSubmit']);

		// jQueryFile is loaded?
		$jQueryFile = ( $GLOBALS['TSFE']->tmpl->setup['plugin.'][$this->key . '.']['jQueryFile'] );

		
		// Is jQuery already loaded externally?
		$jQueryLoadedExternally = intval($GLOBALS['TSFE']->tmpl->setup['plugin.'][$this->key . '.']['jQueryLoadedExternally']);

		// Are the CSS definitions already loaded externally?
		$cssLoadedExternally = intval($GLOBALS['TSFE']->tmpl->setup['plugin.'][$this->key . '.']['cssLoadedExternally']);

		// Supprt extension t3query for jQuery loadage
		if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('t3jquery')){
			require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('t3jquery').'class.tx_t3jquery.php');
		}

		// Should we limit the result to a number of suggestions?
		$maxResults = intval($GLOBALS['TSFE']->tmpl->setup['plugin.'][$this->key . '.']['maxResults']);

		// Shall we auto correct the lists width?
		$autoResize = $GLOBALS['TSFE']->tmpl->setup['plugin.'][$this->key . '.']['autoResize'] ? 'true' : 'false';

		// Should we run into jQuery noConflict Method? 
		$noConflictMethod = intval($GLOBALS['TSFE']->tmpl->setup['plugin.'][$this->key . '.']['noConflictMethod']);

		// Do we need this at all pages?
		$onlySearchPage = intval($GLOBALS['TSFE']->tmpl->setup['plugin.'][$this->key . '.']['onlySearchPage']);

		if(intval($on_search_page) == 0 && $onlySearchPage != 0) {
			return '';
		}

		// No need to insert twice
		if($GLOBALS[$this->key] != 1) {
			$head = '';
			// Include JS
			// load jQuery via t3jquery extension?
			if (T3JQUERY === true) {
				tx_t3jquery::addJqJS();
			}
			$head .= "\n" . '<script type="text/javascript" src="'.$jQueryFile.'"></script>';


			// Include CSS
			if (!$cssLoadedExternally) {
				$head .= "\n" . '<link rel="stylesheet" type="text/css" href="' . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath($this->key) . 'Resources/Public/Css/Indexed_search_autocomplete.css" />';
				//returns the relative path to the css file
			}
			

			// Make rootpage global as we dont have it when we 'eId'. People can tamper with it client side, so we hash it, making it a little harder for them. It does not mean much they will not get access to more pages than they should anyways
			$head .= "\n" . '<script type="text/javascript">' . "\n";
			if ($noConflictMethod > 0) {
				$head .= "	jQuery.noConflict();\n";
			}
			$head .= 'var ll='.intval($GLOBALS['TSFE']->sys_language_uid) . ';var sr=' . intval($GLOBALS['TSFE']->config['rootLine'][0]['uid']) . ';var sh="' . \TYPO3\CMS\Core\Utility\GeneralUtility::md5int($GLOBALS['TSFE']->config['rootLine'][0]['uid'] . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']) . '";' . "\n";
			$head .= "	var cb_indexsearch_autocomplete = {\n";
			$head .= "		altResultLabel: '$altResultLabel',\n";
			$head .= "		altResultsLabel: '$altResultsLabel',\n";
			$head .= "		autoSubmit: $autoSubmit,\n";
			$head .= "		maxResults: $maxResults,\n";
			$head .= "		autoResize: $autoResize\n";
			$head .= "	};\n";
			$head .= "</script>\n";
			
			$GLOBALS['TSFE']->additionalFooterData['tx_indexed_search_autocomplete_pi1'] = $head;
			
			// We have been here
			$GLOBALS[$this->key] = 1;
		}
		return '';
	}
}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/indexed_search_autocomplete/Classes/Hooks/IndexedSearchHook.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/indexed_search_autocomplete/Classes/Hooks/IndexedSearchHook.php']);
}