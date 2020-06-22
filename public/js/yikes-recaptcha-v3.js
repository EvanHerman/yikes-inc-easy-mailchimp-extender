/**
 * Recaptcha Version 3
 */
function yikesRecaptchaV3( passedFunc ) {
    if ( typeof window.yikesGoogleRecaptchaV3 === 'undefined' ) {
        console.error( 'Localization failed for v3 recaptcha.' );
        return;
    }
    if ( ! window.yikesGoogleRecaptchaV3.siteKey ) {
        console.error( 'No site key passed' );
        return;
    }
    grecaptcha.ready( function() {
      grecaptcha.execute( window.yikesGoogleRecaptchaV3.siteKey, { action: 'submit' } ).then(function( token ) {
          console.log( 'Recaptcha verification passed.' );
          return passedFunc();
      } );
    } );
}
