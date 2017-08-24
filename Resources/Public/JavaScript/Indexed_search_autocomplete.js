jQuery(document).ready(function(){
    if (jQuery('.search').length > 0) {
        initIndexSearchAutocomplete();
    }
});

function initIndexSearchAutocomplete() {
    jQuery('.search').on('keyup change', function() {
        $('.search-autocomplete-results').html('');
        var val = $(this).val();
        
        if (val.length < 3) {
            return;
        }
        
        
        var url = $('#autocompleteurl').attr('href');
        
        $.ajax({
            url: url,
            cache: false,
            method: 'POST',
            data: {
                s: val
            },
            success: function (data) {
               $('.search-autocomplete-results').html(data);
            }
        });
    });
}