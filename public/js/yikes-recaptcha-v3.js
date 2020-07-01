/**
 * Recaptcha Version 3
 */

document.addEventListener( 'DOMContentLoaded', function() {
    grecaptcha.ready(function () {
        grecaptcha.execute( yikesRecaptcha.siteKey, { action: 'mailchimp' } ).then( function ( token ) {
            var recaptchaResponse = document.getElementById( 'recaptcha_three_response' );
            recaptchaResponse.value = token;
        } );
    } );
} );
