<?php

// General Message "Sorry, MailChimp could not process your signup. They gave the error code <strong>[error]</strong> For more help <a href="http://apidocs.mailchimp.com/api/1.3/exceptions.field.php">visit the MailChimp website</a> or <a href="http://kb.mailchimp.com/home">contact MailChimp</a>"

//Server Errors
// Message "Sorry, we can't connect to MailChimp at this time. Please come back again and try later."
$errorcode['-32601'] = 'ServerError_MethodUnknown';
$errorcode['-32602'] = 'ServerError_InvalidParameters';
$errorcode['-99'] = 'Unknown_Exception';
$errorcode['-98'] = 'Request_TimedOut';
$errorcode['-92'] = 'Zend_Uri_Exception';
$errorcode['-91'] = 'PDOException';
$errorcode['-91'] = 'Avesta_Db_Exception';
$errorcode['-90'] = 'XML_RPC2_Exception';
$errorcode['-90'] = 'XML_RPC2_FaultException';
$errorcode['-50'] = 'Too_Many_Connections';
$errorcode['0'] = 'Parse_Exception';

//API User or API Key error
// Message "Sorry, this MailChimp account does not exist."
$errorcode['100'] = 'User_Unknown';
$errorcode['101'] = 'User_Disabled';
$errorcode['102'] = 'User_DoesNotExist';
$errorcode['103'] = 'User_NotApproved';
$errorcode['104'] = 'Invalid_ApiKey';
$errorcode['105'] = 'User_UnderMaintenance';
$errorcode['106'] = 'Invalid_AppKey';
$errorcode['107'] = 'Invalid_IP';
$errorcode['108'] = 'User_DoesExist';
$errorcode['109'] = 'User_InvalidRole';
$errorcode['120'] = 'User_InvalidAction';
$errorcode['121'] = 'User_MissingEmail';
$errorcode['122'] = 'User_CannotSendCampaign';
$errorcode['123'] = 'User_MissingModuleOutbox';
$errorcode['124'] = 'User_ModuleAlreadyPurchased';
$errorcode['125'] = 'User_ModuleNotPurchased';
$errorcode['126'] = 'User_NotEnoughCredit';
$errorcode['127'] = 'MC_InvalidPayment';

// List errors
// Message "Sorry,  this list does not exist."
$errorcode['200'] = 'List_DoesNotExist';
$errorcode['210'] = 'List_InvalidInterestFieldType';
$errorcode['211'] = 'List_InvalidOption';
$errorcode['212'] = 'List_InvalidUnsubMember';
$errorcode['213'] = 'List_InvalidBounceMember';

//Already subscribed or unsubscribed

// Message "Sorry, you are already subscribed to this list."
$errorcode['214'] = 'List_AlreadySubscribed';
$errorcode['215'] = 'List_NotSubscribed';
$errorcode['220'] = 'List_InvalidImport';
$errorcode['221'] = 'MC_PastedList_Duplicate';
$errorcode['222'] = 'MC_PastedList_InvalidImport';
$errorcode['230'] = 'Email_AlreadySubscribed';
$errorcode['231'] = 'Email_AlreadyUnsubscribed';
$errorcode['232'] = 'Email_NotExists';
$errorcode['233'] = 'Email_NotSubscribed';

$errorcode['250'] = 'List_MergeFieldRequired';
$errorcode['251'] = 'List_CannotRemoveEmailMerge';
$errorcode['252'] = 'List_Merge_InvalidMergeID';
$errorcode['253'] = 'List_TooManyMergeFields';
$errorcode['254'] = 'List_InvalidMergeField';
$errorcode['270'] = 'List_InvalidInterestGroup';
$errorcode['271'] = 'List_TooManyInterestGroups';
$errorcode['300'] = 'Campaign_DoesNotExist';
$errorcode['301'] = 'Campaign_StatsNotAvailable';
$errorcode['310'] = 'Campaign_InvalidAbsplit';
$errorcode['311'] = 'Campaign_InvalidContent';
$errorcode['312'] = 'Campaign_InvalidOption';
$errorcode['313'] = 'Campaign_InvalidStatus';
$errorcode['314'] = 'Campaign_NotSaved';
$errorcode['315'] = 'Campaign_InvalidSegment';
$errorcode['316'] = 'Campaign_InvalidRss';
$errorcode['317'] = 'Campaign_InvalidAuto';
$errorcode['318'] = 'MC_ContentImport_InvalidArchive';
$errorcode['319'] = 'Campaign_BounceMissing';
$errorcode['330'] = 'Invalid_EcommOrder';
$errorcode['350'] = 'Absplit_UnknownError';
$errorcode['351'] = 'Absplit_UnknownSplitTest';
$errorcode['352'] = 'Absplit_UnknownTestType';
$errorcode['353'] = 'Absplit_UnknownWaitUnit';
$errorcode['354'] = 'Absplit_UnknownWinnerType';
$errorcode['355'] = 'Absplit_WinnerNotSelected';

// Validation errors
// General Validation Message "Sorry, MailChimp doesn't like the data you are trying to send. They gave the error code <strong>[error]</strong> For more help <a href="http://apidocs.mailchimp.com/api/1.3/exceptions.field.php">visit the MailChimp website</a> or <a href="http://kb.mailchimp.com/home">contact MailChimp</a>"
$errorcode['500'] = 'Invalid_Analytics';
$errorcode['503'] = 'Invalid_SendType';
$errorcode['504'] = 'Invalid_Template';
$errorcode['505'] = 'Invalid_TrackingOptions';
$errorcode['506'] = 'Invalid_Options';
$errorcode['507'] = 'Invalid_Folder';
$errorcode['550'] = 'Module_Unknown';
$errorcode['551'] = 'MonthlyPlan_Unknown';
$errorcode['552'] = 'Order_TypeUnknown';
$errorcode['553'] = 'Invalid_PagingLimit';
$errorcode['554'] = 'Invalid_PagingStart';
$errorcode['555'] = 'Max_Size_Reached';
$errorcode['556'] = 'MC_SearchException';


// Message "Sorry, that date and time is invalid. Please try again."
$errorcode['501'] = 'Invalid_DateTimel';

// Message "Sorry, that email address is invalid. Please try again."
$errorcode['502'] = 'Invalid_Email';

// Message "Sorry, that URL is invalid. Please try again."
$errorcode['508'] = 'Invalid_URL';


$error = "There was an error communicating with ";


?>
