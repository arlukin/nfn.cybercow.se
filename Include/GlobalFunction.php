<?php

/*
 Denna fil innehåller hjälp funktioner för både form och result sidor.
*/

//

// getCharRepeatSize använder sig av dessa.
define('DIR_FORWARD', 1);
define('DIR_BACKWARD', 2);

// Kan bara användas innan header skickats till klienten.
// Skriver ut en propertyBox, med rubrik $caption och tabellen/listan $arrList
//
function writePropertyBox($trackNo, $backUrl, $caption, $arrList, $subCaption = NULL, $ingress = NULL)
{
	$track = createSessionTrack(C_STANDARD);
	setUrlToSessionTrack($backUrl.'?formTrackNo='.$trackNo, 'SEARCHRESULT');

	$formArr['properties'] = $arrList;
	$formArr['caption'] = $caption;
	$formArr['subcaption'] = $subCaption;
	$formArr['ingress'] = $ingress;

	setToSessionTrack('formArr', $formArr);

	saveSessionTrack($track);
	header('Location: /System/Properties/Properties.php?formTrackNo='.$track);
	exit();
}

// Kan bara användas innan header skickats till klienten.
// $formTrackNo -- Behöver ej skickas med.
//
/*
	Arguments
	- flags - används ej

	@todo: Använd istället componenten. Kan köras efter att header körts.
	include_once('ContentManager/Components/messagebox/messagebox.php');
	$msgBox = new messagebox();
	$msgBox->setArgument('Caption', 'Subcaption', 'Text');
	$msgBox->writeComponent();

*/
function writeMessageBox($caption, $subCation, $text, $agreeText = NULL, $flags = NULL, $okAction = NULL, $okTarget = NULL, $cancelAction = NULL, $cancelTarget = NULL)
{
	global
		$gCaption,
		$gSubCation,
		$gText,
		$gAgreeText,
		$gFlags,  /* Ikoner, storlek på fönster */
		$gOkAction,
		$gOkTarget,
		$gCancelAction,
		$gCancelTarget,
		$MB_YES_x,
		$MB_NO_x,

		/* Måste vara global för att funktionerna i GlobalForm.php ska komma åt den */
		$gMessageCode,
		$inputAttributes,
		$formTrackNo,
		$formIAgree;

		$gCaption = $caption;
		$gSubCation = $subCation;
		$gText = $text;
		$gAgreeText = $agreeText;
		$gFlags = $flags;
		$gOkAction = $okAction;
		$gOkTarget = $okTarget;
		$gCancelAction = $cancelAction;
		$gCancelTarget = $cancelTarget;

		if (isset($MB_YES_x))
			if ($agreeText == null || checkAgree())
				return true;

		if (isset($MB_NO_x))
			return false;

		include_once('GlobalForm.php');

		echo('<html>');
		echo('<head>');
			include('Stylesheet.php');
			echo('<title>Message box - '.$caption.'</title>');
		echo('</head>');

		echo('<body style="margin-top: 0; margin-left: 4;">');

			include('MessageBox/Form.php');

			echo('</body>');
		echo('</html>');

    exit();
}

function isMessageBox()
{
	global
		$MB_YES_x,
		$MB_NO_x;

	if (isset($MB_YES_x))
		return 'Y';
	elseif (isset($MB_NO_x))
		return 'N';
}

function writeMessageLine()
{
		echo('<html>');
		echo('<head>');
			include('Stylesheet.php');
			echo('<title>Message line</title>');
		echo('</head>');

		echo('<body style="margin-top: 0; margin-left: 4;">');

				echo(writeMessages());

			echo('</body>');
		echo('</html>');
		exit();
}


function writeMaxChar($text, $count, $tooltip=false)
{
	$str = '';
	if (strlen($text) > $count)
	{
		if ($tooltip)
			$str .= '<span title="'.$text.'">';

		$str .= substr($text, 0, $count).'...';

		if ($tooltip)
			$str .= '</span>';

	}
	else
	{
		$str = $text;
	}

	return $str;
}

// Delar upp en text så att det finns en space på minst var X teckens avstånd.
//
// $count Talar om på vilken position som själva space brytet blir.
//        alltså får orden vara $count-1 långa.

function writeWrapedText($text, $count, $maxLength = NULL, $trimChars = NULL)
{
	if (!empty($maxLength))
		$text = substr($text, 0, $maxLength);

	$newStr = '';
	while(strlen($text))
	{
		if (strlen($text) > $count)
		{
			$tmpStr = substr($text, 0, $count);
			$pos = max(strrpos($tmpStr, " "), strrpos($tmpStr, "\n"), strrpos($tmpStr, "\r"));

			// not found...
			if ($pos === false) // note: three equal signs
			{
					// Lägger till text chunken och ersätter sista tecknet med newline
					$newStr .= substr($tmpStr, 0, $count-2)."\r\n";

					$text = substr($text, $count-2);
			}
			else
			{
				$newStr .= substr($text, 0, $pos+2);
				$text = substr($text, $pos+2);
			}
		}
		else
		{
			$newStr .= substr($text, 0);

			// Fixar problemet med att göra daniel[b]nospaces[/b] till daniel [b]nospaces[/b]
			if (strlen($text) == $count)
			{
				$newStr .=' ';
			}

			$text = substr($text, strlen($text));
		}
	}

	// debugEcho('"'.nl2br(str_replace(' ', '&nbsp;',$cow)).'" len'.strlen($cow).' <b>len:'.strlen($newStr).' "'.nl2br(str_replace(' ', '&nbsp;',$newStr)).'</b>"<br><br>');

	if (empty($trimChars))
		$newStr = trim($newStr);
	else
		$newStr = trim($newStr, $trimChars);

        if (isset($maxLength))
        {
	$maxLength = min($maxLength, strlen($newStr));
	if ($maxLength>0)
		$newStr = substr($newStr, 0, $maxLength);
        }

	return $newStr;
}

function writeAttributeBegin($caption=null)
{
	global
		$gStrAttr,
		$gStrCaption;

	$gStrAttr = '';
	$gStrCaption = $caption;
}

function writeAttribute($str, $strTail='', $strHead ='')
{
	global
		$gStrAttr;

	if (!emptyString($str))
	{
		if (!emptyString($gStrAttr))
			$gStrAttr .= ', ';

		$gStrAttr .= $strHead.$str.$strTail;
	}
}

function writeAttributeEnd()
{
	global
		$gStrAttr,
		$gStrCaption;

	echo($gStrCaption);
	if (!emptyString($gStrAttr))
		echo(' <i>('.$gStrAttr.')</i>');
}

function getAttributeEnd()
{
	global
		$gStrCaption,
		$gStrAttr;

	$str = $gStrCaption;
	if (!emptyString($gStrAttr))
		$str .= ' <i>('.$gStrAttr.')</i>';

	return $str;
}

// Fron php.net forum.
function array_diff_assoc_recursive($array1, $array2)
{
	foreach($array1 as $key => $value)
	{
		if(is_array($value))
		{
			if(!is_array($array2[$key]))
			{
				$difference[$key] = $value;
			}
			else
			{
				$new_diff = array_diff_assoc_recursive($value, $array2[$key]);
				if($new_diff != FALSE)
				{
					$difference[$key] = $new_diff;
				}
			}
		}
		else
		{
			if (gettype($array2[$key]) == 'double' || gettype($array2[$key]) == 'float')
				$array2[$key] = (double)((string)$array2[$key]);

			if (gettype($value) == 'double' || gettype($value) == 'float')
				$value = (double)((string)$value);

			if((!is_array($array2) || !array_key_exists($key, $array2)) || (isset($array2[$key]) && isset($value) && $array2[$key] != $value))
			{
				$difference[$key] = $value;
			}
		}
	}
	return !isset($difference) ? 0 : $difference;
}

//
function &arrayIntersect(&$arr1, &$arr2, $compare)
{
	if (!is_array($arr1) || !is_array($arr2))
		return false;

	foreach ($arr2 as $key => $val)
	{
		//echo('<br>Compare: '.$key.' value: '.$arr1[$key].' != '.$arr2[$key]);

		if (is_array($arr1[$key]) && is_array($arr2[$key]))
		{
			//echo('<br>array '.$key.' : '.$val);
			$retArr[$key] = arrayIntersect($arr1[$key], $arr2[$key], $compare);
		}
		elseif ($compare)
		{
		 	if ((string)$arr1[$key] != (string)$arr2[$key])
				$retArr[$key] = $arr1[$key];
		}
		else
		{
			$retArr[$key] = $arr1[$key];
		}
	}
	return $retArr;
}

/*
Ska fungera som arrayRemove. Men används ej idag.

function arraySubtract(&$arr1, &$arr2)
{
	if (!is_array($arr1) || !is_array($arr2))
		return false;

	foreach ($arr1 as $key => $val)
	{
		//echo('<br>Compare: '.$key.' value: '.$arr1[$key].' != '.$arr2[$key]);

		if (is_array($arr1[$key]) || is_array($arr2[$key]))
		{
			//echo('<br>array '.$key.' : '.$val);
			arraySubtract($arr1[$key], $arr2[$key]);
		}
		elseif ($arr1[$key] != $arr2[$key])
		{
			//echo('<br>diff: '.$key.' value: '.$arr1[$key].' != '.$arr2[$key]);
			unset($arr1[$key]);
		}
	}
	return true;
}
*/

// Tar bort de nycklars som finns i arr1 ifrån arr2.
//
// arr1 kommer innehålla de som tagits bort ifrån arr2.(man kan inte ta bort det som inte finns)
// arr2 kommer innehålla de som inte fanns i arr1.
//
// arr1('KEY1' =>'Value 1', 'KEY2' =>'Value 2');
// arr2('KEY1' =>'Value 1', 'KEY3' =>'Value 3');
//
// RETURN
// arr1('KEY1');
// arr2('KEY3');
// TRUE om ändrad
//
// Argument.
//
// remove - false lämnar arr1 oberörd,
//          true  De nycklar som tas bort ifrån arr2, tas bort ifrån arr1.
//								Behåller de nycklar som ej fanns i arr2.
//
// mark   -


// OldPrices, Basket

function arrayRemoveEmpty(&$arr1, $remove=false, $trimChars=null)
{
	$dummy = array();
	return arrayRemoveEx($arr1, $arr1, $dummy, false, $remove, false, true, $trimChars);
}

function arrayRemoveEqual(&$arr1, &$arr2, $remove=false, $trimChars=null)
{
	$dummy = array();
	return arrayRemoveEx($arr1, $arr2, $dummy, false, $remove, true, false, $trimChars);
}

function arrayRemove(&$arr1, &$arr2, $remove=false, $trimChars=null)
{
	$dummy = array();
	return arrayRemoveEx($arr1, $arr2, $dummy, false, $remove, false, false, $trimChars);
}

function arrayRemoveEx(&$arr1, &$arr2, &$arr3, $mark, $remove, $compare, $removeempty, $trimChars)
{
	$change = false;

	if (is_array($arr1))
	{
		foreach ($arr1 as $key => $val)
		{
			//echo('<br>Compare: '.$key.' value: '.$arr1[$key].' != '.$arr2[$key]);

			if (is_array($arr1[$key]) && is_array($arr2[$key]))
			{
				//echo('<br>array '.$key.' : '.$val);
				if(arrayRemoveEx($arr1[$key], $arr2[$key], $arr3[$key], $mark, $remove, $compare, $removeempty, $trimChars))
					$change = true;

				if (empty($arr1[$key]))
					unset($arr1[$key]);

				if (empty($arr2[$key]))
					unset($arr2[$key]);

				if (empty($arr3[$key]))
					unset($arr3[$key]);
			}
			elseif ($compare && (string)$arr1[$key] == (string)$arr2[$key])
			{
				unset($arr2[$key]);
				$change = true;
			}
			elseif ($removeempty && emptyString($arr2[$key]))
			{
				unset($arr2[$key]);
				$change = true;
			}
			elseif (isset($trimChars))
			{
				$arr2[$key] = trim($arr2[$key], $trimChars);
			}
			elseif (!$compare && !$removeempty && isset($arr2[$key]))
			{
				//echo('<br>remove: '.$key.' value: '.$arr1[$key].' != '.$arr2[$key]);
				unset($arr2[$key]);
				$change = true;
			}
			else
			{
				if ($mark && (string)$arr1[$key] != (string)$arr2[$key])
				{
					$arr3[$key] = 'Y';
				}

				if ($remove)
					unset($arr1[$key]);
			}
		}
	}
	return $change;
}

/*
Routine Description:

	Kopierar värden ifrån $arr1 till $arr2

	arr1 kommer innehålla de som lagts till i arr2.
	arr2 kommer innehålla både arr1 och arr2 utan dubletter.

	arr1('KEY1', 'KEY2');
	arr2('KEY1', 'KEY3');

	RETURN
	arr1('KEY2');
	arr2('KEY1', 'KEY2', 'KEY3');
	TRUE om ändrad

Arguments:
	$arr1			- De nycklar och värden som appliceras på arr2
	$arr2 		- Resultatet.
	$arr3 		- Om mark är true får arr3 alla de nycklar och värden
							som kopierats ifrån arr1 till arr2
	$mark 		- @todo: om arr3 är null är mark false, om arr3 är en array är mark true.
	$remove		- Om true, tar bort de värden ifrån arr1 som gick att lägga till i arr2.
	$replace  - Om true lägger till och ersätter existerande värden i arr2.
	$exist    - Om true, arr1 värdet måste existera i arr2 för att ersätta värdet. (replace måste vara true)
	$createkeys  - Skapar alla arr1:s nycklar i arr2. Behåller existernade värden i arr2 det andra sätts till null.
	$ignoreEmptycompare -
	$trimChars

Return Value:

*/
function arrayAddReplace(&$arr1, &$arr2, $remove = true, $removeEmptyArray = true)
{
	$dummy = array();
	return arrayAddEx($arr1, $arr2, $dummy, false, $remove, true, false, false, false, null, $removeEmptyArray);
}

function arrayAdd(&$arr1, &$arr2, $remove=true)
{
	$dummy = array();
	return arrayAddEx($arr1, $arr2, $dummy, false, $remove, false, false, false, false);
}

function arrayAddReplaceExist(&$arr1, &$arr2)
{
	$dummy = array();
	return arrayAddEx($arr1, $arr2, $dummy, false, false, true, true, false, false);
}

function arrayAddEx(&$arr1, &$arr2, &$arr3, $mark, $remove, $replace, $exist, $createkeys, $ignoreEmptyCompare=false, $trimChars=null, $removeEmptyArray = true)
{
	$change = false;
	if (!isset($arr2))
		$arr2 = array();

	if (is_array($arr1) && (is_array($arr2) || $replace) )
	{
		foreach ($arr1 as $key => $val)
		{
			//debugEcho('<br>add Compare: KEY('.$key.') VALUE: ('.$arr1[$key].' != '.$arr2[$key].')');

			// ser till att det är av samma typ.
			// @todo: Ska bara ändra arr2 om $replace är true.
			if (is_array($arr1[$key]) && !is_array($arr2[$key]))
			{
				//debugEcho('Set arr2 to array.');
				$arr2[$key] = array();
			}
			// @todo: Ska bara ändra arr2 om $replace är true.
			elseif (!is_array($arr1[$key]) && is_array($arr2[$key]))
			{
				//debugEcho('Set arr2 to null.');
				$arr2[$key] = NULL;
			}

			if (is_array($arr1[$key]) && is_array($arr2[$key]))
			{
				//debugEcho('<br>add array KEY('.$key.') : VALUE:('.$val.')');

				if (arrayAddEx($arr1[$key], $arr2[$key], $arr3[$key], $mark, $remove, $replace, $exist, $createkeys, ignoreEmptyCompare, $trimChars))
				{
					$change = true;
				}

				// Vi ska inte ha några tomma arrayer.
				if ($removeEmptyArray)
				{
					if (empty($arr1[$key]))
						unset($arr1[$key]);

					if (empty($arr2[$key]))
						unset($arr2[$key]);

					if (empty($arr3[$key]))
						unset($arr3[$key]);
				}
			}
			elseif ($ignoreEmptyCompare && emptyString($arr1[$key]) && emptyString($arr2[$key]))
			{
				//debugEcho(' ---- add ignore empty:');
			}
			elseif ($createkeys)
			{
				//debugEcho(' ---- CreateKeys:');
				if (is_array($arr2) && !key_exists($key, $arr2))
				{
					$arr2[$key] = NULL;
				}
			}
			elseif (!isset($arr2[$key]) || ($replace && (string)$arr2[$key] != (string)$arr1[$key]))
			{
				//debugEcho(' ---- add start:');
				if (!$exist || (is_array($arr2) && key_exists($key, $arr2)))
				{
					if ($mark)
					{
						if (!is_array($arr3) || !key_exists($key, $arr3))
							$arr3[$key] = $arr2[$key];
						else if ((string)$arr3[$key] == (string)$arr1[$key])
							unset($arr3[$key]);
					}

					if (isset($trimChars))
						$arr2[$key] = trim($arr1[$key], $trimChars);
					else
						$arr2[$key] = $arr1[$key];
				}

				$change  = true;
			}
			else
			{
				//debugEcho(' ---- Else:'.((string)$arr2[$key] != (string)$arr1[$key]));
				//debugEcho(' ---- Else:'.(gettype($arr2[$key]).' '.gettype($arr1[$key])));
				if ((string)$arr1[$key] != (string)$arr2[$key] && $mark)
				{
					$arr3[$key] = 'Y';
				}

				if ($remove)
					unset($arr1[$key]);
			}
		}
	}

	return $change;
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

define('ARRAY_SORT_NONE',  1);
define('ARRAY_SORT_DATE',  2);
define('ARRAY_SORT_LOWER', 3);

function arraySort(&$sortArr_InOut, $sortColumnArr_, $flag = SORT_ASC, $columnType = ARRAY_SORT_NONE)
{
  foreach($sortArr_InOut as $key => $val)
  {
		foreach ($sortColumnArr_ as $sortColumn)
		{
			if ($columnType == ARRAY_SORT_LOWER)
			{
				$sortarr[$sortColumn][$key] = strtolower($val[$sortColumn]);
			}
			elseif ($columnType == ARRAY_SORT_DATE)
			{
				$sortarr[$sortColumn][$key] = strtotime($val[$sortColumn]);
			}
			else
			{
				$sortarr[$sortColumn][$key] = $val[$sortColumn];
			}
   	}
  }

	if (!empty($sortarr))
	{
		$evalStr = '';
		foreach ($sortColumnArr_ as $sortColumn)
		{
			if (!empty($evalStr))
				$evalStr .= ',';

			$evalStr .= '$sortarr[\''.$sortColumn.'\']';
		}

		// Vi behöver kopiera $sortArr_InOut eftersom array_multisort inte sorterar
    // en reference.
		$cloneSortArr = $sortArr_InOut;
		$evalStr = 'array_multisort('.$evalStr.', $flag, $cloneSortArr);';
		eval($evalStr);
		$sortArr_InOut = $cloneSortArr;
  }
}


function randString ($pass_len = 10)
{
	$allchars = 'abcdefghijklnmopqrstuvwxyzABCDEFGHIJKLNMOPQRSTUVWXYZ';
	//$allchars = 'abcdefghijklnmopqrstuvwxyzABCDEFGHIJKLNMOPQRSTUVWXYZ0123456789';
	$string = '';

	mt_srand ((double) microtime() * 1000000);

	for ($i = 0; $i < $pass_len; $i++) {
		$string .= $allchars{mt_rand (0,strlen($allchars) -1)};
	}
  /* $string = substr(md5 (uniqid (rand())), 0, $pass_len); */

	return $string;
}


function echoS($string)
{
	if (empty($string))
		return '--';
	else
		return $string;
}

//

// Felhantering.
//
// messages: är en array med felmeddelanden finns i form.php
// codes: är en array med felkoder från bl.a validate.php
//
// Constanter och modul variabler.

DEFINE ('MSGERR', 1);
DEFINE ('MSGINF', 2);

if (!isset($messages))
{
	$gStackMessage = array();
  $messages = array();
  $gMessageCount = 0;
  $gError = FALSE;
  $gGroup = 'DEFAULT';
}

function pushMessage()
{
	global
		$gMessageCode,
		$gMultiMessageCode,
		$gError,
		$gStackMessage;

	array_push($gStackMessage, array($gMessageCode, $gMultiMessageCode, $gError));
	clearMultiMessage();
	$gError = FALSE;
}

function popMessage()
{
	global
		$gMessageCode,
		$gMultiMessageCode,
		$gError,
		$gStackMessage;

	$arr = array_pop($gStackMessage);

	$gMessageCode = 		 $arr[0];
	$gMultiMessageCode = $arr[1];
	$gError = 					 $arr[2];
}

// Om man använder $message bör man skicka in null i $code..
// setMessage(NULL, 'Must enter company name.', MSGERR)
//
// Går nu att skicka in array med kod => felmeddelande så som vi
// får dom från foError.
// ex. setMessage($book->errorObj->getErrorText());
//
function setMessage($code, $message='', $type = MSGERR)
{
	global
	  $gGroup,
		$gMessageCode,
		$gMessageCount,
		$gError;

	if (empty($code))
	{
		$gMessageCount++;
		$code = 'COUNTCODE'.$gMessageCount;
	}

	if (is_array($code))
	{
		foreach($code as $codeKey => $messageVal)
		{
			$gMessageCode[$gGroup][$codeKey] = array($type, $messageVal);
		}
	}
	else
	{
	$gMessageCode[$gGroup][$code] = array($type, $message);
	}

	if ($type == MSGERR)
		$gError = true;
}

//
// Vi byggde den här istället för att använda addMultiMessage, pga
// att t.ex. i Rules kan man redigera flera regler samtidigt, och varje regel
// är en multimessage, men på varje regel finns flera headings, och varje heading
// är en egen group, som behöver egna fel/messages.
//
function setMessageGroup($group)
{
	global
		$gGroup;

	$gGroup = $group;
}

//
function clearMessageGroup()
{
	global
		$gGroup;

	$gGroup = 'DEFAULT';
}

//

function isMessage($group = NULL, $code = NULL)
{
	global
		$gMessageCode;

	if (!empty($group) && !empty($code))
	{
		return !empty($gMessageCode[$group][$code]);
	}
	elseif (!empty($group))
	{
		return !empty($gMessageCode[$group]);
	}

	return !empty($gMessageCode);
}

//

function getMessage($group = NULL, $code = NULL)
{
	global
		$gMessageCode;

	if (!empty($group) && !empty($code))
	{
		return $gMessageCode[$group][$code];
	}
	elseif (!empty($group))
	{
		return $gMessageCode[$group];
	}

	return $gMessageCode;
}

//

function clearMessage($group = NULL, $code = NULL)
{
	global
		$gMessageCode;

	if (!empty($group) && !empty($code))
	{
		$gMessageCode[$group] = NULL;
	}
	elseif (!empty($group))
	{
		$gMessageCode[$group] = NULL;
	}
	else
	{
		$gMessageCode = NULL;
  }
}

//
// Kontrollerar om det finns vanliga fel eller multifel.
function isError()
{
	global
		$gError;

	return $gError;
}

function clearError()
{
	global
		$gError;

	$gError = false;
}

//

function clearMultiMessage()
{
	global
		$gMultiMessageCode;

	$gMultiMessageCode = NULL;
	clearMessage();
}

//

function addMultiMessage($id, $subid = NULL)
{
	global
		$gMultiMessageCode,
		$gMessageCode;

	if (empty($subid))
	{
		if (isset($gMultiMessageCode[$id]))
			arrayAdd($gMultiMessageCode[$id], $gMessageCode);

		$gMultiMessageCode[$id] = $gMessageCode;
	}
	else
	{
		if (isset($gMultiMessageCode[$id][$subid]))
			arrayAdd($gMultiMessageCode[$id][$subid], $gMessageCode);

		$gMultiMessageCode[$id][$subid] = $gMessageCode;
	}

	clearMessage();
}

//

function isMultiMessage($id = NULL, $subId = NULL)
{
	global
	  $gGroup,
		$gMultiMessageCode;

	if (!empty($id))
	{
		if (!empty($gGroup))
		{
			if (empty($subId))
				return isset($gMultiMessageCode[$id][$gGroup]);
			else
				return isset($gMultiMessageCode[$id][$subid][$gGroup]);
		}
		else
		{
			if (empty($subId))
				 return isset($gMultiMessageCode[$id]);
			else
				return isset($gMultiMessageCode[$id][$subid]);
		}
	}

	return !empty($gMultiMessageCode);
}

//

function setResultMessageState()
{
	global
		$gMessageOutputState;

	$gMessageOutputState = 'result';
}

//

function isResultMessageState()
{
	global
		$gMessageOutputState;

	if ( $gMessageOutputState == 'result')
		return true;

	return false;
}

//

function &writeMessages($id = NULL, $subid = NULL)
{
	global
	  $gGroup,
		$messages,
		$gMessageCode,
		$gMultiMessageCode;

	if (empty($id))
		$codes = $gMessageCode[$gGroup];
	else if (empty($subid))
		$codes = $gMultiMessageCode[$id][$gGroup];
	else
		$codes = $gMultiMessageCode[$id][$subid][$gGroup];

	if ( !empty($codes) )
	{
		$msg = '<ul class="cssErrorMessage">';

		while( list($key, $val) = each ($codes) )
		{
			if (empty($val[1]))
				$text = $messages[$key];
			else
				$text = $val[1];

			if ( empty($text) )
				$msg .= '<li><span class="cssErrorColor">Error code: '.$key.'</span></li>';
			else
				if ( $val[0] == MSGERR )
					$msg .= '<li><span class="cssErrorColor">'.$text.'</span></li>';
				else if ( $val[0] == MSGINF )
					$msg .= '<li><span class="cssInfoColor">'.$text.'</span></li>';
				else
					ASSERTLOG(true, LOG_SYSTEMERROR, 'writeMessages: can\'t find '.$val[0], EL_LEVEL_3, ECAT_SYSTEM_CORE);
		}
		$msg .= '</ul>';
	}

	return $msg;
}

//

function isEqualIgnoreCase($str1, $str2)
{
	if (!strcasecmp($str1, $str2))
		return true;
	elseif (foStrToLower($str1) == foStrToLower($str2))
		return true;
	else
		return false;
}

//

function &foStrToUpper(&$str)
{
	$retStr = strtr(strtoupper($str), 'åäöéüáàèìî', 'ÅÄÖÉÜÁÀÈÌÎ');
	return $retStr;
}

//

function &foStrToLower(&$str)
{
	$retStr = strtr(strtolower($str), 'ÅÄÖÉÜÁÀÈÌÎ', 'åäöéüáàèìî');
	return $retStr;
}

//

function foMinDate($num1, $num2, $format = 'dMY')
{
	if (isset($num1) && isset($num2))
		return date($format, min(strtotime($num1), strtotime($num2)));
	else if (isset($num1))
		return date($format, strtotime($num1));
	else if (isset($num2))
		return date($format, strtotime($num2));
}

//

function foMaxDate($num1, $num2, $format = 'dMY')
{
	if (isset($num1) && isset($num2))
		return date($format, max(strtotime($num1), strtotime($num2)));
	else if (isset($num1))
		return date($format, strtotime($num1));
	else if (isset($num2))
		return date($format, strtotime($num2));
}

//

function foMin($num1, $num2)
{
	if (isset($num1) && isset($num2))
		return min($num1, $num2);
	else if (isset($num1))
		return $num1;
	else if (isset($num2))
		return $num2;
}


//

function foMax($num1, $num2)
{
	if (isset($num1) && isset($num2))
		return max($num1, $num2);
	else if (isset($num1))
		return $num1;
	else if (isset($num2))
		return $num2;
}



function &getArrayTable(&$arr, $horizontalCaption = false, $horizontalData = true, $columnArr = null, $border = true, $rowCaption = true, $tableCaption = true, $parseCodeText = false)
{
	if ($parseCodeText)
		include_once('GlobalCode.php');

  foreach ($arr as $key => $val)
  {
  	if (isset($columnArr))
  	{
    	$column = $columnArr[$key]['name'];

    	if (!empty($columnArr[$key]['width']))
    		$tdTags = 'width='.$columnArr[$key]['width'];
    }
    else
    {
      $column = $key;
    }

		if (isset($column))
    {
      if ($horizontalCaption)
      {
        if (empty($dataRow))
          $dataRow .= '<tr>';

        if ($tableCaption)
        {
          if (empty($colRow))
            $colRow .= '<tr>';

        	$colRow .= '<td '.$tdTags.' class="cssArrayTableColumn">'.$column;
        }

        if ($horizontalData)
        {
        	$dataRow .= $colRow.'<tr>';
        	$colRow = '';
        }
      }
      else
      {
    	  $dataRow .= '<tr>';
        if ($tableCaption)
	        $dataRow .= '<td class="cssArrayTableColumn">'.$column;
      }

      if (is_array($val))
  		{
  		    $dataRow .= '<td '.$tdTags.' class="cssArrayTableCell">';
		  	  $dataRow .= getArrayTable($val, $horizontalCaption, $horizontalData, $columnArr, false, $rowCaption, $rowCaption);
      }
      else
      {
         $dataRow .= '<td '.$tdTags.' class="cssArrayTableCell">';

         if($parseCodeText)
    	   	$dataRow .= parseCodeText($val);
    	   else
    	   	$dataRow .= nl2br($val);
      }
    }
  }

	if ($border)
		$str = '<table width="100%" class="cssArrayTableOuter">';
  else
    $str = '<table width="100%" class="cssArrayTableInner">';

  $str .= $colRow;
  $str .= $dataRow;
  $str .= '</table>';
  return $str;
}

function &getColumnTable(&$arr, $columns=2, $length=2600, $parseCodeText = false)
{
  $len = 0;

  $width = round(100/$columns).'%';
  $str = '<table width="100%" class="cssArrayTableOuter">';
	$str .= '<tr><td valign="top" width="'.$width.'">';
  foreach ($arr as $key => $val)
  {
  	 $value = '<br><b>'.$key.'</b><br>'.$val.'<br>';
		 $len += strlen($value);

		 if ($parseCodeText)
		 	$str .= parseCodeText($value);
		 else
		 	$str .= $value;

		 if ($len > $length)
		 {
		 	 $len = 0;
			 $col++;
			 if ($col < $columns)
			 	 $str .= '<td valign="top" width="'.$width.'">';
		 }
  }

  $str .= '</table>';
  return $str;
}

/**
* Används för att köra en preg_match, med lite enklare kod.
* <code>
*		$search = '123423COUNT';
*
*		// Hämtar ut 123423
*		$result = getPreg('/[0-9]*'.'/', $search);
*	</code>
*
* @param	string	$pattern
*
* @param	string	$string
*
* @param	string	$argument
*
* @return	-
*
* @access	public
*/
function &getPreg($pattern, $string, $argument=0)
{
	preg_match($pattern, $string, $match);
	return $match[$argument];
}

/**
*	Konverterar en tid (+10:00 eller -10:34) till relative items, som används av strtotime.
*
*	@param 	string 	$time
*									Tiden som ska konverteras, ie. +10:00 eller -10:00.
*
*	@return string 	Konverterad tid. "+1 hour +34 minutes"
*
*	@access public
*/
function getRelativeItemsFromTime($time)
{
	preg_match('/([+-]{0,1})([0-9]{1,2}):([0-9]{1,2})/', $time, $match);

	if (empty($match[1]))
		$match[1] = '+';

	if (!empty($match[2]))
		$str = $match[1].$match[2].'hours ';

	if (!empty($match[3]))
		$str .= $match[1].$match[3].'minutes';

	if (empty($str))
		$str = '+0 hours';

	return $str;
}

/**
*	Möjliggör enum syntax för att definera constanter.
*
* För varje inparamter definas en konstant som motsvarar paramterens siffra i
*	ordningen
*
*	Originalversion: I en kommentar på http://se.php.net/define
*
*	Example:
*	<code>
* enum( "COLOR_RED", "COLOR_GREEN", "COLOR_BLUE" );
*	echo(COLOR_RED.' '.COLOR_GREEN.' '.COLOR_BLUE); 	// 0 1 2
*	</code>
*
*	@param 	string 	...
*									Godtyckligt antal strängar med godtyckliga namn kan skickas in.
*
*/
function enum()
{
	$ArgC = func_num_args();
	$ArgV = func_get_args();

	for($Int = 0; $Int < $ArgC; $Int++)
		define($ArgV[$Int], $Int);
}

/**
* Som enum fast varje define kopplas mot en egen bit
*
*	Example:
*	<code>
* bitset( "COLOR_RED", "COLOR_GREEN", "COLOR_BLUE" );
*	echo(COLOR_RED.' '.COLOR_GREEN.' '.COLOR_BLUE); 	// 1 2 4
*	</code>
*
*	@param 	string 	...
*									Godtyckligt antal strängar med godtyckliga namn kan skickas in.
* @see enum
*/
function bitset()
{
	$ArgC = func_num_args();
	$ArgV = func_get_args();

	for($Int = 0; $Int < $ArgC; $Int++)
		define($ArgV[$Int], pow(2,$Int));
}

function getCharRepeatSize($char_, $text_, $offset_ = 0, $direction_ = DIR_FORWARD)
{
	$pos = $offset_;

	if($direction_ == DIR_FORWARD)
		$iterator = 1;
	else
		$iterator = -1;

	while($text_{$pos} == $char_)
	{
		$pos += $iterator;
	}

	return $iterator * ($pos - $offset_);
}

?>