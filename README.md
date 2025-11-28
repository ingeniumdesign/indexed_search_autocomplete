# Indexed Search Autocomplete

## TYPO3 Extension `indexed_search_autocomplete`

[![TYPO3 8](https://img.shields.io/badge/TYPO3-8-red.svg)](https://get.typo3.org/version/8)
[![TYPO3 9](https://img.shields.io/badge/TYPO3-9-red.svg)](https://get.typo3.org/version/9)
[![TYPO3 10](https://img.shields.io/badge/TYPO3-10-red.svg)](https://get.typo3.org/version/10)
[![TYPO3 11](https://img.shields.io/badge/TYPO3-11-red.svg)](https://get.typo3.org/version/11)
[![TYPO3 12](https://img.shields.io/badge/TYPO3-12-green.svg)](https://get.typo3.org/version/12)
[![TYPO3 13](https://img.shields.io/badge/TYPO3-13-green.svg)](https://get.typo3.org/version/13)
[![TYPO3 14](https://img.shields.io/badge/TYPO3-14-red.svg)](https://get.typo3.org/version/14)
[![Donate](https://img.shields.io/badge/Donate-PayPal-yellow.svg)](https://www.paypal.me/INGENIUMDESIGN/)
[![Latest Stable Version](https://poser.pugx.org/id/indexed-search-autocomplete/v/stable)](https://packagist.org/packages/id/indexed-search-autocomplete)
[![Monthly Downloads](https://poser.pugx.org/id/indexed-search-autocomplete/d/monthly)](https://packagist.org/packages/id/indexed-search-autocomplete)
[![License](https://poser.pugx.org/id/indexed-search-autocomplete/license)](https://packagist.org/packages/id/indexed-search-autocomplete)

Extends the TYPO3 Core Extension Indexed_Search searchform with an autocomplete feature.

## Minimal Dependencies

* TYPO3 CMS 8.7.x - 13.4.x
* PHP 7.x - 8.x
* ~~Jquery 1.x~~ (TYPO3 8.7.x - 12.4.x)

# Quick Install Guide

### Install the TYPO3 Core indexed_search extensions

**Step 1:** The extension works with the TYPO3 Core indexed_search extension. So please install and configure this one first. Check final, the "Word Index"  works fine!

**Step 2:** Install this plugin.

#### Installation using Composer

The recommended way to install the extension is using [Composer][1].

Run the following command within your Composer based TYPO3 project:

```
composer require id/indexed-search-autocomplete
```

#### Installation as extension from TYPO3 Extension Repository (TER)

Download and install the [extension][2] with the extension manager module.

**Step 3:** Include the static TypoScript of the extension in `Site Management > Typoscript > Edit the whole TypoScript record > Advanced Options > Include TypoScript sets: Indexed Search Autocomplete (indexed_search_autocomplete)`

**Step 4:** Outsource from the EXT:indexed_search the `Partials/Form.html` Template. Like this:
```typoscript
plugin {
  tx_indexedsearch {
    view {
      templateRootPath = fileadmin/Resources/Plugin/IndexedSearch/Private/Templates/
      partialRootPath = fileadmin/Resources/Plugin/IndexedSearch/Private/Partials/
      layoutRootPath = fileadmin/Resources/Plugin/IndexedSearch/Private/Layouts/
    }
  }
}
```

**Step 5:** Find the fluid file / code line that contains the text-input for the search-word.

**Step 6:** Add the class `'.indexed-search-autocomplete-sword'` to this text-input. Example:
```html
<f:form.textfield name="search[sword]" value="{sword}" id="tx-indexedsearch-searchbox-sword" class="tx-indexedsearch-searchbox-sword indexed-search-autocomplete-sword" />
```

**Step 7:** Now add the following line where you want the results to be displayed (_so in most of the cases below the text-input_):

**TYPO3 13.x:**
```html
<div class="search-autocomplete-results no-results" data-mode="word" data-searchonclick="false" data-maxresults="10" data-minlength="2" data-searchurl="{f:uri.action(action: 'search', pageType: '7423794', noCache: 1, extensionName: 'indexedSearchAutocomplete', controller: 'Search')}"></div>
```

**Form.html Example TYPO3 v13:**
```html
<div class="tx-indexedsearch-form">
  <label for="tx-indexedsearch-searchbox-sword"><f:translate key="form.searchFor" />:</label>
  <f:form.textfield name="search[sword]" value="{sword}" id="tx-indexedsearch-searchbox-sword" class="tx-indexedsearch-searchbox-sword indexed-search-autocomplete-sword" />
  <div class="search-autocomplete-results no-results" data-mode="word" data-searchonclick="false" data-maxresults="10" data-minlength="2" data-searchurl="{f:uri.action(action: 'search', pageType: '7423794', noCache: 1, extensionName: 'indexedSearchAutocomplete', controller: 'Search')}"></div>
</div>
```

**Step 8:** Now you can configure the plugins options with the parameters of that `<div>` (see options)

**Step 9:** TYPO3 Site-Config add the new PAGE typeNum `7423794`, here a Example: 
```yaml
routeEnhancers:
  PageTypeSuffix:
   type: PageType
   default: /
   index: ''
   map:
     /: 0
     sitemap.xml: 500001
     autocomplete: 7423794
```
**Additional:** Make sure to disable Indexed-Search option "_Use MySQL specific fulltext search_", otherwise the word-suggestion won't work!

### Options

* ~~Enable or Disable~~ the **JQuery-Source** in the Extension Settings. (Backend -> Extension-Settings) => (TYPO3 8.7.x - 12.4.x)
  ```page.includeJSFooterlibs.JquerySource = ```
* **data-mode="word"** => the following values are possible: `word` or `link`. Depending on which option you choose, the plugin will suggest either words or links as you type. You can edit both template files at indexed_search_autocomplete/Resources/Private/Partials/ (Fluid)
* **data-maxresults="10"** => The maximum number of entries per suggestion (Fluid)
* **data-minlength="2"** => How many characters must be in the input field for the plugin to make it's first suggestion (Fluid)
* **data-searchonclick="false"** => If a suggestion is selected, should this submit the form (so basically the search starts after selecting a word). Possible values are "false" or "true". 

# Contact &amp; Communication

## Working / Testing Developer Example:

**TYPO3 11:** https://t11.baukasten-typo3.de/ <br />
**TYPO3 12:** https://t12.baukasten-typo3.de/ <br />
**TYPO3 13:** https://t13.baukasten-typo3.de/

## GIT

We are on github:<br />
https://github.com/ingeniumdesign/indexed_search_autocomplete/


## Agency

INGENIUMDESIGN<br />
TYPO3 - Agentur<br />
65510 Idstein<br />
<br />
https://www.ingeniumdesign.de/ <br />
info@ingeniumdesign.de

## Donate

Amazon: https://www.amazon.de/hz/wishlist/ls/13RT2BFNRP05 <br />
PayPay: www.paypal.me/INGENIUMDESIGN/

### Sponsoring

https://www.ingeniumdesign.de/ - INGENIUMDESIGN<br />
https://www.agenturamwasser.ch/ - Agentur am Wasser GmbH<br />
https://www.baukasten-typo3.de/ - INGENIUMDESIGN<br />
https://www.smallprime.ch/ - Agentur am Wasser GmbH<br />
https://www.takeoffmedia.de/ - Takeoff-Media GmbH<br />
https://ead.darmstadt.de/ - Eigenbetrieb für kommunale Aufgaben und Dienstleistungen (EAD)<br />
https://www.easy-sprachreisen.de/ - Sebastian Ernst & Petra Wagner GbR<br />


## Downloads

TYPO3 TER: https://extensions.typo3.org/extension/indexed_search_autocomplete <br />
Composer: https://packagist.org/packages/id/indexed-search-autocomplete

## Used by

We are searching for Live-Examples and for Sponsoring for the TYPO3 indexed_search Autocomplete Extension.<br />
Please be so kind to send us an E-Mail if you're using it. Thanks!

**Links/References:**

https://www.ingeniumdesign.de/ - by INGENIUMDESIGN<br />
https://www.baukasten-typo3.de/ - by INGENIUMDESIGN<br />
https://www.takeoffmedia.de/ - by INGENIUMDESIGN<br />
https://ead.darmstadt.de/ - by INGENIUMDESIGN<br />
https://www.easy-sprachreisen.de/ - by INGENIUMDESIGN

[1]: https://packagist.org/packages/id/indexed-search-autocomplete
[2]: https://extensions.typo3.org/extension/indexed_search_autocomplete
