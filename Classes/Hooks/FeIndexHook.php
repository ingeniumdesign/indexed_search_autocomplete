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

// Exit, if script is called directly (must be included via eID in index_ts.php)
if (!defined ('PATH_typo3conf')) 	die ('Could not access this script directly!');

// Exit if user tries to fiddle with the values ;-)
if(!(\TYPO3\CMS\Core\Utility\GeneralUtility::_GET('sr') < 1 || \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('sh') == \TYPO3\CMS\Core\Utility\GeneralUtility::md5int(\TYPO3\CMS\Core\Utility\GeneralUtility::_GET('sr') . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']))) {
	die('thx but no thx');
}
$obj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_indexed_search_autocomplete_fe_index');
$obj->look_up();

// No need to waste more time here :-]
exit();


class tx_indexed_search_autocomplete_fe_index {

	/*
	* Do the setup bit
	*/
	function tx_indexed_search_autocomplete_fe_index() {
		
		$this->pages = array(intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GET('sr')));

		// hook for showing extension version
		if( intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GET('sv')) > 0 ) {
			$_EXTKEY = "temp";
			require_once($GLOBALS['TYPO3_LOADED_EXT']['indexed_search_autocomplete']['siteRelPath'] . 'ext_emconf.php');
			die($EM_CONF[$_EXTKEY]['version']);
		}
		
		return true;
	}

	
	/*
	* Look up the pages the user can access
	*/
	function get_pages() {	
		$pages = implode(',', $this->pages);		
		while($pages = $this->get_subpages($pages)) {
			$this->pages = array_merge($this->pages, explode(',', $pages));
		}		
		return true;
	}	

	/*
	* Return subpages to $pages
	*/
	function get_subpages($pages) {		
		$new_pages = '';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'pages', "pid IN ($pages)" . $this->enableFields);		
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$new_pages .= $row['uid'] . ',';
		}		
		// Did we get any?
		if(strlen($new_pages) > 0) {
			$new_pages = substr($new_pages, 0, -1);
		}		
		return $new_pages;
	}
	

	/*
	* Look up the words and print them
	*/
	function look_up() {		
		$ll = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET( 'll' );
		$language = ( !empty( $ll ) ) ? intval( $ll ) : 0;
		$the_word_array = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(' ', \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('sw'), 1);
		$the_final_word_array = array();
		$this->get_pages();
				
		// Look up the words
		foreach($the_word_array as $one_word) {
			
			if(strlen($one_word) < 3) {
				continue;
			}

			$condition =  'index_words.wid = index_rel.wid AND 
            index_rel.phash = index_phash.phash AND index_phash.sys_language_uid = ' .$language. ' AND
            (baseword LIKE '. $GLOBALS['TYPO3_DB']->fullQuoteStr("%$one_word%", 'index_words') . ' OR 
            baseword LIKE '. $GLOBALS['TYPO3_DB']->fullQuoteStr("$one_word%", 'index_words') . ' OR 
            baseword LIKE '. $GLOBALS['TYPO3_DB']->fullQuoteStr("%$one_word", 'index_words') . ') ' .
            $this->multipleGroupsWhereClause;

		 	$data = $GLOBALS['TYPO3_DB']->exec_SELECTquery( 'baseword, data_page_id', 'index_words, index_rel, index_phash',
			$condition, '', '', '' );

			// Build the array
			$results = array();
			$word_page_index = array();
			while($value = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($data)) {
				// Final check against the pages we retreived on basis of the enablefields (starttime/endtime)
				if(in_array($value['data_page_id'], $this->pages)) {			
					$word_page_index[$value['data_page_id']][] = $value['baseword'];
					if(!isset($results[$value['baseword']])) {
						$results[$value['baseword']] = 1;
					} else {
						$results[$value['baseword']]++;
					}
				}
			}
			
			// For each word, count if its a subword from another word(s) in the array
			$the_words = array_keys($results);
			$final_results = array();
			foreach($results as $word => $count) {
				$final_count = 0;
				foreach($word_page_index as $one_page_array) {
					foreach($one_page_array as $one_word) {
						if(strpos($one_word, $word) !== false) {
							// We got a hit on this page
							$final_count++;
							// Dont count more, next page up!
							break;
						}
					}
				}
				$final_results[$word] = $final_count;
			}
			
			// Sort it
			arsort($final_results);

			// Save us from charset trouble (maybe ;-) )
			header('Content-type: text/html; charset=UTF-8');

			// Print it
			foreach($final_results as $word => $count) {			
				echo $word . '|' . $count . "\n";
			}
			return true;
		}
	}
}