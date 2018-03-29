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
jQuery('document').ready(function ($) {
    const button = $(this).find('.woo_foo_btn')[0];

    $('#search-woo-account').on('keyup', function (e) {
        if ($('#search-woo-account').val().trim()) {
            button.disabled = false;
        } else {
            button.disabled = true;
        }
        console.log($('#search-woo-account').val())
    });
    // Form submission listener
    $('#um_form').submit(function (e) {
        e.preventDefault();

        var um_val = $('#search-woo-account').val();
        button.disabled = true;
        button.innerText = 'Await';

        // Do very simple value validation
        if (um_val.length && um_val.trim()) {
            $.ajax({
                responseType: 'json',
                url: ajax_url,
                type: 'POST',
                data: {
                    action: 'woo_foo',
                    'woo-account-search': um_val
                }
            })
                .success(function (results) {
                    button.disabled = false;
                    button.innerText = 'Save!';
                    $('.' + results.fragment.element).replaceWith(results.fragment.content);
                    // console.log( 'User Meta Updated!', results );
                })
                .fail(function (data) {
                    console.log(data);
                    console.log('Request failed: ' + data.statusText);
                });

        } else {
            // Show user error message.
            button.innerText = 'Noo!';
            setTimeout(function () {
                button.disabled = false;
                button.innerText = 'Save!';
            }, 2000);

        }

        return false;   // Stop our form from submitting
    });
});

