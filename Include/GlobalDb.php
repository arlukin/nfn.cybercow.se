<?php

function &getCompanyTypes()
{
  return executeOnDb('
    select
      companytype_id,
      companytype_name
    from
			companytype
		order by
      companytype_id
   ');
}

function &getBranches()
{
  return executeOnDb('
    select
      branch_id,
      branch_name
    from
			branch
		order by
      branch_id
   ');
}

// GetActiveairfareContractsMODE
define('GACMODE_ALL', 0);
define('GACMODE_CONTRACTS', 1);
define('GACMODE_AGREEMENTS', 2);

// @todo: alternativt namn getActiveContracts();
function &getActiveAirfareContracts($mode = GACMODE_ALL)
{
  global
        $cookieBrandedCompanyId;

  $where = 'contract.contractstatus_id = '.CS_ACTIVE.'
        and contract.contractaccess = \'Y\'
  			and	contract.buyer_id ='.escN(getBuyerIdFromSession());

	if (getSellerIdFromSession())
	{
		if ($mode == GACMODE_AGREEMENTS)
			$where .= ' and	contract.seller_id != '.escN(getSellerIdFromSession());
		elseif ($mode == GACMODE_CONTRACTS)
			$where .= ' and	contract.seller_id = '.escN(getSellerIdFromSession());
	}

  if (empty($cookieBrandedCompanyId))
  {
  	$where .= ' and contract.brandedaccess = '.escS(escV('N'));

  	return executeOnDb('
  		select
  			node.node_id,
  			concat(company.shortname, " (", node.node_name, ")") as name
  		from
  			contract
  			inner join node using (node_id)
  			inner join seller using (seller_id)
  			inner join company using (company_id)
  		where
  			'.$where.'
  		order by
  			company.shortname,
  			node.node_name
  	');
  }
  else
  {
  	$where .= ' and contract.brandedaccess = '.escS(escV('Y')).
  						' and company.company_id ='.escN($cookieBrandedCompanyId);

    return executeOnDb('
      select
        node.node_id,
        concat(company.shortname, " (", node.node_name, ")") as name
      from
        contract
        inner join node using (node_id)
        inner join seller using (seller_id)
        inner join company using (company_id)
      where
      	'.$where.'
      order by
        company.shortname,
        node.node_name
    ');
  }
}

// return country_id, country_name
function &getAllCountries($translate_ = false)
{	
	if($translate_)
	{
		return executeOnDb('
			SELECT
				country.country_id,
				getLanguage(location.location_name, '.escS(getCompanyLanguageFromSession()).', \'UK_\', 1) as country_name
			FROM
				country
			  inner join location	on (location.location_code = country.country_code)
		  WHERE 
			  	location_type_set & '.LT_COUNTRY.'
			ORDER BY
				country_name
		');
	}
	else
	{
	
		return executeOnDb('
			select
				country_id,
				country_name
			from
				country
			order by
				country_name
		');
	}
}

function getCountryNameFromId($id_)
{
	static
		$countries = array();

	if(!isset($countries[$id_]))
	{
		$countries[$id_] = executeOnDbReturnOneColumn('
			SELECT
				country_name
			FROM
				country
			WHERE
				country_id = '.$id_
		);
	}
	return $countries[$id_];
}

function getCountryIdFromCode($countryCode_)
{
	static
		$countries = array();

	if(!isset($countries[$countryCode_]))
	{
		$countries[$countryCode_] = executeOnDbReturnOneColumn
		('
			SELECT
				country_id
			FROM
				country
			WHERE
				country_code = '.escS($countryCode_)
		);
	}
	return $countries[$countryCode_];
}

function getAllLanguages()
{
	return executeOnDb('
		select
			language_code,
			language_name
		from
			language
	');
}

function &getCurrency()
{
	return executeOnDb('
		select
			currency_code,
			concat(currency_code, " (", currency_name, ")")
		from
			currency
		order by
			currency_code
	');
}

function &getFullName($userId)
{
	return executeOnDbReturnOneColumn('
		select
			concat(user.firstname," ", user.lastname) as fullname
		from
			user
		where
			user.user_id = '.escN($userId)
		);
}

/**
*
* @param	string	$companyNo_
*
* @return	-
*
* @access	public
*/
function getCompanyIdFromCompanyNo($companyNo_)
{
	static
		$companyNoArr;

	if (!isset($companyNoArr[$companyNo_]))
	{
		$companyNoArr[$companyNo_] = executeOnDbReturnOneColumn
		('
			SELECT
				company_id
			FROM
				company
			WHERE
				company_no = '.escS($companyNo_)
		);
	}

	return $companyNoArr[$companyNo_];
}

function &getCompanyName($companyId)
{
	return executeOnDbReturnOneColumn('
		select
			company_name
		from
			company
		where
			company_id = '.escN($companyId)
		);
}


function &getCompanyCountry($companyId)
{
	return executeOnDbReturnOneColumn('
		select
			country_id
		from
			company
		where
			company_id = '.escN($companyId)
		);
}

function &getCompanyReference($companyId)
{
	return executeOnDbReturnOneColumn('
		select
			reference
		from
			company
		where
			company_id = '.escN($companyId)
		);
}

function &getCompanyRow($companyId)
{
	static
		$companyRow;

	if (empty($companyRow[$companyId]))
	{
		$companyRow[$companyId] = executeOnDbReturnOneRow
		('
			select
				company.*,
				country.country_name,
				country.country_code
			from
				company
				inner join country country using (country_id)
			where
				company_id = '.escN($companyId)
		);
	}

	return $companyRow[$companyId];
}

function &getUserRow($userId)
{
	static
		$userRow;

	if (empty($userRow[$userId]))
	{
		$userRow[$userId] = executeOnDbReturnOneRow
		('
			select
				user.*
			from
				user
			where
				user_id = '.escN($userId)
		);
	}

	return $userRow[$userId];
}

function &getUserRowExtended($userId)
{
	static
		$userRowExtended;

	if (empty($userRowExtended[$userId]))
	{
		$userRowExtended[$userId] = executeOnDbReturnOneRow
		('
			SELECT
				user.firstname,
				user.lastname,
				user.email,
				userdesc.phone,
				company.company_name,
				company.address as company_address,
				company.email as company_email,
				company.phone as company_phone,
				company.fax as company_fax,
				userdesc.mobile
			FROM
				user
				inner join company USING (company_id)
				left join userdesc ON (user.user_id = userdesc.user_id)
			WHERE
				user.user_id = '.escN($userId)
		);
	}

	return $userRowExtended[$userId];
}


function setPortalCompanyId($companyId)
{
	global
		$gStaticCompanyIdFromPortal;

	clearPortalCompanyId();
	$gStaticCompanyIdFromPortal = $companyId;
	foSession::setCompanyId($companyId);
}


function clearPortalCompanyId()
{
	global
	  $gStaticBuyerIdFromPortal,
	  $gStaticSellerIdFromPortal,
		$gStaticCompanyIdFromPortal;

	unset($gStaticBuyerIdFromPortal);
	unset($gStaticSellerIdFromPortal);
	unset($gStaticCompanyIdFromPortal);
	
	foSession::setCompanyId(getCompanyIdFromSession());
}


function getBuyerIdFromPortal()
{
	global
		$gStaticBuyerIdFromPortal,
		$gStaticCompanyIdFromPortal;

	ASSERTLOG
	(
		empty($gStaticCompanyIdFromPortal),
		LOG_SYSTEMERROR,
		'getBuyerIdFromPortal(): PortalCompanyId not set!',
		EL_LEVEL_3,
		ECAT_SYSTEM_CORE
	);

	if (!isset($gStaticBuyerIdFromPortal))
		$gStaticBuyerIdFromPortal = executeOnDbReturnOneColumn('
			select
				buyer.buyer_id
			from
				buyer
			where
				company_id = '.escN($gStaticCompanyIdFromPortal)
			);

	return $gStaticBuyerIdFromPortal;
}

function getSellerIdFromPortal()
{
	global
		$gStaticSellerIdFromPortal,
		$gStaticCompanyIdFromPortal;

	ASSERTLOG
	(
		empty($gStaticCompanyIdFromPortal),
		LOG_SYSTEMERROR,
		'getSellerIdFromPortal(): PortalCompanyId not set!',
		EL_LEVEL_3,
		ECAT_SYSTEM_CORE
	);

	if (!isset($gStaticSellerIdFromPortal))
		$gStaticSellerIdFromPortal = executeOnDbReturnOneColumn('
			select
				seller.seller_id
			from
				seller
			where
				company_id = '.escN($gStaticCompanyIdFromPortal)
			);

	return $gStaticSellerIdFromPortal;
}

function getCompanyIdFromPortal()
{
	global
		$gStaticCompanyIdFromPortal;

	return $gStaticCompanyIdFromPortal;
}



// Session Id.
function getCompanyIdFromSession()
{
	global
		$gStaticCompanyIdFromPortal,
		$gStaticCompanyIdFromSession;

	if (isset($gStaticCompanyIdFromPortal))
		return getCompanyIdFromPortal();

	if (!isset($gStaticCompanyIdFromSession))
		$gStaticCompanyIdFromSession = foSession::getCompanyId();

	return $gStaticCompanyIdFromSession;
}


function getSellerIdFromSession()
{
	// Global istället för static, så att man kan sätta den utifrån.
	global
		$gStaticCompanyIdFromPortal,
		$gStaticSellerIdFromSession;

	if (isset($gStaticCompanyIdFromPortal))
		return getSellerIdFromPortal();

	if (!isset($gStaticSellerIdFromSession))
		$gStaticSellerIdFromSession = executeOnDbReturnOneColumn('
			select
				seller.seller_id
			from
				seller
				inner join user on seller.company_id = user.company_id
				inner join session on user.user_id = session.user_id
			where
				session.session_id = '.escS(session_id())
			);

	return $gStaticSellerIdFromSession;
}

//

function getSellerIdFromCompany($companyId)
{
	$buyerId = executeOnDbReturnOneColumn('
		select
			seller_id
		from
			seller
		where
			company_id = '.escN($companyId)
	);

	return $buyerId;
}

//

function getBuyerIdFromCompany($companyId)
{
	$buyerId = executeOnDbReturnOneColumn('
		select
			buyer_id
		from
			buyer
		where
			company_id = '.escN($companyId)
	);

	return $buyerId;
}

//

function getCompanyIdFromBuyerId($buyerId_)
{
	$companyId = executeOnDbReturnOneColumn('
		select
			company_id
		from
			buyer
		where
			buyer_id = '.escN($buyerId_)
	);

	return $companyId;
}

//

function getBuyerIdFromSession()
{
	global
		$gStaticCompanyIdFromPortal,
		$gStaticBuyerIdFromSession;

	if (isset($gStaticCompanyIdFromPortal))
		return getBuyerIdFromPortal();

	if (!isset($gStaticBuyerIdFromSession))
		$gStaticBuyerIdFromSession = executeOnDbReturnOneColumn('
				select
					buyer.buyer_id
				from
					buyer
					inner join user on buyer.company_id = user.company_id
					inner join session on user.user_id = session.user_id
				where
					session.session_id = '.escS(session_id())
			);

	return $gStaticBuyerIdFromSession;
}

function &getUserLanguageFromSession()
{
	global
		$gStaticUserLanguageFromSession;

	if (!isset($gStaticUserLanguageFromSession))
	{
		$gStaticUserLanguageFromSession =  executeOnDbReturnOneColumn('
			select
				language_code
			from
				user
				inner join session using (user_id)
			where
				session_id = '.escS(session_id())
		);

		ASSERTLOG(!isset($gStaticUserLanguageFromSession), LOG_SYSTEMERROR, 'getUserLanguageFromSession: User must have a language', EL_LEVEL_3, ECAT_SYSTEM_CORE);
	}
	return $gStaticUserLanguageFromSession;
}

function setPortalCompanyLanguage($language_)
{
	global
		$gStaticCompanyLanguageFromPortal;

	$gStaticCompanyLanguageFromPortal = $language_;
}

function &getCompanyLanguageFromSession()
{
	global
		$gStaticCompanyLanguageFromPortal;

	if (isset($gStaticCompanyLanguageFromPortal))
	{
		return getPortalCompanyLanguage();
	}

	return executeOnDbReturnOneColumn('
		select
			language_code
		from
			company
			inner join country using (country_id)
		where
			company_id = '.escN(getCompanyIdFromSession())
	);
}

//

function getPortalCompanyLanguage()
{
	global
		$gStaticCompanyLanguageFromPortal;

	return $gStaticCompanyLanguageFromPortal;
}

//

function &getUserIdFromSession()
{
	return foSession::getUserId();
}

function &getCompanyIdFromUser($userId)
{
	return executeOnDbReturnOneColumn('
		select
			company_id
		from
			user
		where
			user_id = '.escN($userId)
	);
}

function getCompanyCurrencyCode()
{
	return executeOnDbReturnOneColumn('
		select
			currency_code
		from
			buyer
		where
			buyer_id = '.escN(getBuyerIdFromSession())
	);
}

function getCompanyCounter($counter)
{
		$companyId = getCompanyIdFromSession();
		executeOnDb('LOCK TABLES company_counters WRITE');
		$counterRow = executeOnDbReturnOneRow('select company_id, '.$counter.' from company_counters where company_id = '.escN($companyId));
		if (emptyString($counterRow[company_id]))
		{
			$counterRow[$counter]=1;
			executeOnDb('insert into company_counters (company_id, '.$counter.') values ('.escN($companyId).', '.escN($counterRow[$counter]).')');
		}
		else
		{
			$counterRow[$counter]++;
			executeOnDb('update company_counters set '.$counter.' = '.escN($counterRow[$counter]).' where company_id = '.escN($companyId));
		}

		executeOnDb('UNLOCK TABLES');
		return $counterRow[$counter];
}


?>