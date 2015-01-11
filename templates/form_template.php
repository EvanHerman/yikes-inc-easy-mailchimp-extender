<div class="yks-mailchimpFormContainerInner" id="yks-mailchimpFormContainerInner_<?php echo $list['id']; ?>">	
		<?php if( isset( $this->optionVal['yks-mailchimp-required-text'] ) ) { 
						if ( $this->optionVal['yks-mailchimp-required-text'] == '1' ) { ?>
			<div class="yks-require-description">
				<span class='yks-required-label'>*</span> = <?php _e('required field','yikes-inc-easy-mailchimp-extender'); ?>
			</div>
		<?php } } ?>
		<form method="post" name="yks-mailchimp-form" id="yks-mailchimp-form_<?php echo $list['id']; ?>" rel="<?php echo $list['id']; ?>">
			<input type="hidden" name="yks-mailchimp-list-ct" id="yks-mailchimp-list-ct_<?php echo $list['id']; ?>" value="<?php echo $listCt; ?>" />
			<input type="hidden" name="yks-mailchimp-list-id" id="yks-mailchimp-list-id_<?php echo $list['id']; ?>" value="<?php echo $list['list-id']; ?>" />
			<?php echo $this->getFrontendFormDisplay($list, $submit_text); ?>
		</form>
	</div>