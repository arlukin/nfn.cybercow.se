<?php

	function &getMoves()
	{
		$result	= array();

		$result = executeOnDbReturnMerge($result,
		'select
			type.type_id,
			type.type_name
		from
			type
		',			
			'type_id',
			array(
				'types'				=> 'type_id'
			)
		);

		$result =	executeOnDbReturnMerge($result,
			'select
				move.move_id,
				move.move_name,
				move.type_id,				
				difficulty.difficulty_name,
				movie.url
			from
				move
				inner join difficulty using (difficulty_id)
				inner join movemovies on (move.move_id = movemovies.move_id)
				inner join movie using (movie_id)
			where
				move.type_id in (#types)				
			order by
				move_id',
			null,
			null,
			array(
				'types' => 'type_id'
			)
		);

		return $result;
	}
	
	
?>

