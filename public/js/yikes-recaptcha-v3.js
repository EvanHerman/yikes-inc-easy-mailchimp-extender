/**
 * Recaptcha Version 3
 */
function yikesRecaptchaV3( siteKey, passedFunc ) {
    if ( typeof grecaptcha !== 'function' ) {
        console.error( 'grecaptcha not defined.' );
        return;
    }
    if ( ! siteKey ) {
        console.error( 'No site key passed' );
        return;
    }
    grecaptcha.ready( function() {
      grecaptcha.execute( siteKey, { action: 'submit' } ).then(function(token) {
          return passedFunc();
      } );
    } );
}
