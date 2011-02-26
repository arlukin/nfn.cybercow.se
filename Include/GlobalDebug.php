<?php
include_once('GlobalQuery.php');
include_once('GlobalDb.php');
include_once('3dPart/Timer.php');

//
// Constanter för debug mode.
//
define ('DBG_OFF', 		1);
define ('DBG_SCREEN', 2);
define ('DBG_DB', 		3);
define ('DBG_FILE', 	4);

// DEBUGPRODUCTION {DBG_OFF, DBG_SCREEN, DBG_DB, DBG_FILE}
//	Om denna constant är defined kommer dess värden att användas i första hand,
//ingen annan debug const överlagrar.

// DEBUGDEVELOP{DBG_OFF, DBG_SCREEN, DBG_DB, DBG_FILE}
// 	Stänger av och på debug mode i develop miljö

// DEBUGLOCAL{DBG_OFF, DBG_SCREEN, DBG_DB, DBG_FILE}
// 	Används i lokala php filer för att ändra output mode.
//Denna kan överlagra output mode ifrån DEBUGDEVELOP, men ej ifrån
//DEBUGPRODUCTION

//
// Har debug mode startats genom adminProfile.
//
$debugModePassword = 'COWSAREHACKINGUS&';
if($debugMode == md5($debugModePassword))
{
	define ('DEBUGPRODUCTION', DBG_SCREEN);
	define ('DBLOGTOFILE', true);

	define ('DEBUGDEVELOP', DBG_FILE);

}
else
{
	define ('DEBUGPRODUCTION', DBG_OFF);
	define ('DBLOGTOFILE', false);
}

//

	// Om denna är false, sker ingen db loggning. (explainQueries)
if($debugShowExplain == md5($debugModePassword))
	define ('GLOBALDBLOGTOFILE', true);
else
	define ('GLOBALDBLOGTOFILE', false);

/**
*	Will send an email with a vardump/debug info to a specific email
*
*	Example:
*	<code>
* debugEmail("cow's r us", 'cow@cybercow.se');
*	</code>
*
*	@param 	mixed  	$variable
*									The variable to send.
*
*	@param 	number 	$email
*									Email address to send the vardump to.
*
*	@see debugEcho()
*	@access public
*/
function debugEmail($variable, $email)
{
	include_once('GlobalEmail.php');
	ob_start();
		$debugNo = debugEcho($variable);

		exSendMail('DEBUG@fareoffice.com', array($email), 'DEBUG #'.$debugNo, NULL, ob_get_contents());
	ob_end_clean();
}

// debugExit och debugFilter funktionerna används för att göra en exit eller skriva ut 
//  text för just din user eller för ett specifikt filter.
//
// $filterId vilken filter som man sen ställer in i Admin Profile.
//
// Ex 1. jag skriver in debugExit('MP') och sen ställer in debug filter MP i Admin Profile
//       så kommer bara denna exit påverka mig och ingen annan som är inne och surfar i koden.
//
// Ex 2. På alla XML arrayer för alamo som skrivs ut i koden använder vi 
//			 debugFilter($var, 'XML') så om vi vill se alla utskrifter och XML:er 
//       så går man till AdminProfile och skriver in XML. 

function debugExit($filterId_ = NULL)
	{
	if (_isDebugFilter($filterId_))
	{
		_writeDebugEcho('debugExit', _getDebugFilterTooltipText($filterId_));
		exit();
	}
}

// se funktionen debugExit.
function debugFilter($variable, $filterId_ = NULL, $style = WHA_ARRAY, $forceOutput = false)
{
		
	if (_isDebugFilter($filterId_))
	{
		return _writeDebugEcho($variable, _getDebugFilterTooltipText($filterId_), $style, $forceOutput);
	}
	
	return null;
}

function debugEcho($variable, $style = WHA_ARRAY, $forceOutput = false)
{
	return _writeDebugEcho($variable, null, $style, $forceOutput);
}


function debugToXML($val_, $parentName_ = '', $descriptive_)
{
	static $level = 0;
	$level++;
	$retVal = '';

	if (is_array($val_) || is_object($val_))	
	{
		$retVal = "\r\n".str_repeat("\t", $level);
		$num = 0;
		foreach ($val_ as $key => $value)
		{
			$indentLevel = $level;
			$name = $key;

			$attributes = '';
			if ($descriptive_)
			{
				$attributes = 'type="'.gettype($value).'" size="'.sizeof($value).'"';
			}

			if (!_isValidElementName($key))
			{
				$attributes = 'id="'.$key.'" '.$attributes;
				if (!_isValidElementName($parentName_))
	{
					$name = 'item';
	}
				elseif (preg_match('/ies$/', $parentName_))
	{
					$name = substr($parentName_, 0, strlen($parentName_)-3).'y';
				}
				elseif (preg_match('/[s]$/', $parentName_))
				{
					$name = substr($parentName_, 0, strlen($parentName_)-1);
				}
				else
				{
					$name = $parentName_.'_item';
				}
			}
			
			if (++$num == count($val_))
			{
				$indentLevel = $level - 1;
			}
			
			$name = strtolower(str_replace(' ', '_', $name));
			
			$retVal .= '<'.$name.' '.$attributes.'>';
			$retVal .= debugToXML($value, $key, $descriptive_);
			$retVal .= '</'.$name.'>'."\r\n".str_repeat("\t", $indentLevel);
		}
	}
	else
	{
		$retVal = htmlentities($val_);
	}
	
	$level--;
	if ($level == 0)
	{
		$attributes = '';
		if ($descriptive_)
		{
			$attributes = ' type="'.gettype($val_).'" size="'.sizeof($val_).'"';
		}
		
		$retVal = '<root'.$attributes.'>'.$retVal.'</root>';
	}
	
	return $retVal;
}

function _isValidElementName($name_)
{
	if (is_numeric($name_) || is_numeric($name_{0}))
		return false;
	
	if (preg_match('/[*&%$\/\[\]{}=?\'"!£@]/', $name_))
		return false;
		
	if (strlen($name_) < 2)
		return false;
		
	return true;
}

function _isDebugFilter($filterId_)
{
	return (empty($filterId_) || empty($GLOBALS['debugFilterId']) || $filterId_ == $GLOBALS['debugFilterId']);
}

function _getDebugFilterTooltipText($filterId_)
{
	if (empty($filterId_))
	{
		$id = 'All';
	}
	else 
	{
		$id = $filterId_;
}

	if (empty($GLOBALS['debugFilterId']))
{
		$txt = 'Filter: '.$id.' (your debug filter is not set use Admin Profile to set debug filter)'.NEWLINE;
	}
	else if (empty($filterId_))
	{
		$txt = 'Filter: '.$id.' (your debug filter is \''. $GLOBALS['debugFilterId'].'\')'.NEWLINE;
	}
	else 
	{
		$txt = 'Filter: '.$id.NEWLINE;
	}
	
	return $txt;
}

//
function _writeDebugEcho($variable, $tooltip_ = null, $style = WHA_ARRAY, $forceOutput = false)
{
	static
		$debugNo = 0,
		$timer, 
		$memoryUsageFirst = 0,
		$memoryUsageLast = 0;

	if (DEBUGPRODUCTION != DBG_OFF || $forceOutput)
	{
		$file = debug_backtrace();
		echo('<span title="');
		
		if (!empty($tooltip_))
		{
			echo($tooltip_);
		}
		
		if (empty($timer))
		{
			$timer = new Benchmark_Timer;
			$timer->start();
			$timer->setMarker('new');

			echo('Time: [0.00]');
			echo('[0.00]');
		} 
		else
		{
			$timer->markers['old'] = $timer->markers['new'];
			$timer->setMarker('new');

			echo('Time: ['.number_format($timer->timeElapsed('Start', 'new'),2).']');
			echo('['.number_format($timer->timeElapsed('old', 'new'),2).']');
		}
		
		echo(' Line:'.$file[1]['line']."\n".$file[1]['file']);


		$memoryLimit = ini_get('memory_limit');

		if (!empty($memoryLimit))
		{
			$memoryUsage = memory_get_usage();
			if (empty($memoryUsageFirst))
			{
				$memoryUsageFirst = $memoryUsage;
				$memoryUsageLast = $memoryUsage;
			}
			echo(NEWLINE.'Memory usage:'.NEWLINE);
			echo('  Total: '.($memoryUsage/1024).' kb'.NEWLINE);
			echo('  Diff last: '.($memoryUsage-$memoryUsageLast).' bytes'.NEWLINE);
			echo('  Diff first: '.($memoryUsage-$memoryUsageFirst).' bytes');
			$memoryUsageLast = $memoryUsage;
		}		
		
		echo('">');
		
		if($debugNo > 0)
			echo('<a href="#debug_'.($debugNo-1).'" title="Previous">«</a>');
		else
			echo('<span disabled>«</span>');
		
    echo('<a name="debug_'.(string)$debugNo.'">#</a>'.(++$debugNo).'<a href="#debug_'.($debugNo).'" title="Next">»</a>:</span> ');
    if ($style != WHA_XML && $style != WHA_XML_DESC)
    {
    echo(getVarDump($variable, $style));
    }
    else
    {
			echo('<a href=\'#\' onclick=\'var oWin = window.open("'.FAREOFFICE_LINK.'/xml.php", "debug_'.$debugNo.'", "width=600,height=540,scrollbars=yes,resizable=yes,menubar=yes,toolbar=no");while(!oWin.document.all.data);oWin.document.all.data.value=document.all.xml_'.$debugNo.'.innerHTML;\'>view XML in new window</a><br><pre><xml id="xml_'.$debugNo.'">'.debugToXML($variable, '', $style == WHA_XML_DESC).'</xml></pre>');
    }

		echo('<br>');
	}

	return $debugNo;
}

//

function getVarDump($variable, $style)
{
	if (is_array($variable))
	{
		writeHtmlArray($variable, $style);
	}
	else if (is_object($variable))
	{
		$class = strtolower(get_class($variable));
		echo('<br><b>'.$class.'</b><br>');
		//echo('<br>Variables');
		writeHtmlArray(get_object_vars($variable), $style);
		//echo('<br>Methods');
		//writeHtmlArray(get_class_methods($class));
		//writeHtmlArray($variable);
		//var_dump($variable);
	}
	else if (is_resource($variable))
	{
		writeHtmlResult($variable, $style);
	}
	elseif ($style == DE_CHAR)
	{
		for($i=0; $i <strlen($variable); $i++)
		{
			echo(ord($variable[$i]).'-');
		}
	}
	else
	{
		echo('<span title="');
			echo('Type: '.gettype($variable).NEWLINE);
			echo('Length: '.strlen($variable));
		echo('">'.getVarContent($variable).'</span>');
	}
}

function getVarContent($val)
{
	if (is_null($val))
		$ret = 'NULL';
	else if (empty($val) && is_bool($val))
		$ret = 'FALSE';
	else if (emptyString($val) && !is_int($val))
		$ret = 'EMPTY';
	else if(is_string($val))
		$ret =  htmlspecialchars($val);
	else
		$ret =  $val;

	return $ret;
}

// Används för att skriva ut debug echos, se constant.php
//
// define ('DEBUGLOCAL', DBG_SCREEN);

// $style tar både writeHtmlArray koderna och nedan koder
define('DE_CHAR', 100);

//

define('WHL_RESULT', 	0);
define('WHL_ARRAY', 	1);
define('WHL_PHP', 		2);
define('WHL_PHPFULL',	3);
//
// $list = array, object eller mysql resultat
//
function writeHtmlList(&$list, $viewType = WHL_RESULT)
{
	echo getHtmlList($list, $viewType);
}

//
// @todo: ska stödja , $showTotal=false, $showType=false, $sort = false, $showSql = false, $showLink = false)
//
function getHtmlList(&$list, $viewType = WHL_RESULT)
{
	// Convert to array
	if (is_array($list))
	{
		$arrResult =& $list;
	}
	else if (is_object($list))
	{
		$arrResult = &get_object_vars($list);
	}
	else if (is_resource($list))
	{
		while ($row = mysql_fetch_assoc($list))
		{
			$arrResult[] = $row;
		}
	}

	// Output
	if ($viewType == WHL_ARRAY)
	{
		$result = getHtmlArray($arrResult);
	}
	elseif ($viewType == WHL_RESULT)
	{
		$result = getHtmlArrayResult($arrResult);
	}
	elseif ($viewType == WHL_PHP)
	{
		$result = '<pre>'.getHtmlArrayPhp($arrResult).'</pre>';
	}
	elseif ($viewType == WHL_PHPFULL)
	{
		$result = '<pre>'.getHtmlArrayPhpFull($arrResult).'</pre>';
	}

	return $result;
}

//

function writeHtmlResult(&$result, $showTotal=false, $showType=false, $sort = false, $showSql = false, $showLink = false, $showFilter = false)
{
	echo(getHtmlResult($result, $showTotal, $showType, $sort, $showSql, $showLink, $showFilter));
}

//

define('WHA_ARRAY', 	0);
define('WHA_RESULT', 	1);
define('WHA_PHP', 		2);
define('WHA_PHPFULL',	3);
define('WHA_PHPONELINE',4);		// Skriver ut en php array, på en rad.
define('WHA_XML', 5); 
define('WHA_XML_DESC', 6); 
function writeHtmlArray(&$arr, $type = WHA_ARRAY)
{
	if ($type == WHA_ARRAY)
	{
		echo(getHtmlArray($arr));
	}
	elseif ($type == WHA_RESULT)
	{
		echo(getHtmlArrayResult($arr));
	}
	elseif ($type == WHA_PHP)
	{
		echo('<pre>'.getHtmlArrayPhp($arr).'</pre>');
	}
	elseif ($type == WHA_PHPFULL)
	{
		echo('<pre>'.getHtmlArrayPhpFull($arr).'</pre>');
	}
	elseif ($type == WHA_PHPONELINE)
	{
		$search = array("'", "\n");
		$replace = array('"', '');
		$str = str_replace($search, $replace, getHtmlArrayPhp($arr));

		echo('<br>'.$str);
	}

}

//


function writeAscii($text)
{
	for ($i=0;$i<strlen($text);$i++)
	{
		$arr[$i] = '['.$text[$i].'] - '.ord($text[$i]);
	}
	writeHtmlArray($arr);
}

/*
Routine Description:

	Sorterar värdet i en array, kan sortera på
	en viss column.

	OBS: Kom ihåg att $key sparas ej av array_multisort, om $key är numerisk.

Arguments:

	$columnType
		LOWER
		DATE

*/
function csort($arr, $column, $flag = SORT_ASC, $columnType = 'LOWER')
{
  foreach($arr as $key => $val)
  {
  	if ($columnType == 'LOWER')
  	{
    	$sortarr[$key]=strtolower($arr[$key][$column]);
    }
    elseif ($columnType == 'DATE')
    {
    	$sortarr[$key]=strtotime($arr[$key][$column]);
    }
  }

  if (is_array($sortarr))
  	array_multisort($sortarr, $flag, $arr);

  return $arr;
}

//

function getHtmlArray(&$arr, $topArray=true)
{
	global
		$getHtmlArrayStr;

	static
		$sNumberOfLevels;
		
	if ($topArray)
	{
		$getHtmlArrayStr = '';
		$sNumberOfLevels = 0;
	}

	if (is_array($arr))
	{
		if ($topArray)
		{
			$getHtmlArrayStr .= '<br><br>';
		}

		$getHtmlArrayStr .='<table border="1" cellspacing="0" cellpadding="3">';
			$getHtmlArrayStr .='<tr>';
				$getHtmlArrayStr .='<td colspan="4">Count:'.count($arr).'</td>';
			$getHtmlArrayStr .='</tr>';
			$getHtmlArrayStr .='<tr>';
				$getHtmlArrayStr .='<td>key</td>';
				$getHtmlArrayStr .='<td>value</td>';
				$getHtmlArrayStr .='<td>type</td>';
				$getHtmlArrayStr .='<td>size</td>';
			$getHtmlArrayStr .='</tr>';

			foreach ($arr as $key => $val)
			{
				$getHtmlArrayStr .='<tr>';

					$getHtmlArrayStr .='<td>'.$key.'</td>';
					$getHtmlArrayStr .='<td>'.getVarContent($val).'</td>';
					$getHtmlArrayStr .='<td>'.gettype($val).'</td>';

					if (is_string($val))
					{
						$getHtmlArrayStr .='<td>'.strlen($val).'</td>';
					}
					else
					{
						$getHtmlArrayStr .='<td>&nbsp;</td>';
					}

				$getHtmlArrayStr .='</tr>';

				// Write the inner array
				if (is_array($val))
				{
					$getHtmlArrayStr .='<tr>';
						$getHtmlArrayStr .='<td></td>';
							$getHtmlArrayStr .='<td colspan="3">';

							if ($sNumberOfLevels > 15)
							{
								$getHtmlArrayStr .=' To many levels for debug output ';
							}
							else
							{
								$sNumberOfLevels++;
								getHtmlArray($val, false);
								$sNumberOfLevels--;
							}

						$getHtmlArrayStr .='</td>';
					$getHtmlArrayStr .='</tr>';
				}
			}
			
		$getHtmlArrayStr .='</table>';

		if ($topArray)
		{
			$getHtmlArrayStr .='<br><br>';
		}
	}

	if ($topArray)
		return $getHtmlArrayStr;
}

//

function getHtmlArrayResult($arr)
{
	global
		$getHtmlArrayStr;

	$captionRow = reset($arr);
	$str = '';
	if (!empty($captionRow))
	{
		$str .='<table bordercolor="black" cellspacing="2" cellpadding="3" style="background-color: #F0F1F5; border-style: outset;border-width: 1; border-color: #F0F1F5;">';

			$str .='<tr style="background-color: #F0F1F5;">';
				foreach ($captionRow as $key => $val)
				{
					$str .='<td nowrap><font color="blue">';
						$str .= ucfirst($key);
					$str .='</font>';
				}
			foreach ($arr as $dummy => $row)
			{
				$str .='<tr style="background=white">';
					foreach ($captionRow as $key => $column)
					{
						$str .='<td nowrap valign="top">';
							if (is_array($row[$key]))
							{
								$getHtmlArrayStr = '';
									getHtmlArray($row[$key], false);
								$str .= $getHtmlArrayStr;
							}
							else if (emptyString($row[$key]))
							{
								$str .= '&nbsp;';
							}
							else
							{
								$str .= $row[$key];
							}
					}
			}

		$str .='</table>';
	}

	return $str;
}

//
// @todo spearator används aldrig. Syftet med denna?
function getHtmlArrayPhp($arr, $rowPrefix = '')
{
	if (!empty($arr))
	{
		$currentRowPrefix = '';
		$str = 'array'."\n".$rowPrefix.'('."\n";
		foreach ($arr as $key => $row)
		{
			if (!empty($currentRowPrefix))
				$str .= ",\n";

			$currentRowPrefix = $rowPrefix."\t".'\''.$key.'\' => ';

			$arr = unserialize($row);
			if ($arr)
				$row = $arr;

			if (is_array($row))
			{
				$currentArrayRowPrefix = $rowPrefix."\t";

				$recursiveStr = getHtmlArrayPhp($row, $currentArrayRowPrefix);
				if (empty($recursiveStr))
					$recursiveStr = 'array()';

				$str .= $currentRowPrefix.$recursiveStr;
			}
			elseif (emptyString($row))
				$str .= $currentRowPrefix.' NULL';
			elseif (is_numeric($row))
				$str .= $currentRowPrefix.escN($row);
			elseif (is_string($row))
				$str .= $currentRowPrefix.escS($row);
			else
				$str .= $currentRowPrefix.'\'\'';
		}
		$str .= "\n".$rowPrefix.')';
	}

	return $str;
}

//

function getHtmlArrayPhpFull($arr, $rowPrefix = '$array', $rowSuffix = ";\n")
{
	if (!empty($arr))
	{
		foreach ($arr as $key => $row)
		{
			$currentRowPrefix = $rowPrefix.'['.$key.']';

			if (is_array($row))
			{
				$recursiveStr = getHtmlArrayPhpFull($row, $currentRowPrefix);
				if (empty($recursiveStr))
					$recursiveStr = $currentRowPrefix.' = array()'.$rowSuffix;

				$str .= $recursiveStr;
			}
			elseif (emptyString($row))
				$str .= $currentRowPrefix.' = NULL'.$rowSuffix;
			elseif(is_numeric($row))
				$str .= $currentRowPrefix.' = '.$row.$rowSuffix;
			else
				$str .= $currentRowPrefix.' = \''.$row.'\''.$rowSuffix;
		}
	}

	return $str;
}

//
/**
* @todo HJÄLP!!! JAG ÄR EN JÄTTELÅNG FUNKTION SOM ÄR JÄTTEJOBBIG ATT LÄSA!!!
*/
function getHtmlResult(&$result, $showTotal=false, $showType=false, $sort = true, $showSql = false, $showLink = false, $showFilter = false)
{
  global
  	$HTTP_POST_VARS,
  	$HTTP_GET_VARS,
    $formResultFilter,
    $formResultFilterFlag,
    $formSortColumn,
    $formSortFlag,
    $sortFlag,
    $formTrackNo,
    $_SERVER;

		$columns = mysql_num_fields($result);
		$columnStr .='<tr style="background-color: #F0F1F5;">';
		$filterStr .='<tr style="background-color: #F0F1F5;">';
		$sqlStr = '';

		$typeArray = array(
			'like' 	=> '%',
			'equal' => '=',
			'less' 	=> '&lt;',
			'great' => '&gt;'
		);

    if (!empty($formSortFlag))
    {
      if ($formSortFlag == SORT_ASC)
        $sortFlag = SORT_DESC;
      else
        $sortFlag = SORT_ASC;

      $formSortFlag = '';
    }

    if (!empty($formResultFilterFlag))
    {
	    $formResultFilter = null;
	  }

    if (empty($sortFlag))
     $sortFlag = SORT_ASC;

		for ($i=0; $i < $columns; $i++)
		{
				$columnStr .='<td nowrap><font color="blue">';
				$meta = mysql_fetch_field($result);

        if ($sort)
        {
	        $queryStr = 'formTrackNo='.$formTrackNo.'&formSortColumn='.urlencode($meta->name).'&formSortFlag='.urlencode($sortFlag);
		      $columnStr .= '<a href="javascript:doPostOnLink(\''.$queryStr.'\', \'\');">';
		      $columnStr .= $meta->name;
		      $columnStr .= '</a>';

        }
        else
        {
          $columnStr .= $meta->name;
        }


				if ($showSql)
					$columnStrSql[$meta->table] = inEscN($meta->name, $columnStrSql[$meta->table]);

	      $metaData[$i] = $meta;

        if ($showType)
        {
          $columnStr .=  '(';
					if (!empty($meta->primary_key))
						$columnStr .='PK ';
					$columnStr .= $meta->type.' ';
					$columnStr .= $meta->max_length;
				  $columnStr .= ')';
        }

			$columnStr .='</font>';

			if ($showFilter)
			{
				$filterStr .= '<td nowrap>
											 <select name="formResultFilter['.$meta->name.'][type]">
											 	'.writeSelectArray($typeArray, $formResultFilter[$meta->name]['type'], NULL).'
											 </select>
											 <input type="text" name="formResultFilter['.$meta->name.'][value]" size="4" value="'.$formResultFilter[$meta->name]['value'].'">';
			}
  	}

		while ($row = mysql_fetch_array($result))
		{
			if ($showFilter && isset($formResultFilter))
			{
				$validColumns = 0;
				foreach($formResultFilter as $key => $val)
				{
					if (!empty($val['value']))
					{
						if ($val['type'] == 'equal')
						{
							if ($row[$key] == $val['value'])
								$validColumns++;
						}
						else if ($val['type'] == 'less')
						{
							if ($row[$key] < $val['value'])
								$validColumns++;
						}
						else if ($val['type'] == 'great')
						{
							if ($row[$key] > $val['value'])
								$validColumns++;
						}
						else if ($val['type'] == 'like')
						{
							if (strstr(strtolower($row[$key]), strtolower($val['value'])))
								$validColumns++;
						}

					}
					else
					{
						$validColumns++;
					}
				}

				if ($validColumns == $columns)
					$arr[] = $row;
			}
			else
			{
				$arr[] = $row;
			}
		}
		
		if (mysql_num_rows($result) > 0)
		{
			mysql_data_seek($result, 0);
		}

    if (isset($arr))
    {
      if ($sort && !empty($formSortColumn))
         $arr = csort($arr, $formSortColumn, $sortFlag);

      foreach ($arr as $row)
      {
      	if ($showSql)
      		$valueStrSql = array();

        $str .='<tr style="background=white">';
        for ($i=0; $i < $columns; $i++)
        {

					$obj = unserialize($row[$i]);
					if ($obj)
					{
						$str .='<td nowrap valign="top">';
						$str .= getHtmlArray($obj);
					}
					else
					{
						if ($metaData[$i]->max_length < 50)
						{
							$str .='<td nowrap valign="top">';
							$str .= $row[$i];
						}
						else
						{
							$str .='<td valign="top">';
							$str .= nl2br($row[$i]);
						}
					}

					if($showLink)
					{
						if (substr($metaData[$i]->name, -2, 2) == 'id')
						{
								$formQuery = 'select * from '.substr($metaData[$i]->name, 0, -3).' where '.$metaData[$i]->name.' = '.escN($row[$i]);
								$queryStr = 'formSortColumn='.urlencode($metaData[$i]->name).'&formSortFlag='.urlencode($sortFlag).'&formQuery='.urlencode($formQuery).'&formResultFilterFlag=true';
								$str .= '<a href="javascript:doPostOnLink(\''.$queryStr.'\',\'\');">';

								if (substr($metaData[$i]->name, 0, -3) != $metaData[$i]->table)
									$str .= '=>';
								else
									$str .= '*>';


								$str .= '</a>';
						}
					}

          if ($showTotal)
          {
            if (is_numeric($row[$i]))
              $totalNum[$i] += $row[$i];
            else
              $totalUnique[$i][$row[$i]]++;
          }

          if ($showSql)
          {
            if (is_numeric($row[$i]))
              $valueStrSql[$metaData[$i]->table] = inEscN($row[$i], $valueStrSql[$metaData[$i]->table]);
            else
            	$valueStrSql[$metaData[$i]->table] = inEscS($row[$i], $valueStrSql[$metaData[$i]->table]);
          }
        }

      	if ($showSql)
      	{
					foreach($valueStrSql as $table => $strSql)
					{
						$tmp = '<br>('.$strSql.')';
						$allStrSql[$table] = inEscN($tmp, $allStrSql[$table]);
					}
				}
      }
    }

  	$retStr .='<table bordercolor="black" cellspacing="2" cellpadding="3" style="width=100%; background-color: #F0F1F5; border-style: outset;border-width: 1; border-color: #F0F1F5;">';
		if ($showTotal)
		{
			$retStr .='<tr><td colspan="'.$columns.'" height="5">';
			$retStr .='<tr><td colspan="'.$columns.'"><b>TOTAL</b>';
      $retStr.= $columnStr;

			$retStr .='<tr style="background=white">';
			for ($i=0; $i < $columns; $i++)
			{
				$retStr .='<td nowrap valign="top">';
					if (!empty($totalUnique[$i]))
						$retStr .= count($totalUnique[$i]).' unique';

          if (!empty($totalNum[$i]))
						$retStr .= '='.$totalNum[$i];
			}
		}

    $retStr .='<tr><td colspan="'.$columns.'"><b>LIST</b>';
    $retStr.= $columnStr;
    $retStr.= $filterStr;
    $retStr .= $str;

		if ($showSql)
		{
			$retStr .='<tr><td colspan="'.$columns.'" height="5">';
			$retStr .='<tr><td colspan="'.$columns.'"><b>SQL</b>';
			$retStr .='<tr style="background=white">';

			$retStr .='<td colspan="'.$columns.'" nowrap valign="top">';

			foreach($allStrSql as $table => $strSql)
			{
				$retStr .= 'insert into '.$table.' ('.$columnStrSql[$table].') values '.$strSql.'<br>;';
				$retStr .= '<br><br>';
			}
		}

	$retStr .='</table>';
	return $retStr;
}

//

function formatSQL($sql)
{
	$search = array
	(
		'(\s{1,})',
		'/[,]\s/',
		'/[\s]and[\s]/i',
		'/[\s]or[\s]/i',
		'/[\s]using[\s]/i',
		'/[\s]on[\s]/i',
		'/[\s]select[\s]/i',
		'/[\s]from[\s]/i',
		'/[\s]inner[\s]join[\s]/i',
		'/[\s]left[\s]join[\s]/i',
		'/[\s]right[\s]join[\s]/i',
		'/[\s]straight_join[\s]/i',
		'/[\s]where[\s]/i',
		'/[\s]order by[\s]/i',
		'/[\s]group by[\s]/i',
		'/[\s]limit[\s]/i',
		'/[\s]distinct[\s]/i',
	);

	$replace = array
	(
		' ',
		",\n\t",
		" <font color=blue>AND</font>\n\t",
		" <font color=blue>OR</font>\n\t",
		" <font color=blue>USING </font>",
		" <font color=blue>ON </font>",
		"<font color=blue>SELECT</font>\n\t",
		"\n<font color=blue>FROM</font>\n\t",
		"\n\t<font color=red>INNER JOIN </font>",
		"\n\t<font color=red>LEFT JOIN </font>",
		"\n\t<font color=red>RIGHT JOIN </font>",
		"\n\t<font color=red>STRAIGHT_JOIN </font>",
		"\n<font color=blue>WHERE </font>\n\t",
		"\n<font color=blue>ORDER BY</font>\n\t",
		"\n<font color=blue>GROUP BY</font>\n\t",
		"\n<font color=blue>LIMIT&nbsp;</font>",
		"\n\t<font color=blue>DISTINCT</font>\n\t",
	);

	return '<pre>'.preg_replace($search, $replace ,$sql).'</pre>';
}

//

function &writeExplainQueries()
{
	global
		$dbExplainQueriesDisabled,
		$dbExplainQueries;

	$dbExplainQueriesDisabled = true;

	if (!empty($dbExplainQueries))
	{
		echo('<br><br>');
		echo('<table bordercolor = "blue" cellspacing="0" cellpadding="10" border="1">');
		foreach($dbExplainQueries as $arr)
		{
			if (substr(trim(strtolower($arr[0])),0 , 7) != 'explain')
			{
				$formatedSql = formatSQL($arr[0]);

				echo('<tr><td><br>');

					// om det skulle ha skapats en form
					echo('</form>');

					echo('<form target="AdminQuery" method="post" action="/Admin/AdminQuery/AdminQuery.php">');
						echo('<input type="hidden" value="'.urlencode(strip_tags($formatedSql)).'" name="formQuery">');
						echo('<input type="image" src="'.IMAGEURL.'/Buttons/template.gif" name="AdminQuery">');
					echo('</form>');

					echo('File: '.$arr[5].'<br>');
					echo('Time:'.$arr[4].' sec<br>');
					echo('Num of rows: '.$arr[2].'<br>');

					$countTime += $arr[4];

					if (
					    (substr(trim(strtolower($arr[0])),0 , 6) == 'update') ||
						  (substr(trim(strtolower($arr[0])),0 , 7) == 'replace') ||
						  (substr(trim(strtolower($arr[0])),0 , 6) == 'insert')  ||
						  (substr(trim(strtolower($arr[0])),0 , 6) == 'delete')
						 )
					{
						echo('Affected rows:'.$arr[2].'<br>');
						$count++;
					}
					else if (substr(trim(strtolower($arr[0])),0 , 6) == 'select')
					{
						$resultArr =array();
						resultToArray($resultArr, executeOnDb('explain '.$arr[0], $arr[1], false));
						writeHtmlArray($resultArr, WHA_RESULT);

						$queryExplain = new foQuery(QUERY_INSERT);
						$queryIndex 	= new foQuery(QUERY_INSERT_UPDATE);
						$query			 	= new foQuery(QUERY_INSERT);
						$queryCounter = new foQueryCounter('debug_query_stat');

						$query->addValue('debug_query_stat', 		'debug_query_stat_id',	$queryCounter);
						$query->addValue('debug_query_stat', 		'query_sql', 						escS($arr[0]));
						$query->addValue('debug_query_stat', 		'seconds', 							escS($arr[4]));
						$query->addValue('debug_query_stat', 		'rows', 								escS($arr[2]));
						$query->addValue('debug_explain_stat', 	'created_by',						escN(getUserIdFromSession()));
						$query->addValue('debug_explain_stat',	'created_time',					escV('now()'));
						$query->execute();

						foreach($resultArr as $row)
						{
							if (!empty($row[table]))
							{
								// Spara explain frågan
								//
								$queryExplain->addValue('debug_explain_stat', 'debug_query_stat_id', 		$queryCounter);
								$queryExplain->addValue('debug_explain_stat', 'table_name', 						escS($row[table]));
								$queryExplain->addValue('debug_explain_stat', 'type', 									escS($row[type]));
								$queryExplain->addValue('debug_explain_stat', 'possible_keys', 					escS($row[possible_keys]));
								$queryExplain->addValue('debug_explain_stat', 'key_name',								escS($row[key_name]));
								$queryExplain->addValue('debug_explain_stat', 'key_len', 								escS($row[key_len]));
								$queryExplain->addValue('debug_explain_stat', 'ref', 										escS($row[ref]));
								$queryExplain->addValue('debug_explain_stat', 'rows', 									escN($row[rows]));
								$queryExplain->addValue('debug_explain_stat', 'extra',									escS($row[extra]));
								$queryExplain->addValue('debug_explain_stat', 'created_by',							escN(getUserIdFromSession()));
								$queryExplain->addValue('debug_explain_stat', 'created_time',						escV('now()'));
								$queryExplain->execute();

								// Spara stat över alla index.
								//
								if ($row[type] == 'system' || $row[type] == 'ALL')
									$row[key] = $row[type];

								$queryIndex->addValue('debug_index_stat', 'table_name', escS($row[table]));
								$queryIndex->addValue('debug_index_stat', 'key_name', 	escS($row[key]));
								$queryIndex->addValue('debug_index_stat', 'times_used', escV('times_used+1'));
								$queryIndex->addValue('debug_index_stat', 'max_rows', 	escV('GREATEST(max_rows, '.$row[rows].')'));
								$queryIndex->addValue('debug_index_stat', 'total_rows', escV('total_rows+'.$row[rows]));

								$queryIndex->addWhere('debug_index_stat', 'table_name = '.escS($row[table]));
								$queryIndex->addWhere('debug_index_stat', 'key_name   = '.escS($row[key]));
								$queryIndex->execute();
							}
						}
						$count++;
					}
					else
						echo('<br>');

					echo($formatedSql);

				echo('<br></td></tr>');
			}
		}
		echo('</table>');

		echo('Time: '.($countTime).' sec<br>');
		echo('Queries: '.($count).'<br>');
	}
}

//

function debug_serialize($arr)
{
	if (count($arr) >0 )
	{
		$str = '';
		foreach ($arr as $val)
		{
			if (!isset($val))
				$val = 'NULL';

			if (!empty($str))
				$str .= ', ';

			$str .= $val;
		}
		reset($arr);
	}

	return ' array('.$str.')';
}

//

function c_sizeof($type,$auto_detect=0)
{
	 if (!is_array($type))
	 { // if we're a single data type
			 if ($auto_detect=0)
			 { //if someone is just telling us a type
					 switch ($type)
					 {
							 case "char":
							 {
									 return 1;
							 }
							 case "wchar_t":
							 {
									 return 2;
							 }
							 case "int":
							 {
									 return 4;
							 }
							 case "short":
							 {
									 return 2;
							 }
							 case "long":
							 {
									 return 4;
							 }
							 case "float":
							 {
									 return 4;
							 }
							 case "double":
							 {
									 return 8;
							 }
							 case "long double":
							 {
									 return 10;
							 }
							 case "bool":
							 {
									 return 1;
							 }
					 }
			 }
			 else
			 {	// or else we're an actual type
					 $tvar=true;
					 switch ($tvar)
					 {
							 /*case is_short($type):
							 {
										 unset($tvar);
										 return 2;
							 }*/
							 case is_int($type):
							 {
									 unset($tvar);
									 return 4;
							 }
							 case is_float($type):
							 {
									 unset($tvar);
									 return 4;
							 }
							 case is_double($type):
							 {
									 unset($tvar);
									 return 4;
							 }
							 case is_bool($type):
							 {
									 unset($tvar);
									 return 1;
							 }
							 case is_string($type):
							 {
									 unset($tvar);
									 return strlen($type);
							 }
					 }
			 }
	 }
	 else
	 {
	 		 // or if we're an array
			 // calculate array data usage in bytes.
			 return c_sizeof_array($type);
	 }
}

/*
	The recursive function for getting
	the size in bytes of an array
*/
function c_sizeof_array(&$struct)
{
	 $element_size=0;
	 foreach ($struct as $key => $val)
	 {
			 if (!is_array($val))
			 {
					 $element_size= ($element_size + c_sizeof($val,1));
			 } else
			 {
					 $element_size= ($element_size + c_sizeof_array($val));
			 }
	 }
	 return $element_size;
}

//


function getSizeOfGlobals($minSize = 10000)
{
	$arr = array();
	foreach($GLOBALS as $key => $val)
	{
		if ($key != 'GLOBALS')
		{
			$size = c_sizeof($val, 1);
			if ($size > $minSize)
			{
				$arr[$key] = $size;
			}

			$totalAllocatedMemory +=$size;
		}
	}

	if (!empty($arr))
		$arr[TOTAL_ALLOCATED_MEMORY] = number_format($totalAllocatedMemory, 0, '.', ' ');

	return $arr;
}


function debugSizeOf($minSize = 10000)
{
	debugEcho(getSizeOfGlobals($minSize));
}

?>