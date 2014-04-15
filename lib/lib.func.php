<?php
function yksemeProcessSnippet($list=false, $submit_text)
	{
	global $yksemeBase;
	if(!is_object($yksemeBase))
		{
		$yksemeBase			= new yksemeBase();
		}
	return $yksemeBase->processSnippet($list, $submit_text);
	}
?>