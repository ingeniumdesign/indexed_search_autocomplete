# 14.0.1

## UPDATE
- Added TYPO3 v14 support: the extension now runs on TYPO3 13.4 LTS and TYPO3 14 from a single codebase (`typo3/cms-core: ^13.4 || ^14.0`); no functional or source-code changes.
- composer.json: declared `extra.typo3/cms.version` and `Package.providesPackages` (clears the TYPO3 v14.2 `ext_emconf.php` deprecation in classic mode), embedded the title in the `description` (` - ` separator), and tightened the PHP constraint to `^8.2`.
- ext_emconf.php: widened the `typo3` and `indexed_search` dependency ranges to include 14.x and lifted the PHP upper bound.

### Contributors

- Sebastian Schmal


# 13.0.4

## UPDATE
- Link mode: redesigned the default suggestion markup - each result is now a single compact clickable row (linked title plus optional cropped description) instead of the debug-style "Titel/Beschreibung/Page-ID" text lines; the internal page ID is no longer rendered as visible text. If you styled the previous default markup, check your site's CSS
- Removed the empty `ext_conf_template.txt` - the extension no longer shows up with an empty form in Admin Tools > Settings > Extension Configuration
- Removed the dead TypoScript options `xhtml_cleaning` (removed from core in 8.0) and `additionalHeaders` (scalar form, never evaluated) from the AJAX PAGE config
- Performance: the word-mode page scope is now resolved via `PageRepository::getPageIdsRecursive()` instead of loading the entire `pages` table into PHP and walking the tree manually on every request
- README: removed the premature TYPO3 14 badge, corrected the PHP requirement (8.2 - 8.4) and fixed install Step 3 (TypoScript is loaded globally by the extension; the Site Set is optional)
- Removed the legacy `uploadfolder` and `createDirs` keys from `ext_emconf.php` (ignored since TYPO3 v12); `searchType` is now passed as explicit int `1` (`SearchType::PART_OF_WORD`) instead of a boolean - runtime-identical
- Accessibility: the suggestion list now exposes `role="listbox"` on the container and `role="option"` on each entry (both word and link mode) so screen readers announce the suggestions as a selectable list

## FIX
- Security: Word mode no longer suggests words from fe_group-protected pages to visitors without access. Suggestions are now filtered by the frontend user's group list via an `index_grlist` join, mirroring the core `IndexSearchRepository::checkResume()` behaviour that link mode already used
- Security: The client-supplied `mr` (max results) POST parameter is now bounded server-side to 1-50 in both search modes, preventing unbounded `LIMIT` values and result buffers
- JavaScript: Reset `lastSearchQuery` when the input drops below `minlength`, so re-typing the same search term triggers a new suggestion request again
- Word mode: a missing or empty `plugin.tx_indexedsearch.settings.rootPidList` no longer triggers PHP 8.2 warnings/deprecations and no longer silently expands the search scope to the whole installation - the scope now falls back to the current site root (core semantics)
- Link mode: missing indexed_search plugin TypoScript no longer causes a fatal `TypeError` in `IndexSearchRepository::initialize()`; an empty `rootPidList` no longer produces invalid SQL - both cases now use the same site-root fallback
- JavaScript: Selecting a suggestion with Enter now listens on `keydown` instead of the deprecated `keypress` event, so keyboard selection also works reliably on mobile/IME keyboards
- The search term is now only accepted as a string; an array-shaped POST parameter (`s[]=...`) no longer triggers a PHP "Array to string conversion" warning (or an HTTP 500 under debug error presets)
- JavaScript: visible suggestions are no longer wiped when a value-preserving key (Shift, CapsLock, Home/End) is pressed - the list is only cleared when a new search actually starts
- JavaScript: a scheduled request is now cancelled when the input drops below `minlength`, so suggestions for an already-deleted term no longer appear after the debounce delay

### Contributors

- Sebastian Schmal


# 13.0.3

## UPDATE
- Migrated to pure TYPO3 13.4 architecture: removed `ext_tables.php`.
- Replaced deprecated `PLUGIN_TYPE_PLUGIN` with `PLUGIN_TYPE_CONTENT_ELEMENT` in `configurePlugin()`
- Replaced `COA_INT` + `tt_content.list.20` TypoScript approach with `EXTBASEPLUGIN` on a dedicated `PAGE` typeNum
- TypoScript constants and setup are now loaded globally via `addTypoScriptConstants()` and `addTypoScriptSetup()` in `ext_localconf.php`
- AJAX endpoint PAGE typeNum `7423794` is now defined in `Configuration/TypoScript/setup.typoscript` (not in the Config Set)
- Added `pluginName: 'Search'` to `f:uri.action()` in Fluid template to ensure correct plugin namespace in URL

## FIX
- Fixed deprecation warning #105076 (PLUGIN_TYPE_PLUGIN)
- Fixed broken AJAX endpoint (`No page configured for type=7423794`) caused by TypoScript not loading
- Fixed unresolved `{$...}` constants causing empty JS/CSS file paths

### Contributors

- Sebastian Schmal


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
