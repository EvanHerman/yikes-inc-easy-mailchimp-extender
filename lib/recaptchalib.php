<?php
	/* All New  No-Captcha Re-Captcha Google Library */

	wp_register_script( 'no-captcha-recaptcha-library' , 'https://www.google.com/recaptcha/api.js' , array( 'jquery' ) , 'all' );
	wp_enqueue_script( 'no-captcha-recaptcha-library' );
?>
	
    <form action="?" method="POST">
      <div class="g-recaptcha" data-sitekey="<?php echo $this->optionVal['recaptcha-api-key']; ?>"></div>
    </form>
