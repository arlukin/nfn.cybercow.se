<?php

	function isValid()
	{
		global
			$errMsg, $formName, $formEmail, $formComment;
			
		if (empty($formName) || empty($formEmail) || empty($formComment))		
		{
			$errMsg = 'You must enter a name, email and a comment.';			
		}
		
		if (empty($errMsg))
			return true;
		else	
			return false;			
	}
?>