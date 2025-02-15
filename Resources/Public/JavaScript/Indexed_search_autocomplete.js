jQuery(document).ready(function () {
    // Init
    new IndexSearchAutoComplete();
});
function IndexSearchAutoComplete() {
    var debounceTimeout = null; // Used to reduce the amount of queries
    var lastSearchQuery = ''; // Used to reduce the amount of queries

    // Check whether there is an input box to apply the autocomplete to
    if (jQuery('input.search, input.tx-indexedsearch-searchbox-sword, input.indexed-search-atocomplete-sword, input.indexed-search-autocomplete-sword').length == 0)
        return;

    // Initialise the autocomplete
    var that = this;
    jQuery('input.search, input.tx-indexedsearch-searchbox-sword, input.indexed-search-atocomplete-sword, input.indexed-search-autocomplete-sword')
        .on('keypress keyup', function (e) {
            that.autocomplete(e, this);
        }).attr('autocomplete', 'off');

    // When a click is performed somewhere on the page, remove the autocomplete-box
    jQuery(document).on("click", function (event) {
        var targetClass = '.search-autocomplete-results';

        if (!jQuery(event.target).hasClass(targetClass)) {
            jQuery(targetClass).html('').hide().removeClass('results').addClass('no-results');
        }
    });
}

/**
 * Autocomplete a query
 *
 * @param e jQuery-Event which gets fired in case of a keypress
 */
IndexSearchAutoComplete.prototype.autocomplete = function(e, ref) {
    var $input = jQuery(ref);
    var $elem = jQuery(ref);
    var $results;

    // Find the corresponding div for the results
    var cnt = 0;
    while ($elem.prop("tagName") !== 'HTML') {
        $results = $elem.find('.search-autocomplete-results');
        if ($results.length > 0) {
            break;
        }
        $elem = $elem.parent();
    }
    if ($elem.prop("tagName") === 'HTML') {
        console.log("we couldn't find a result div (.search-autocomplete-results)");
        return;
    }

    // Retrieve options
    var mode = typeof $results.data('mode') === 'undefined' ? 'word' : $results.data('mode');
    var soc = $results.data('searchonclick') == true;

    // navigate through the suggestions/results
    if (e.which === 38 || e.which === 40 || e.keyCode === 10 || e.keyCode === 13) { // up / down / enter

        if (e.which === 38 && e.type === 'keyup') { // up
            var $prev = $results.find('li.highlighted').prev();

            if ($results.find('li.highlighted').length === 0 || $prev.length === 0) {
                $results.find('li.highlighted').removeClass('highlighted');
                $results.find('li').last().addClass('highlighted');
                return;
            }

            $results.find('li.highlighted').removeClass('highlighted');
            $prev.addClass('highlighted');
        }

        if (e.which === 40 && e.type === 'keyup') { // down
            var $next = $results.find('li.highlighted').next();
            if ($results.find('li.highlighted').length === 0 || $next.length === 0) {
                $results.find('li.highlighted').removeClass('highlighted');
                $results.find('li').first().addClass('highlighted');
                return;
            }

            $results.find('li.highlighted').removeClass('highlighted');
            $next.addClass('highlighted');
        }

        if ((e.keyCode === 10 || e.keyCode === 13) && e.type === 'keypress') { // enter
            if ($results.is(':visible') && $results.find('li.highlighted').length > 0) {
                if (mode === 'word') {
                    $results.find('li.highlighted').click();

                    // Search on click
                    if (soc) {
                        $input.closest('form').submit();
                    }
                } else {
                    window.location = $results.find('li.highlighted a.navigate-on-enter').attr('href');
                }
                e.preventDefault();
            }
        }

        return;
    }

    // Catch left / right arrow keys
    if (e.keyCode === 37 || e.keyCode === 39)
        return;

    // Do only start a query if a key is released to save querys
    if (e.type !== 'keyup')
        return;

    // Empty the results
    $results.html('').hide().removeClass('results').addClass('no-results');

    // Retrieve the query
    var val = jQuery(ref).val().trim();
    var minlen = typeof $results.data('minlength') === 'undefined' ? 3 : $results.data('minlength');
    var maxResults = typeof $results.data('maxresults') === 'undefined' ? 10 : $results.data('maxresults');

    // Check if the query is long enough
    if (val.length < minlen)
        return;

    // Check whether the search term changed
    if (val == this.lastSearchQuery)
        return;

    // Set the old query value
    this.lastSearchQuery = val;

    // tell the user the search is running
    $results.addClass('autocomplete_searching');

    // Perform the query
    this.performQuery(val, mode, maxResults, $results, $input);
}


IndexSearchAutoComplete.prototype.performQuery = function(val, mode, maxResults, $results, $input) {
    var soc = $results.data('searchonclick') === true;
    // Debounce
    clearTimeout(this.debounceTimeout);
    this.debounceTimeout = setTimeout(function() {
        clearTimeout(this.debounceTimeout);

        // Execute the query
        jQuery.ajax({
            url: $results.data('searchurl'),
            cache: false,
            method: 'POST',
            data: {
                s: val,
                m: mode,
                mr: maxResults
            },
            success: function (data) {

                // Insert the results
                $li = $results
                    .show()
                    .html(data)
                    .removeClass('autocomplete_searching')
                    .find('li');

                // Add a click action
                $li.click(function () {
                    if (mode === 'word') {
                        $input.val(jQuery(this).text().trim());
                        $results.html('').hide();

                        if (soc) {
                            $input.closest('form').submit();
                        }
                    } else {
                        window.location = $li.find('a.navigate-on-enter').attr('href');
                    }
                });

                // Check if there are results and update the FE depending on it
                if ($li.length == 0) {

                    // No results
                    $results.html('').hide();
                    $results.removeClass('results').addClass('no-results');
                } else {

                    // Results
                    $results.removeClass('no-results').addClass('results');
                }
            }
        });
    }, 250);
}
