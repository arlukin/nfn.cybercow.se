<?php

// OPT: G�r att s�tta ifr�n php.ini
//ob_start("ob_gzhandler");
/*
	function compress_output($output)
	{
		// We can perform additional manipulation on $output here, such
		// as stripping whitespace, etc.
		//return gzencode($output);
		return $output;
	}

	// Check if the browser supports gzip encoding, HTTP_ACCEPT_ENCODING
	if (strstr($HTTP_SERVER_VARS['HTTP_ACCEPT_ENCODING'], 'gzip'))
	{
		// Start output buffering, and register compress_output() (see
		// below)
		ob_start("compress_output");

		// Tell the browser the content is compressed with gzip
		//header("Content-Encoding: gzip");
	}
*/
	//

	// OPT: Skulle nog g� att strunta i dom h�r.. Om man strippar vid nedsparning eller n�tt.
	// T�nk p� att folk kan hacka, � kanske posta ner javacript � skr�p..

	function foStripHTTP(&$arr)
	{
		if(is_array($arr))
		{
			foreach($arr as $key =>$val)
			{
				if (!is_array($arr[$key]))
					$arr[$key] = strip_tags($val);
				else
					foStripHTTP($arr[$key], false);
			}
		}
	}

	if (!defined("DONTSTRIPTAGS"))
	{
		foStripHTTP($HTTP_POST_VARS);
		foStripHTTP($HTTP_GET_VARS);
		foStripHTTP($HTTP_COOKIE_VARS);

		extract($HTTP_POST_VARS);
		extract($HTTP_GET_VARS);
		extract($HTTP_COOKIE_VARS);
	}
?>
