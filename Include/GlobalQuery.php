<?php

/*
@todo: när det gäller $depends måste AddJoin anroppas före addwhere och addCoulmn för att den skall fungera som den skall.


Class som håller reda på vilka tabeller som används i en fråga och joinar bara in de tabeller
som behövs.

// --------------  Select query -------------------

$query = new foQuery(QUERY_SELECT);

$query->addColumn('user', 'firstname');
$query->addColumn('user', 'lastname');
$query->addWhere('user', 		'user_id='.escN(getUserIdFromSession()));
$query->addJoin('user');

debugEcho($query->getQuery());

// -------------- Select

$query = new foQuery(QUERY_SELECT);

$query->addColumn('user', 'firstname');
$query->addColumn('user', 'lastname');

$query->addColumn('company', 'company_id',
									'company', 'company_name');

$query->addWhere('user', 		' 	 user_id='.escN(getUserIdFromSession()),
								 'company', ' or company_id='.escN(getCompanyIdFromSession()));

$query->addJoin('user',		 'user');
$query->addJoin('company', 'inner join company on(user.company_id = company.company_id)',
								'buyer', 	 'inner join buyer on (company_id = company.company_id)');

$query->addOrder('user', 		'firstname desc',
								 'company', 'company_name asc');

debugEcho($query->getQuery(0, 10));



// --------------  Insert query -------------------

$firstName = 'kalle';
$lastName = 'pettersson';

$query = new foQuery(QUERY_INSERT);

$query->addValue('user', 'firstname', escS($firstName));
$query->addValue('user', 'lastname',  escS($lastName));

debugEcho($query->getQuery());

// --------------  Update query -------------------

$query = new foQuery(QUERY_UPDATE);

$query->addColumn('user', 'firstname', escS($firstName));
$query->addColumn('user', 'lastname',  escS($lastName));

$query->addWhere('user', 'user_id='.escN(getUserIdFromSession()));
debugEcho($query->getQuery());


// --------------  Delete query ------------------

$query = new foQuery(QUERY_DELETE);
$query->addWhere('user', 'user_id='.escN(getUserIdFromSession()));

debugEcho($query->getQuery());
$query->execute();

*/
define ('QUERY_SELECT', 					 1);
define ('QUERY_INSERT', 					 2);
define ('QUERY_UPDATE', 					 3);
define ('QUERY_DELETE', 					 4);
define ('QUERY_REPLACE',    			 5);
define ('QUERY_INSERT_UPDATE',     6);
define ('QUERY_INSERT_IGNORE',     7);

class foQueryCounter
{
	var
		$_start,
		$_name,
		$_idx,
		$_last,
		$_value;

	function foQueryCounter($name)
	{
		$this->_idx = 0;
		$this->_name = $name;
	}

	function incId()
	{
		$this->_idx++;
		unset($this->_value);
	}

	function setId($value)
	{
		$this->_value = $value;
	}

	function getId()
	{
		return $this->getValue();
	}

	function getLastId()
	{
		return $this->_last;
	}

	function getStartId()
	{
		return $this->_start;
	}

	function getEndId()
	{
		return $this->_start+$this->_idx;
	}

	function getValue()
	{
		if (!empty($this->_value))
			return $this->_value;
		else
			return $this->_idx;
	}

	function getDatabaseValue($value)
	{
		if (!isset($this->_start))
			$this->_load();

		$this->_last = $this->_start+$value;
		return $this->_last;
	}

	function _load()
	{
		executeOnDb('LOCK TABLES counters WRITE');
		$counter = executeOnDbReturnOneColumn('select '.$this->_name.' from counters');
		$counter++;
		$newCounter=$counter+$this->_idx;
		executeOnDb('update counters set '.$this->_name.'='.escN($newCounter));
		executeOnDb('UNLOCK TABLES');

		$this->_start = $counter;
	}

}

class foQuery
{
	var
		$enabled,
		$type,
		$tables,
		$columns,
		$values,
		$wheres,
		$joins,
		$depends,
		$groups,
		$orders;

		function foQuery($type)
		{
			$this->enabled = true;
			$this->type = $type;
			$this->tables = array();
			$this->columns = array();
			$this->values = array();
			$this->wheres = array();
			$this->joins = array();
			$this->depends = array();
			$this->groups = array();
			$this->orders = array();
		}

		/**
		*	Enable so that an execute will commit to the database.
		*
		*	@access public
		*/
		function enable()
		{
			$this->enabled = true;
		}

		/**
		*	Disable so that an execute will NOT commit to the database.
		*
		*	@access public
		*/
		function disable()
		{
			$this->enabled = false;
		}

		/**
		*	Check if execute is enabled to commit to the database.
		*
		* @return	bool	true if enabled.
		*	@access public
		*/
		function isEnabled()
		{
			return $this->enabled;
		}

		function addTable($table, $sql=null)
		{
			$this->tables[$table] = true;

			if (isset($this->depends[$table]))
			{
				foreach($this->depends[$table] as $dependingTable)
					$this->addTable($dependingTable);
			}

			if (isset($sql))
				$this->joins[$table] = $sql;
		}

		function getTable($name = null)
		{
			if (isset($name))
				return $this->tables[$name];
			else
				return $this->tables;
		}

		// vid select.
		function addColumn($table, $field, $addTable=true)
		{
			if ($addTable && !empty($table))
				$field = $table.'.'.$field;

			if (!empty($table))
				$this->addTable($table);

			$this->columns[$field] = true;
		}

		// vid insert/replace/update.
		// @todo: Om det är $this->type == QUERY_INSERT
		// så ska $this->table bara få innehålla en tabell.
		//   Så man inte kan göra så här
		//		$query->addValue('product', 'changed_time',			escV('now()'));
		//		$query->addValue('baseprice', 'changed_by',				getUserIdFromSession());
		function addValue($table, $field, &$value, $addTable=true)
		{
			if ($addTable && $this->type != QUERY_INSERT)
				$field = $table.'.'.$field;

			if (is_object($value))
				$this->values[] = array("field" => $field, "value" => $value->getValue(), "object" => &$value);
			else
				$this->values[] = array("field" => $field, "value" => $value);

			$this->addTable($table);
			$this->columns[$field] = true;
		}

		/**
		* Description
		*
		* @param	string	$table_
		*
		* @param	string	$column_
		*
		* @param	string	$arr_
		*
		* @param	string	$splitChar_
		*
		* @return	-
		*
		* @access	public
		*/
		function addWhereStringOr($table_, $column_, $arr_, $prefixChar_ = NULL, $suffixChar_ = NULL, $splitChar_ = '/')
		{
			if (is_array($arr_))
			{
				$arr =& $arr_;
			}
			else
			{
				$arr =& explode($splitChar_, trim($arr_, $splitChar_));
			}

			// Istället för count (finns flera värden att köra REGEXP på)
			if (isset($arr[1]))
			{
				$val = $prefixChar_.implode($suffixChar_.'|'.$prefixChar_, $arr).$suffixChar_;
				// Kan inte göra escS här eftersom vi vinte vill escape:a visa tecken för REGEXP
				$this->addWhere($table_, $column_.' REGEXP \''.str_replace('\\', '\\\\', $val).'\'');
			}
			else
			{
				$val = $prefixChar_.$arr[0].$suffixChar_;
				$this->addWhere($table_, $column_.' LIKE '.escS($val, '%', ESC_BOTH));
			}
		}

		/**
		* Description
		*
		* @param	string	$table_
		*
		* @param	string	$column_
		*
		* @param	string	$arr_
		*
		* @param	string	$splitChar_
		*
		 /'
		* @return	-
		*
		* @access	public
		*/
		function addWhereStringIn($table_, $column_, $arr_, $splitChar_ = '/')
		{
			if (is_array($arr_))
			{
				$arr =& $arr_;
			}
			else
			{
				$arr =& explode($splitChar_, trim($arr_, $splitChar_));
			}

			// Istället för count (finns flera värden att köra IN på)
			if (isset($arr[1]))
			{
				$this->addWhere($table_, $column_.' in ('.inEscS($arr).')');
			}
			else
			{
				$this->addWhere($table_, $column_.' = '.escS($arr[0]));
			}
		}

		/**
		* Description
		*
		* @param	string	$table_
		*
		* @param	string	$column_
		*
		* @param	string	$arr_
		*
		* @param	string	$splitChar_
		*
		 /'
		* @return	-
		*
		* @access	public
		*/
		function addWhereNumericIn($table_, $column_, $arr_, $splitChar_ = '/')
		{
			if (is_array($arr_))
			{
				$arr =& $arr_;
			}
			else
			{
				$arr =& explode($splitChar_, trim($arr_, $splitChar_));
			}

			// Istället för count (finns flera värden att köra IN på)
			if (isset($arr[1]))
			{
				$this->addWhere($table_, $column_.' in ('.inEscN($arr).')');
			}
			else
			{
				$this->addWhere($table_, $column_.' = '.escN($arr[0]));
			}
		}
		//addWhere($table, $sql, $table, $sql, ...)
		function addWhere()
		{
			$sql = '';
		  $tmp = array();
			$arr = func_get_args();
			$c = func_num_args();
			for ($i=0; $i<$c; $i++)
			{
				$table = $arr[$i];
				$sql =   $arr[++$i];

				if (!empty($sql))
				{
					if (isset($table))
					{
						$this->addTable($table);
						$sql = $table.'.'.$sql;
					}

					$tmp[] = $sql;
				}
			}

			if (!empty($tmp))
				$this->wheres[] = $tmp;
		}

		// @todo: byt plats på $sql och $table2
		function addJoin($table1, $sql, $table2 = null)
		{
			$this->joins[$table1] = $sql;
			if (!empty($table2))
				$this->depends[$table1][] = $table2;
		}

		function addInner($table1, $field1, $table2, $field2)
		{
			$this->joins[$table1] = 'inner join '.$table1.' on ('.$table2.'.'.$field2.'='.$table1.'.'.$field1.')';
			$this->depends[$table1][] = $table2;
		}

		function addLeft($table1, $field1, $table2, $field2)
		{
			$this->joins[$table1] = 'left join '.$table1.' on ('.$table2.'.'.$field2.'='.$table1.'.'.$field1.')';
			$this->depends[$table1][] = $table2;
		}

		// @todo: alla addStraight kommer med i frågan oavsätt om det används eller inte.
		function addStraight($table1, $field1, $table2, $field2)
		{
			$this->joins[$table1] = 'straight_join '.$table1;
			$this->addWhere($table1, $field1.'='.$table2.'.'.$field2);
			$this->depends[$table1][] = $table2;
		}

		function addOrder($table, $sql)
		{
			// @todo: SKA finnas något sånt här med null, på alla funktioner.
			if (isset($table))
			{
				$this->addTable($table);
				$orderBy = $table.'.';
			}

			$this->orders[] = $orderBy.$sql;
		}

		// addGroup($table, $sql, ...)
		function addGroup($table, $sql)
		{
			// @todo: SKA finnas något sånt här med null, på alla funktioner.
			if (isset($table))
			{
				$this->addTable($table);
				$groupBy = $table.'.';
			}
			$this->groups[] = $groupBy.$sql;
		}

		function &_compileWhere()
		{
			$sqlWheres = '';
			if (!empty($this->wheres))
				foreach($this->wheres as $arr)
				{
					if (!empty($sqlWheres)) $sqlWheres .= ' and ';

					$sqlTmp='';
					foreach($arr as $sql)
					{
						if (!empty($sqlTmp)) $sqlTmp .= ' or ';
						$sqlTmp .= $sql;
					}
					$sqlWheres .= $sqlTmp;
				}

			if ($this->type == QUERY_SELECT || $this->type == QUERY_DELETE)
			{
				if (!empty($this->values))
					foreach($this->values as $arr)
					{
						if (isset($arr[object]))
							$databaseValue = $arr[object]->getDatabaseValue($arr[value]);
						else
							$databaseValue = $arr[value];

						if (!empty($inValues[$arr[field]])) $inValues[$arr[field]] .= ', ';
						$inValues[$arr[field]] .= $databaseValue;
					}


				if (!empty($inValues))
					foreach($inValues as $field => $inSql)
					{
						if (!empty($sqlWheres)) $sqlWheres .= ' and ';
						$sqlWheres .= $field.' in ('.$inSql.')';
					}
			}

			return $sqlWheres;
		}

		function &_compileSelectQuery()
		{
			$sqlJoins = '';
			$sqlColumns = '';
			$sqlGroups = '';
			$sqlOrders = '';

			// lägger till kolumer till sql-frågan.
			if (!empty($this->columns))
				foreach($this->columns as $field => $value)
				{
					if (!empty($sqlColumns)) $sqlColumns .= ', ';
					$sqlColumns .= $field;
				}

			// lägger till joins till sql-frågan.
			if (!empty($this->tables) && !empty($this->joins))
				foreach($this->joins as $table => $val)
				{
					if (isset($this->tables[$table]))
						$sqlJoins .= ' '.$this->joins[$table].' ';
				}

			// lägger till where till sql-frågan.
			$sqlWheres = $this->_compileWhere();

			// lägger till group by till sql-frågan.
			if (!empty($this->groups))
				foreach($this->groups as $sql)
				{
					if (!empty($sqlGroups)) $sqlGroups .= ', ';
					$sqlGroups .= $sql;
				}

			// lägger till order by till sql-frågan.
			if (!empty($this->orders))
				foreach($this->orders as $sql)
				{
					if (!empty($sqlOrders)) $sqlOrders .= ', ';
					$sqlOrders .= $sql;
				}

			// skapar sql.
			$sql = 'select '.$sqlColumns.' from '.$sqlJoins;
			if (!empty($sqlWheres))
				$sql .= ' where '.$sqlWheres;

			if (!empty($sqlGroups))
				$sql .= ' group by '.$sqlGroups;

			if (!empty($sqlOrders))
				$sql .= ' order by '.$sqlOrders;

			return $sql;
		}

		function &_compileInsertReplaceQuery()
		{
			// hämtar först tabellen eftersom det bara kan finnas en tabell i en insert fråga.
			if (!empty($this->tables))
			{
				reset($this->tables);
				$sqlTables = key($this->tables);
			}

			// lägger till kolumer till sql-frågan.
			$sqlColumns = '';
			if (!empty($this->columns))
				foreach($this->columns as $field => $value)
				{
					if (!empty($sqlColumns)) $sqlColumns .= ', ';
					$sqlColumns .= $field;
				}

			if (!empty($this->values))
			{
				reset($this->values);
				list ($key, $arr) = each($this->values);
				while($arr)
				{
					if (!emptyString($sqlValues)) $sqlValues .= '), (';

					$sqlTmp='';
					foreach($this->columns as $field => $value)
					{
						if ($arr[field] == $field)
						{
							if (isset($arr[object]))
								$databaseValue = $arr[object]->getDatabaseValue($arr[value]);
							else
								$databaseValue = $arr[value];

							$currentValues[$field] = $databaseValue;
							list ($key, $arr) = each($this->values);
						}

						if (!emptyString($sqlTmp))
							$sqlTmp .= ', ';

						if (isset($currentValues[$field]))
							$sqlTmp .= $currentValues[$field];
						else
							$sqlTmp .= 'null';
					}
					$sqlValues .= $sqlTmp;
					$rows++;

					if ($rows>2000)
					{
						if ($this->type == QUERY_INSERT)
							$sql = 'insert ';
						else if ($this->type == QUERY_INSERT_IGNORE)
              $sql = 'insert ignore ';
						else
							$sql = 'replace ';

						// skapar sql.
						$sql .= ' into '.$sqlTables.' ('.$sqlColumns.') values ('.$sqlValues.')';

						$queries[] = $sql;

						$sqlValues = '';
						$rows = 0;
					}
				}
			}

			if ($rows>0)
			{
				if ($this->type == QUERY_INSERT)
					$sql = 'insert ';
				else if ($this->type == QUERY_INSERT_IGNORE)
					$sql = 'insert ignore ';
				else
					$sql = 'replace ';

				// skapar sql.
				$sql .= ' into '.$sqlTables.' ('.$sqlColumns.') values ('.$sqlValues.')';

				$queries[] = $sql;
			}

			return $queries;
		}

		function &_compileUpdateQuery()
		{
			// hämtar först tabellen eftersom det bara kan finnas en tabell i en update fråga.
			if (!empty($this->tables))
			{
				reset($this->tables);
				$sqlTables = key($this->tables);
			}

			if (!empty($this->values))
			{
				foreach($this->values as $arr)
				{
					if (isset($arr[object]))
						$databaseValue = $arr[object]->getDatabaseValue($arr[value]);
					else
						$databaseValue = $arr[value];

					if (!empty($sqlValues)) $sqlValues .= ', ';
					$sqlValues .= $arr[field].'='.$databaseValue;
				}
			}

			// lägger till where till sql-frågan.
			$sqlWheres = $this->_compileWhere();

			// skapar sql.
			$sql = 'update '.$sqlTables.' set '.$sqlValues;
			if (!empty($sqlWheres))
				$sql .= ' where '.$sqlWheres;

			return $sql;
		}

		function &_compileDeleteQuery()
		{
			// hämtar först tabellen eftersom det bara kan finnas en tabell i en delete fråga.
			if (!empty($this->tables))
			{
				reset($this->tables);
				$sqlTables = key($this->tables);

			// lägger till where till sql-frågan.
			$sqlWheres = $this->_compileWhere();

			// skapar sql.
			$sql = 'delete from '.$sqlTables;
			if (!empty($sqlWheres))
				$sql .= ' where '.$sqlWheres;
			}
			return $sql;
		}

		function getQuery($offset=null, $limit=null)
		{
			switch ($this->type)
			{
				case QUERY_SELECT:
					$sql = $this->_compileSelectQuery();

				break;
				case QUERY_INSERT:
				case QUERY_INSERT_IGNORE:
				case QUERY_REPLACE:
					$queries =& $this->_compileInsertReplaceQuery();
					$sql = $queries[0];

				break;
				case QUERY_UPDATE_CREATE:
				case QUERY_UPDATE:
					$sql = $this->_compileUpdateQuery();

				break;
					case QUERY_INSERT_UPDATE:
						// Kan endast användas för debuging.
	          $sql = 'debug '.$this->_compileUpdateQuery();

						$this->type = QUERY_INSERT_IGNORE;
						$queries = $this->_compileInsertReplaceQuery();
						for($i=0;isset($queries[$i]);$i++)
						{
							$sql .= $queries[$i];
						}
						$this->type = QUERY_INSERT_UPDATE;

					break;

				case QUERY_DELETE:
					$sql = $this->_compileDeleteQuery();

				break;
			}

			if (isset($offset) || isset($limit))
				$sql .= ' limit '.$offset.', '.$limit;

			return $sql;
		}
		
		function lock($lockKey_, $timeout_ = 60)
			{
			$status = executeOnDbReturnOneColumn('SELECT GET_LOCK('.escS($lockKey_).', '.escN($timeout_).') as status');
			TRACELOG($status != 1, LOG_SYSTEMERROR, 'foQuery: can\'t retrive lock on '.$lockKey_, EL_LEVEL_2, ECAT_SYSTEM_CORE);
		}
		
		function unlock($lockKey_)
		{
			$status = executeOnDbReturnOneColumn('SELECT RELEASE_LOCK('.escS($lockKey_).') as status');
			TRACELOG($status != 1, LOG_SYSTEMERROR, 'foQuery: can\'t unlock '.$lockKey_, ' maybe already unlock by another lock', EL_LEVEL_2, ECAT_SYSTEM_CORE);			
		}			

		function &execute($offset=null, $limit=null)
		{
			if ($this->enabled)
			{
				switch ($this->type)
				{
					case QUERY_SELECT:
						$sql = $this->_compileSelectQuery();

						if (isset($offset) || isset($limit))
							$sql .= ' limit '.$offset.', '.$limit;

						return executeOnDb($sql);

					break;
          case QUERY_INSERT:
          case QUERY_INSERT_IGNORE:
					case QUERY_REPLACE:
						$queries =& $this->_compileInsertReplaceQuery();
						for($i=0;isset($queries[$i]);$i++)
						{
							executeOnDb($queries[$i]);
						}
					break;
					case QUERY_UPDATE:
						$sql = $this->_compileUpdateQuery();
						return executeOnDb($sql);

          break;
					
					case QUERY_INSERT_UPDATE:
	          $sql = $this->_compileUpdateQuery();
	          
            $affected = executeOnDbReturnMatchedRows($sql);
            if ($affected == 0)
            {
            	$this->type = QUERY_INSERT_IGNORE;
	            $queries = $this->_compileInsertReplaceQuery();
              for($i=0;isset($queries[$i]);$i++)
              {
	            	executeOnDb($queries[$i]);
              }
              $this->type = QUERY_INSERT_UPDATE;
            }

					break;
					case QUERY_DELETE:
						$sql = $this->_compileDeleteQuery();
						if (!empty($sql))
						   return executeOnDb($sql);
					break;
				}
			}

			return null;
		}

		function writeHtml()
		{
			echo('<br><br><b>Tables used</b>');
			writeHtmlArray($this->tables);

			echo('<br><br><b>Columns used</b>');
			writeHtmlArray($this->columns);

			echo('<br><br><b>Values used</b>');
			writeHtmlArray($this->values);

			echo('<br><br><b>Where used</b>');
			writeHtmlArray($this->wheres);

			echo('<br><br><b>All Joins</b>');
			writeHtmlArray($this->joins);

			echo('<br><br><b>Group by</b>');
			writeHtmlArray($this->groups);

			echo('<br><br><b>Order by</b>');
			writeHtmlArray($this->orders);

			echo('<br><br><b>Query</b><br><br>');
			echo($this->getQuery());
			echo('<br><br><br><br><br><br>');
		}
}

?>