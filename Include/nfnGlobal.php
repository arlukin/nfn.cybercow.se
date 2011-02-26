<?php
	include_once("GlobalStripHttp.php");

// Denna fil inkluderas av "90%" av alla filer. Så lägg inte onödiga funktioner här.
//

// General
//

// Används av ASSERTLOG
define ('LOG_SYSTEMERROR',         1);	// Dessa fel ska aldrig inträffa. Det vill säga, inträffar det finns det buggar i databas eller program.
define ('LOG_ADMINDELETECOMPANY',  3);	// Företag som tagitsbort
define ('LOG_DEBUG',  						 4);	// Se GlobalDebug.php
define ('LOG_DELETEDUSER',  			 5);	// Loggar alla borttagna användare
define ('LOG_DELETEDPRICEPLAN',		 6);	// Loggar alla borttagna prisplaner
define ('LOG_USERERROR',     			10);	// RESERVED
define ('LOG_DEVELOP',    				66);
define ('LOG_LOADDATA',  	 	 			67);
define ('LOG_CONNECTNODEBEGIN', 	100);	// Används för att se om det finns start och slut connectnodes.
define ('LOG_CONNECTNODEEND',  		101);	// Det ska finnas lika många begin som end.

// Används i huvudsak av executeOnDb, när db har knasat till sig.
function LOGTOFILE($logType, $message, $fileName=ERRORLOGFILE)
{
	$message = nl2br('<br><br><b>LOGTOFILE(Type = '.$logType.')</b><br>'.$message.'<br><br>');
	if (!empty($message))
	{
		$fps = fopen($fileName, "a");
		fputs($fps, $message, strlen($message));
	}
}

function LOGTODB($logType, $message)
{
	global
		$PHP_SELF;

	// Hämta upp user_id ifrån sessionen om det finns någon.
	include_once("GlobalDb.php");
	$userId = getUserIdFromSession();

	executeOnDb('
	insert delayed into log
		(
			user_id,
			logtype_id,
			phpdoc,
			info,
			created,
			phpver,
			osver
		) values ( '.
			escN($userId).','.
			escN($logType).','.
			escS($PHP_SELF).','.
			escS($message).',
			now(), '.
			escS(phpversion()).','.
			escS(php_uname()).'
		)
	');
}

// Används när ett system felmedelande ska skrivas ut till skärmen.
function displayErrorMessage($msg)
{
	if (DEVELOP)
	{
		echo(nl2br($msg));
	}
	else
	{
		header('Location: /ApacheError/ApacheError.php?error=500');
		exit();
	}
}

function ASSERTLOG($condition, $logType, $message)
// Ska inte använda vanlig assert för vi vill ha med en logType och ett medelande.
//
{
	global
		$PHP_SELF;

	if ( $condition )
	{
		$msg .= "\n<B>ASSERT:</B>\n";
		$msg .= "File: <i>$PHP_SELF</i>\n";
		$msg .= "Current PHP version: <i>".phpversion()."</i>\n";
		$msg .= "Current OS: <i>".php_uname()."</i>\n";
		$msg .= "Message: <i>$message</i>\n\n";

		displayErrorMessage($msg);

		if (!DEVELOP)
		{
			include_once("GlobalEmail.php");
			sendEmail(SYSTEMERROREMAIL, 'ASSERTLOG SYSTEM ERROR', $msg."\nMore info in the db", 'FAREOFFICE SYSTEM GHOST', 'noemail@fareoffice.com');
			LOGTODB($logType, $msg);
		}

		exit();
	}
}

// Database
//

function &getMySQLErrorMessage($foError)
{
	global
		$PHP_SELF;

	$errMsg  = '<br>';
	$errMsg .= '<b>Error Occured at </b> '.date("Y-m-d H:i:s").'<br>';
	$errMsg .= '<b>File: </b>'.$PHP_SELF.'</b><br>';
	$errMsg .= '<b>MySQL ErrNo:</b> '.mysql_errno().'<br>';
	$errMsg .= '<b>MySQL ErrMsg:</b> '.mysql_error().'<br>';
	$errMsg .= '<b>FO ErrMsg:</b> '.$foError.'<br>';

	return $errMsg;
}

function emptyString(&$value)
{
	if (empty($value) && $value != '0')
		return true;

	return false;
}

// Används vid numerics värden på where, insert och update.
function &escN(&$value)
{
	if (emptyString($value))
		return 'NULL';
	else
		return $value;
};

// Används i getWhere funktioner..
function addWhere($whereAdd, &$where)
{
	if (!empty($where)) $where .= ' and ';
	$where .= $whereAdd;

	return true;
}

// Används vid string värden på where, insert och update.
define ('ESC_NONE',  0);
define ('ESC_LAST',  1);
define ('ESC_FIRST', 2);
define ('ESC_BOTH',  3);
function &escS(&$value, $tkn = '', $type = ESC_LAST)
{
	if (emptyString($value))
	{
		$ret = 'NULL';
	}
	else
	{
		switch ($type)
		{
			case ESC_LAST:
				$ret = '\''.mysql_escape_string($value).$tkn.'\'';
				break;

			case ESC_FIRST:
				$ret = '\''.$tkn.mysql_escape_string($value).'\'';
				break;

			case ESC_BOTH:
				$ret = '\''.$tkn.mysql_escape_string($value).$tkn.'\'';
				break;

			case ESC_NONE:
				$ret = '\''.mysql_escape_string($value).'\'';
				break;

			default:
				ASSERTLOG(TRUE, LOG_SYSTEMERROR, 'ecsS: Invalid type');
		}
	}

	return $ret;
}


function inEscS(&$arr)
{
	foreach($arr as $val)
	{
		if (!empty($str))
			$str .= ', ';

		$str .= escS($val);
	}

	return $str;
}


function inEscN(&$arr)
{
	foreach($arr as $val)
	{
		if (!empty($str))
			$str .= ', ';

		$str .= escN($val);
	}

	return $str;
}

// Används vid date värden på where, insert och update.
function &escD(&$value, $format = 'Y-m-d')
{
	if (empty($value))
		return ' null ';
	else
		return '\''.date($format, strtotime($value)).'\'';
}

// link, host, username, password, db
$dbLink = array(
);

function getMicrotime()
{
    list($usec, $sec) = explode(" ",microtime());
    return (int) ($usec * 100000);
}

function &executeOnDbNoExplain($query, $dbName='foMaster')
{
	return executeOnDb($query, $dbName='foMaster', false, true);
}

function &executeOnDb($query, $dbName='foMaster', $logToFile = false, $disableExplain = false)
{
	global
		$dbExplainQueries,
		$dbLink,
		$dbConfig;

	static
		$ignoreAbort;

	// Develop kod, används för att tracea hur en sql fråga ser ut.
	if (($logToFile || DBLOGTOFILE) && GLOBALDBLOGTOFILE)
	{
		$fps = fopen(SQLLOGFILE, "a");
		fputs($fps, 'explain ', 8);
		fputs($fps, $query, strlen($query));
		fputs($fps, ';'.chr(13), 2);

		if ($disableExplain)
			$dbExplainQueries[] = array('NO EXPLAIN'.$query, $dbName);
		else
			$dbExplainQueries[] = array($query, $dbName);
	}

	$dbNr = getMicrotime() % count($dbConfig[$dbName]);
	// Ser till att om någon databas fråga körts så kommer sidans alla databasfrågor
	// att köras.
	if (empty($ignoreAbort))
	{
		// OPT: bara om det är inserts, delets eller update, replace som körts...
		//if ()

		$ignoreAbort = true;
		ignore_user_abort($ignoreAbort);
		set_time_limit(86400);
	}

	$link = $dbLink[$dbName];
	// Connect to DB
	if ( !(isset($link)) && !($link = mysql_connect($dbConfig[$dbName][$dbNr][0], $dbConfig[$dbName][$dbNr][1], $dbConfig[$dbName][$dbNr][2])) )
	{
		$errorMessage = getMySQLErrorMessage("Error connecting to $dbName!");
	}
	else
	{
		if (!$result = mysql_db_query($dbConfig[$dbName][$dbNr][3], $query, $link))
			$errorMessage = getMySQLErrorMessage($query);

		$dbLink[$dbName] = $link;

		if (($logToFile || DBLOGTOFILE) && GLOBALDBLOGTOFILE)
		{
			$dbExplainQueries[count($dbExplainQueries)-1][2] = mysql_affected_rows($link);
			if (empty($result))
				$dbExplainQueries[count($dbExplainQueries)-1][3] = mysql_num_rows($result);
		}
	}

	if (!empty($errorMessage))
	{
		LOGTOFILE(LOG_SYSTEMERROR, $errorMessage);
		displayErrorMessage($errorMessage);
		exit();
	}

	// Måste ses över mer. Kan bli problem om vi har flera servers och connection poolen
	// kanske lämnar tillbaka en koppling till en annan server.
	//
	//mysql_close($pricelink);

	return $result;
}

function &executeOnDbReturnAffectedRows($query, $dbName='foMaster')
{
	global
		$dbLink;

	executeOnDb($query, $dbName);
	return mysql_affected_rows($dbLink[$dbName]);
}


function &executeOnDbReturnOneColumn($query, $dbName='foMaster', $logToFile=false)
{
	$result = executeOnDb($query, $dbName, $logToFile);
	if ( $row = mysql_fetch_array($result) )
		$attribute = $row[0];

	mysql_free_result($result);
	return $attribute;
}

function &executeOnDbReturnOneRow($query, $dbName='foMaster', $logToFile=false)
{
	$result = executeOnDb($query, $dbName, $logToFile);
	if ( $row = mysql_fetch_array($result) )
		$attribute = $row;

	mysql_free_result($result);
	return $attribute;
}

function executeOnDbReturnArray(&$arr, $sql, $key=null)
{
	$result = executeOnDb($sql);

	if (!empty($key))
	{
		$cols = split(',', $key);
		foreach ($cols as $col)
		{
			if (!empty($code))
				$code .= ".'_'.";

			$code .= "\$row[".$col."]";
		}
	}

	while($row=mysql_fetch_assoc($result))
	{
		if (empty($key))
			$id++;
		else
			eval('$id='.$code.';');

		$arr[$id] = $row;
	}
	return $arr;
}

function executeOnDbReturnTree(&$arr, $sql, $key=null, $pk=null)
{
	$result = executeOnDb($sql);

	if (!empty($key))
	{
		$cols = split(',', $key);
		foreach ($cols as $col)
		{
			if (!empty($code))
				$code .= ".'_'.";

			$code .= "\$row[".$col."]";
		}
	}

	while($row=mysql_fetch_assoc($result))
	{
		if (empty($key))
			$id++;
		else
			eval('$id='.$code.';');

		if (!empty($pk))
		{
			$tmp = &$arr;
			foreach($pk as $col => $name)
			{
				if (!isset($tmp[$row[$name]]))
					$tmp[$row[$name]] = array();

				$tmp = &$tmp[$row[$name]];
			}
			$tmp = $row;
		}
		else
		{
			$arr[$id] = $row;
		}

	}

	return $arr;
}



function executeOnDbReturnMerge(&$arr, $sql, $key=null, $pk=null, $fk=null)
{
	global
		$gIdx;

	if (!empty($fk))
	{
		foreach($fk as $name => $col)
		{
			if (!is_array($gIdx[$name]))
				return $arr;

			if (!empty($where))
				$where .= ' and ';

			$where .= implode(', ', array_keys($gIdx[$name]));
			$sql = str_replace('#'.$name, $where, $sql);
		}
	}

	$result = executeOnDb($sql);
	while($row=mysql_fetch_assoc($result))
	{
		if (empty($key))
			$id++;
		else
			$id = $row[$key];

		if (!empty($pk))
		{

			if (!isset($arr[$id]))
				$arr[$id] = $row;

			foreach($pk as $name => $col)
			{
				$gIdx[$name][$row[$col]] = array();
				$arr[$id][$name] = &$gIdx[$name][$row[$col]];
			}
		}

		if (!empty($fk))
		{
			foreach($fk as $name => $col)
				$gIdx[$name][$row[$col]][$id] = $row;
		}
	}
	return $arr;
}

function executeOnDbReturnMergeCount($name)
{
	global
		$gIdx;

	return count($gIdx[$name]);
}

// FIX: executeOnDbReturnInArray
// Select company_id, seller_id from seller; returnerar arr[1]= "1,2,3,4", arr[2]= "1,2,4,6,9"...
function &executeOnDbReturnString($sql, $separator = ', ')
{
	$result = executeOnDb($sql);

	while ($row = mysql_fetch_array($result))
	{
		$row[0] = strtoupper($row[0]);

		if (!empty($arr[$row[0]]))
			$arr[$row[0]] .= $separator;

		$arr[$row[0]] .= $row[1];
	}

	return $arr;
}

// Select seller_id from seller; returnerar "1,2,3,4,"
function &executeOnDbReturnIn($sql, $separator = ', ')
{
	$result = executeOnDb($sql);

	while ($row = mysql_fetch_array($result))
	{
		if (!empty($str))
			$str .= $separator;

		$str .= $row[0];
	}

	return $str;
}

function getCounters($table, $qty = 1)
{
	executeOnDb('LOCK TABLES counters WRITE');
	$counter = executeOnDbReturnOneColumn('select '.$table.' from counters');
	$newCounter=$counter+$qty;
	executeOnDb('update counters set '.$table.'='.escN($newCounter));
	executeOnDb('UNLOCK TABLES');

	return $counter+1;
}

?>
