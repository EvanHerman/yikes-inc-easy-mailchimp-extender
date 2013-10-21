<?php
//Server Errors	
$errorcode['-32601'][1] = 'ServerError_MethodUnknown';
$errorcode['-32602'][1] = 'ServerError_InvalidParameters';
$errorcode['-99'][1] = 'Unknown_Exception';
$errorcode['-98'][1] = 'Request_TimedOut';
$errorcode['-92'][1] = 'Zend_Uri_Exception';
$errorcode['-91'][1] = 'PDOException';
$errorcode['-91'][1] = 'Avesta_Db_Exception';
$errorcode['-90'][1] = 'XML_RPC2_Exception';
$errorcode['-90'][1] = 'XML_RPC2_FaultException';
$errorcode['-50'][1] = 'Too_Many_Connections';
$errorcode['0'][1] = 'Parse_Exception';

$errormessage[1] = "Sorry, we can't connect to MailChimp at this time. Please come back again and try later.";

//API User or API Key error
$errorcode['100'][2] = 'User_Unknown';
$errorcode['101'][2] = 'User_Disabled';
$errorcode['102'][2] = 'User_DoesNotExist';
$errorcode['103'][2] = 'User_NotApproved';
$errorcode['104'][2] = 'Invalid_ApiKey';
$errorcode['105'][2] = 'User_UnderMaintenance';
$errorcode['106'][2] = 'Invalid_AppKey';
$errorcode['107'][2] = 'Invalid_IP';
$errorcode['108'][2] = 'User_DoesExist';
$errorcode['109'][2] = 'User_InvalidRole';
$errorcode['120'][2] = 'User_InvalidAction';
$errorcode['121'][2] = 'User_MissingEmail';
$errorcode['122'][2] = 'User_CannotSendCampaign';
$errorcode['123'][2] = 'User_MissingModuleOutbox';
$errorcode['124'][2] = 'User_ModuleAlreadyPurchased';
$errorcode['125'][2] = 'User_ModuleNotPurchased';
$errorcode['126'][2] = 'User_NotEnoughCredit';
$errorcode['127'][2] = 'MC_InvalidPayment';

$errormessage[2] = "Sorry, this MailChimp account does not exist.";

// List errors 
$errorcode['200'][3] = 'List_DoesNotExist';
$errorcode['210'][3] = 'List_InvalidInterestFieldType';
$errorcode['211'][3] = 'List_InvalidOption';
$errorcode['212'][3] = 'List_InvalidUnsubMember';
$errorcode['213'][3] = 'List_InvalidBounceMember';

$errormessage[3] = "Sorry,  this list does not exist.";

//Already subscribed or unsubscribed
$errorcode['214'][4] = 'List_AlreadySubscribed';
$errorcode['215'][4] = 'List_NotSubscribed';
$errorcode['220'][4] = 'List_InvalidImport';
$errorcode['221'][4] = 'MC_PastedList_Duplicate';
$errorcode['222'][4] = 'MC_PastedList_InvalidImport';
$errorcode['230'][4] = 'Email_AlreadySubscribed';
$errorcode['231'][4] = 'Email_AlreadyUnsubscribed';
$errorcode['232'][4] = 'Email_NotExists';
$errorcode['233'][4] = 'Email_NotSubscribed';

$errormessage[4] = "Sorry, you are already subscribed to this list.";

// General Message 
$errorcode['250'][5] = 'List_MergeFieldRequired';
$errorcode['251'][5] = 'List_CannotRemoveEmailMerge';
$errorcode['252'][5] = 'List_Merge_InvalidMergeID';
$errorcode['253'][5] = 'List_TooManyMergeFields';
$errorcode['254'][5] = 'List_InvalidMergeField';
$errorcode['270'][5] = 'List_InvalidInterestGroup';
$errorcode['271'][5] = 'List_TooManyInterestGroups';
$errorcode['300'][5] = 'Campaign_DoesNotExist';
$errorcode['301'][5] = 'Campaign_StatsNotAvailable';
$errorcode['310'][5] = 'Campaign_InvalidAbsplit';
$errorcode['311'][5] = 'Campaign_InvalidContent';
$errorcode['312'][5] = 'Campaign_InvalidOption';
$errorcode['313'][5] = 'Campaign_InvalidStatus';
$errorcode['314'][5] = 'Campaign_NotSaved';
$errorcode['315'][5] = 'Campaign_InvalidSegment';
$errorcode['316'][5] = 'Campaign_InvalidRss';
$errorcode['317'][5] = 'Campaign_InvalidAuto';
$errorcode['318'][5] = 'MC_ContentImport_InvalidArchive';
$errorcode['319'][5] = 'Campaign_BounceMissing';
$errorcode['330'][5] = 'Invalid_EcommOrder';
$errorcode['350'][5] = 'Absplit_UnknownError';
$errorcode['351'][5] = 'Absplit_UnknownSplitTest';
$errorcode['352'][5] = 'Absplit_UnknownTestType';
$errorcode['353'][5] = 'Absplit_UnknownWaitUnit';
$errorcode['354'][5] = 'Absplit_UnknownWinnerType';
$errorcode['355'][5] = 'Absplit_WinnerNotSelected';

$errormessage[5] = 'Sorry, MailChimp could not process your signup. They gave the error code';

// Validation errors
$errorcode['500'][6] = 'Invalid_Analytics';
$errorcode['503'][6] = 'Invalid_SendType';
$errorcode['504'][6] = 'Invalid_Template';
$errorcode['505'][6] = 'Invalid_TrackingOptions';
$errorcode['506'][6] = 'Invalid_Options';
$errorcode['507'][6] = 'Invalid_Folder';
$errorcode['550'][6] = 'Module_Unknown';
$errorcode['551'][6] = 'MonthlyPlan_Unknown';
$errorcode['552'][6] = 'Order_TypeUnknown';
$errorcode['553'][6] = 'Invalid_PagingLimit';
$errorcode['554'][6] = 'Invalid_PagingStart';
$errorcode['555'][6] = 'Max_Size_Reached';
$errorcode['556'][6] = 'MC_SearchException';

$errormessage[6] = "Sorry, MailChimp doesn't like the data you are trying to send. They gave the error code";

// Validate date and time field
$errorcode['501'][7] = 'Invalid_DateTimel';

$errormessage[7] = "Sorry, that date and time is invalid. Please try again.";

//Validate Email
$errorcode['502'][8] = 'Invalid_Email';

$errormessage[8] = "Sorry, that email address is invalid. Please try again.";

// Validate URL fields
$errorcode['508'][9] = 'Invalid_URL';

$errormessage[9] = "Sorry, that URL is invalid. Please try again.";

// Get error message

foreach ($errorCode as $value )
	{
	if (key($errorCode) == $error)
		{
		foreach ($value as $mssg => $key)
			{
				$Message = $errormessage[$key];
				$Message .= '<br /><strong>Error Code: '.$error.' - '.$mssg.'</strong><br />';
				$Message .= 'For more help <a href="http://apidocs.mailchimp.com/api/1.3/exceptions.field.php">visit the MailChimp website</a> or <a href="http://kb.mailchimp.com/home">contact MailChimp</a>"';	
			}			
		}
	}	
	return $Message;
}
?>