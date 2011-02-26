<?php

	function &getLinks()
	{
		$result	= array();

		$result = executeOnDbReturnMerge($result,
		'select
			link_id,
			link_name,
			url,
			name,
			email,
			comment
		from
			link
		where
			verified = "Y"
		order by
			name
		', 
			'link_id',
			array(
				'prices'				=> 'link_id'
			)
		);

		return $result;
	}
	

function insLink($name, $email, $webpageName, $url, $comment)
{
	executeOnDb('
		insert into link (
			name,
			email,
			link_name,
			url,
			comment
		) values ('
		.escS($name).','
		.escS($email).','
		.escS($webpageName).','
		.escS($url).','
		.escS($comment).'
		)'
	);
}
?>

