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
        
        $.ajax({
            url: $('.search-autocomplete-results').data('searchurl'),
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