<?php

// create our language variable dependent on what is set in MailChimp
			if(!empty($subscriber_data['language'])) {	
				$subscriber_data_language =  $subscriber_data['language']; 
					if ( $subscriber_data_language == 'en' ) {
						$subscriber_data_language = 'English';
					} elseif ( $subscriber_data_language == 'ar' ) {
						$subscriber_data_language = 'Arabic';
					} elseif ( $subscriber_data_language == 'af' ) {
						$subscriber_data_language = 'Afrikaans';
					} elseif ( $subscriber_data_language == 'be' ) {
						$subscriber_data_language = 'Belarusian';
					} elseif ( $subscriber_data_language == 'bg' ) {
						$subscriber_data_language = 'Bulgarian';
					} elseif ( $subscriber_data_language == 'ca' ) {
						$subscriber_data_language = 'Catalan';
					} elseif ( $subscriber_data_language == 'zh' ) {
						$subscriber_data_language = 'Chinese';
					} elseif ( $subscriber_data_language == 'hr' ) {
						$subscriber_data_language = 'Croatian';
					} elseif ( $subscriber_data_language == 'cs' ) {
						$subscriber_data_language = 'Czech';
					} elseif ( $subscriber_data_language == 'da' ) {
						$subscriber_data_language = 'Danish';
					} elseif ( $subscriber_data_language == 'nl' ) {
						$subscriber_data_language = 'Dutch';
					} elseif ( $subscriber_data_language == 'et' ) {
						$subscriber_data_language = 'Estonian';
					} elseif ( $subscriber_data_language == 'fa' ) {
						$subscriber_data_language = 'Farsi';
					} elseif ( $subscriber_data_language == 'fi' ) {
						$subscriber_data_language = 'Finnish';
					} elseif ( $subscriber_data_language == 'fr' ) {
						$subscriber_data_language = 'French';
					} elseif ( $subscriber_data_language == 'fr_CA' ) {
						$subscriber_data_language = 'French (Canada)';
					} elseif ( $subscriber_data_language == 'de' ) {
						$subscriber_data_language = 'German';
					} elseif ( $subscriber_data_language == 'el' ) {
						$subscriber_data_language = 'Greek';
					} elseif ( $subscriber_data_language == 'he' ) {
						$subscriber_data_language = 'Hebrew';
					} elseif ( $subscriber_data_language == 'hi' ) {
						$subscriber_data_language = 'Hindi';
					} elseif ( $subscriber_data_language == 'hu' ) {
						$subscriber_data_language = 'Hungarian';
					} elseif ( $subscriber_data_language == 'is' ) {
						$subscriber_data_language = 'Icelandic';
					} elseif ( $subscriber_data_language == 'id' ) {
						$subscriber_data_language = 'Indonesian';
					} elseif ( $subscriber_data_language == 'ga' ) {
						$subscriber_data_language = 'Irish';
					} elseif ( $subscriber_data_language == 'it' ) {
						$subscriber_data_language = 'Italian';
					} elseif ( $subscriber_data_language == 'ja' ) {
						$subscriber_data_language = 'Japanese';
					} elseif ( $subscriber_data_language == 'km' ) {
						$subscriber_data_language = 'Khmer';
					} elseif ( $subscriber_data_language == 'ko' ) {
						$subscriber_data_language = 'Korean';
					} elseif ( $subscriber_data_language == 'lv' ) {
						$subscriber_data_language = 'Latvian';
					} elseif ( $subscriber_data_language == 'lt' ) {
						$subscriber_data_language = 'Lithuanian';
					} elseif ( $subscriber_data_language == 'mt' ) {
						$subscriber_data_language = 'Maltese';
					} elseif ( $subscriber_data_language == 'ms' ) {
						$subscriber_data_language = 'Malay';
					} elseif ( $subscriber_data_language == 'mk' ) {
						$subscriber_data_language = 'Macedonian';
					} elseif ( $subscriber_data_language == 'no' ) {
						$subscriber_data_language = 'Norwegian';
					} elseif ( $subscriber_data_language == 'pl' ) {
						$subscriber_data_language = 'Polish';
					} elseif ( $subscriber_data_language == 'pt' ) {
						$subscriber_data_language = 'Portuguese (Brazil)';
					} elseif ( $subscriber_data_language == 'pt_PT' ) {
						$subscriber_data_language = 'Portuguese (Portugal)';
					} elseif ( $subscriber_data_language == 'ro' ) {
						$subscriber_data_language = 'Romanian';
					} elseif ( $subscriber_data_language == 'ru' ) {
						$subscriber_data_language = 'Russian';
					} elseif ( $subscriber_data_language == 'sr' ) {
						$subscriber_data_language = 'Serbian';
					} elseif ( $subscriber_data_language == 'sl' ) {
						$subscriber_data_language = 'Slovak';
					} elseif ( $subscriber_data_language == 'sl' ) {
						$subscriber_data_language = 'Slovenian';
					} elseif ( $subscriber_data_language == 'es' ) {
						$subscriber_data_language = 'Spanish (Mexico)';
					} elseif ( $subscriber_data_language == 'es_ES' ) {
						$subscriber_data_language = 'Spanish (Spain)';
					} elseif ( $subscriber_data_language == 'sw' ) {
						$subscriber_data_language = 'Swahili';
					} elseif ( $subscriber_data_language == 'sv' ) {
						$subscriber_data_language = 'Swedish';
					} elseif ( $subscriber_data_language == 'ta' ) {
						$subscriber_data_language = 'Tamil';
					} elseif ( $subscriber_data_language == 'th' ) {
						$subscriber_data_language = 'Thai';
					} elseif ( $subscriber_data_language == 'tr' ) {
						$subscriber_data_language = 'Turkish';
					} elseif ( $subscriber_data_language == 'uk' ) {
						$subscriber_data_language = 'Ukrainian';
					} elseif ( $subscriber_data_language == 'vi' ) {
						$subscriber_data_language = 'Vietnamese';
					}
			} else { 
				$subscriber_data_language = 'Not Set.'; 
			}