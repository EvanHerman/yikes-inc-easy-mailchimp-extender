<?php
function yksemeProcessSnippet($list=false)
	{
	global $yksemeBase;
	if(!is_object($yksemeBase))
		{
		$yksemeBase			= new yksemeBase();
		}
	return $yksemeBase->processSnippet($list);
	}
?>