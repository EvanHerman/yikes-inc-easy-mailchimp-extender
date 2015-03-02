<?php

	/* Main template file which updates a list on a given field */

	parse_str( $p['form_data'], $formData );
	$list_id	= $formData['mc-list-id'];
	$old_merge_tag_name = $formData['old-merge-tag'];
	$new_merge_tag = $formData['add-field-field-merge-tag'];
	$field_name = $formData['add-field-field-name'];
	$field_required = $formData['update-field-field-required'];
	$field_type = $p['field_type'];
	$field_default_value = $formData['add-field-default-value'];
	
	$api	= new Mailchimp($this->optionVal['api-key']);
	
	if ( $field_type == 'dropdown' || $field_type == 'radio' ) {
		try {
			$retval = $api->call('lists/merge-var-update', array(
				'id'              => $list_id, // list id to connect too
				'tag'             => $old_merge_tag_name, // merge variable name e.g. FNAME. Valid A-Z 0-9 _ no spaces, dashes etc.
				'options'	=> array(
					'name' => $field_name,
					'choices' => $formData['radio-dropdown-option'],
					'req' => $field_required,
					'tag' => $new_merge_tag
				), 
			));
			return "done";
		} catch( Exception $e ) { // catch any errors returned from MailChimp
			$errorMessage = $e->getMessage();
			echo $errorMessage;
			// write our error to the error log,
			// when advanced debug is enabled
			if ( $this->optionVal['debug'] == 1 ) {
					$this->writeErrorToErrorLog( $e );
				}
			die();
		}
	} else if( $field_type == 'text' ) {
		try {
			$retval = $api->call('lists/merge-var-update', array(
				'id'              => $list_id, // list id to delete merge tag from
				'tag'             => $old_merge_tag_name, // merge tag to be delete
				'options' => array(
					'name' => $field_name,
					'req' => $field_required,
					'tag' => $new_merge_tag,
					'default_value' => $field_default_value
				)
			));
			return "done";
		} catch( Exception $e ) { // catch any errors returned from MailChimp
			$errorMessage = $e->getMessage();
			echo $errorMessage;
			// write our error to the error log,
			// when advanced debug is enabled
			if ( $this->optionVal['debug'] == 1 ) {
					$this->writeErrorToErrorLog( $e );
				}
			die();
		}
	} else {
		try {
			$retval = $api->call('lists/merge-var-update', array(
				'id'              => $list_id, // list id to delete merge tag from
				'tag'             => $old_merge_tag_name, // merge tag to be delete
				'options' => array(
					'name' => $field_name,
					'req' => $field_required,
					'tag' => $new_merge_tag
				)
			));
			return "done";
		} catch( Exception $e ) { // catch any errors returned from MailChimp
			$errorMessage = $e->getMessage();
			echo $errorMessage;
			// write our error to the error log,
			// when advanced debug is enabled
			if ( $this->optionVal['debug'] == 1 ) {
					$this->writeErrorToErrorLog( $e );
				}
			die();
		}
	}

?>