<?php

	/* Main Template for generating a new field to attach to a list */
	
	$api	= new Mailchimp($this->optionVal['api-key']);
	parse_str( $p['form_data'], $formData );
	$list_id	= $formData['mc-list-id'];
	$field_name = $formData['add-field-field-name'];
	$required = $formData['add-field-field-required'];	
	$public = isset( $formData['add-field-public'] ) ? $formData['add-field-public'] : '';
	$merge_tag = $formData['add-field-field-merge-tag'];
	$field_type = $p['field_type'];
	$field_default_value = $formData['add-field-default-value'];
	
	if( $field_type == 'radio' || $field_type == 'dropdown' ) {
		try {
			$retval = $api->call('lists/merge-var-add', array(
				'id'              => $list_id, // list id to connect too
				'tag'             => $merge_tag, // merge variable name e.g. FNAME. Valid A-Z 0-9 _ no spaces, dashes etc.
				'name'        => $field_name, // Name of field
				'options'	=> array(
					'choices' => $formData['radio-dropdown-option'],
					'field_type' => $field_type,
					'req' => $required,
					'public' => $public
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
	} else if( $field_type == 'date' || $field_type == 'birthday' ) {
		try {
			$retval = $api->call('lists/merge-var-add', array(
				'id'              => $list_id, // list id to connect too
				'tag'             => $merge_tag, // merge variable name e.g. FNAME. Valid A-Z 0-9 _ no spaces, dashes etc.
				'name'        => $field_name, // Name of field
				'options'	=> array(
					'field_type' => $field_type,
					'req' => $required,
					'public' => $public,
					'dateformat' => $formData['add-field-dateformat']
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
	} else if( $field_type == 'phone' ) {
		try {
			$retval = $api->call('lists/merge-var-add', array(
				'id'              => $list_id, // list id to connect too
				'tag'             => $merge_tag, // merge variable name e.g. FNAME. Valid A-Z 0-9 _ no spaces, dashes etc.
				'name'        => $field_name, // Name of field
				'options'	=> array(
					'field_type' => $field_type,
					'req' => $required,
					'public' => $public,
					'phoneformat' => $formData['add-field-phoneformat']
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
	} else if ($field_type == 'text' ) {
		try {
			$retval = $api->call('lists/merge-var-add', array(
				'id'              => $list_id, // list id to connect too
				'tag'             => $merge_tag, // merge variable name e.g. FNAME. Valid A-Z 0-9 _ no spaces, dashes etc.
				'name'        => $field_name, // Name of field
				'options'	=> array(
					'field_type' => $field_type,
					'req' => $required,
					'public' => $public,
					'default_value' => $field_default_value
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
	} else {
		try {
			$retval = $api->call('lists/merge-var-add', array(
				'id'              => $list_id, // list id to connect too
				'tag'             => $merge_tag, // merge variable name e.g. FNAME. Valid A-Z 0-9 _ no spaces, dashes etc.
				'name'        => $field_name, // Name of field
				'options'	=> array(
					'field_type' => $field_type,
					'req' => $required,
					'public' => $public
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
	}