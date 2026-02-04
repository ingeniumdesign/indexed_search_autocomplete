# 13.0.2

## UPDATE
- Replaced `$_REQUEST` with PSR-7 `$this->request->getParsedBody()` in SearchController
- Introduced Constructor Injection in SearchService for `Context`, `ConnectionPool`, `ConfigurationManagerInterface` and `IndexSearchRepository`
- Removed manual `GeneralUtility::makeInstance()` calls and unused imports
- Removed all dead jQuery-related code, settings and labels (`ext_localconf.php`, `ext_conf_template.txt`, `ExtConfTemplate.xlf`, `constants.typoscript`)
- Fixed typo in JS selector: `indexed-search-atocomplete-sword` → `indexed-search-autocomplete-sword`
- Removed duplicate JS selector
- Removed dead CSS rules (`li.even`, `li.odd`)
- Removed empty `suggest` block from `composer.json`
- Added explicit `(int)` cast for `rootPidList` values
- Added explicit `$pluginType` argument (`PLUGIN_TYPE_PLUGIN`) to `configurePlugin()` call

## FEATURE
- Added support for TYPO3 13
- Edit the Readme with Info Stuff
- Improved Word & Link autocomplete modes
- Removed jQuery dependency

## FIX
- Modernized internal code structure for TYPO3 13
- Updated fluid templates and TypoScript

### Contributors

- Sebastian Schmal
- Simon Dürr

# 12.1.13

## FEATURE
- Edit structure for TYPO3 12
- Edit the Readme with Info Stuff

### Contributors

- Sebastian Schmal

# 1.0.12

## FEATURE
- Added support for TYPO3 12
- Edit the Readme with Info Stuff
- Added data-mode "word" or "link"
- Add new Sponsoring

### Contributors

- Felix Mächtle
- Sebastian Schmal

# 1.0.11

## FEATURE
- Added support for TYPO3 11
- Edit the Readme with Info Stuff
- Edit TypoScript default files for .typoscript

## FIX
- Add extension key to composer.json - Thanks @RKlingler

### Contributors

- Felix Mächtle
- Sebastian Schmal
- R. Klingler

# 1.0.10

## FIX
- fix File permissions
- Edit Composer.json
- JS fixed `$` not defined

# IndexedSearchAutocomplete Version 1.0.9

## FIX
- fix jQuery Setting for TYPO3 9 and 10

# IndexedSearchAutocomplete Version 1.0.8

## FEATURE
- Added support for TYPO3 10

## FIX
- Refractored JavaScript

# IndexedSearchAutocomplete Version 1.0.7

## FIX
- bug in multilanguage handling  


# IndexedSearchAutocomplete Version 1.0.6

## FEATURE
- Supports TYPO3 9
- Request to the server are now debounced with a delay of 250ms. This reduces the amount of requests.

## FIX
- rootPidList-Parameter of the indexed_search-Extension gets used in word&link-mode  

# IndexedSearchAutocomplete Version 1.0.5

## FEATURE
- If the search-suggestion-div is opened, you can close it by clicking somewhere else


# IndexedSearchAutocomplete Version 1.0.4

## FEATURE
- Added a class to the results-div (".search-autocomplete-results") that shows if there are results ("results", "no-results").


# IndexedSearchAutocomplete Version 1.0.3

## FEATURE
- Added Composer Support
- Edit Readme with "Use MySQL specific fulltext search"


# IndexedSearchAutocomplete Version 1.0.2

## BUGFIX
- Due to a Bug in JS the JS-Code did not got initialized properly every time


# IndexedSearchAutocomplete Version 1.0.1

## BREAKING
- nothing

## FEATURE
- added a new option to search the suggestion immediately after its selected

## TASK
- nothing

## BUGFIX
- added extension - and controller name so it's always the right ajax-request (there were problems with tx_srlanguagemenu)
