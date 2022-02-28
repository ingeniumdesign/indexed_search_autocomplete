# IndexedSearchAutocomplete Version 1.0.11

## FEATURE
- Added support for Typo3 11
- Edit the Readme with Info Stuff
- Edit TypoScript default files for .typoscript

##FIX
- Add extension key to composer.json - Thanks @RKlingler

# IndexedSearchAutocomplete Version 1.0.10

##FIX
- fix File permissions
- Edit Composer.json
- JS fixed `$` not defined

# IndexedSearchAutocomplete Version 1.0.9

##FIX
- fix jQuery Setting for TYPO3 9 and 10

# IndexedSearchAutocomplete Version 1.0.8

## FEATURE
- Added support for Typo3 10

##FIX
- Refractored JavaScript


# IndexedSearchAutocomplete Version 1.0.7

##FIX
- bug in multilanguage handling  


# IndexedSearchAutocomplete Version 1.0.6

## FEATURE
- Supports Typo3 9
- Request to the server are now debounced with a delay of 250ms. This reduces the amount of requests.

##FIX
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
