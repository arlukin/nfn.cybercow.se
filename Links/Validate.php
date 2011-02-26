<?php

	function isValid()
	{
		global
			$errMsg, $formName, $formEmail, $formWebpageName, $formUrl;
			
		if (empty($formName) || empty($formEmail) || empty($formWebpageName) || empty($formUrl))		
		{
			$errMsg = 'You must enter a name, email, webpage name and an url.';			
		}		
		if (empty($errMsg))
			return true;
		else	
			return false;			
	}
?>