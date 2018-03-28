(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
    // Declare our JQuery Alias
    jQuery( 'document' ).ready( function( $ ) {
        // console.log(ajax_url);

        // Form submission listener
        $( '#um_form' ).submit( function(e) {
            e.preventDefault();

            var um_val = $( '#search-woo-account' ).val();

            // Do very simple value validation
            if( um_val.length ) {
                $.ajax( {
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
                        $( '.' + results.fragment.element ).replaceWith( results.fragment.content );
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

})( jQuery );
