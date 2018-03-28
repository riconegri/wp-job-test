// Declare our JQuery Alias
jQuery( 'document' ).ready( function( $ ) {
    // console.log(ajax_url);

    // Form submission listener
    jQuery( '#um_form' ).submit( function(e) {
        e.preventDefault();

        var um_val = jQuery( '#search-woo-account' ).val();

        // Do very simple value validation
        if( um_val.length ) {
            jQuery.ajax( {
                responseType: 'json',
                url : ajax_url,                 // Use our localized variable that holds the AJAX URL
                type: 'POST', // Declare our ajax submission method ( GET or POST )
                data: {                         // This is our data object
                    action  : 'um_cb',          // AJAX POST Action
                    'woo-account-search': um_val       // Replace `um_key` with your user_meta key name
                }
            } )
                .success( function( results ) {
                    console.log(results.fragment.element);
                    jQuery( '.' + results.fragment.element ).replaceWith( results.fragment.content );
                    // console.log( 'User Meta Updated!', results );
                } )
                .fail( function( data ) {
                    console.log( data.responseText );
                    console.log( 'Request failed: ' + data.statusText );
                } );

        } else {
            // Show user error message.
        }

        return false;   // Stop our form from submitting
    } );
} );