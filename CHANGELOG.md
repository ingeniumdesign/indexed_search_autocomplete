# IndexedSearchAutocomplete Version 1.0.6

## FEATURE
- Supports Typo3 9
- Request to the server becomes sent after that user has stopped typing for 250ms. This reduces the amount of requests sent.
##FIX
- rootPidList-Parameter of the indexed_search-Extension gets used in word&link-mode  

# IndexedSearchAutocomplete Version 1.0.5

## FEATURE
- If the search-suggestion-div is opened now you can close it by clicking somewhere else


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
