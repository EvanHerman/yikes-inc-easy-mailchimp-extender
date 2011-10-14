<?php
/*
This page gets included from lib.ajax and then processes
the post. This page should never get called by itself.
*/
if(!empty($_POST)
&& isset($_POST['form_action']))
	{	
	switch($_POST['form_action'])
		{
		
		
		
default:
	echo '-1';
	break;
	
case 'update_api_key':
	$action	= $yksmfBase->updateApiKey($_POST['api_key']);
	if($action)
		{
		echo '1';
		}
	else echo '-1';
	break;

case 'list_add':
	$list	= $yksmfBase->addList();
	if($list)
		{
		echo json_encode($list);
		}
	else echo '-1';
	break;
	
case 'list_update':
	$action	= $yksmfBase->updateList($_POST);
	if($action)
		{
		echo '1';
		}
	else echo '-1';
	break;
	
case 'list_sort':
	$action	= $yksmfBase->sortList($_POST);
	if($action)
		{
		echo '1';
		}
	else echo '-1';
	break;
	
case 'list_delete':
	$action	= $yksmfBase->deleteList($_POST['id']);
	if($action)
		{
		echo '1';
		}
	else echo '-1';
	break;

case 'frontend_submit_form':
	$action	= $yksmfBase->addUserToMailchimp($_POST);
	if($action)
		{
		echo '1';
		}
	else echo '-1';
	break;
	

		
		}
	}
?>