<?php

// OPT: Går att sätta ifrån php.ini
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

	// OPT: Skulle nog gå att strunta i dom här.. Om man strippar vid nedsparning eller nått.
	// Tänk på att folk kan hacka, å kanske posta ner javacript å skräp..

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
