<?php

	function &getMoves()
	{
		global
			$formMovieId;
			
		$result	= array();

		$result = executeOnDbReturnMerge($result,
		'select
				move.move_id,
				move.move_name,
				move.type_id,				
				move.visits,
				move.description,
				difficulty.difficulty_name,
				type.type_name
			from
				move
				inner join difficulty using (difficulty_id)
				inner join type on (move.type_id = type.type_id) 				
			where
				move_id = '.escN($formMovieId),			
			'move_id',
			array(
				'movies' => 'move_id',
				'comments' => 'move_id'
			)
		);

		$result =	executeOnDbReturnMerge($result,
			'select
				movemovies.move_id,
				movie.movie_id,
				movie.size,
				movie.url,
				movie.downloads,
				user.user_name
			from
				movemovies
				inner join movie using (movie_id)
				inner join user using (user_id)
			where
				movemovies.move_id in (#movies)				
			order by
				movie.movie_id',
			null,
			null,
			array(
				'movies' => 'move_id'
			)
		);

		$result =	executeOnDbReturnMerge($result,
			'select
				comment.move_id,
				comment.name,
				comment.email,
				comment.comment,
				comment.created
			from
				comment
			where
				comment.move_id in (#comments)				
			order by
				comment.created',
			null,
			null,
			array(
				'comments' => 'move_id'
			)
		);
		
		return $result;
	}
	
function insComment($moveId, $name, $email, $comment)
{
	executeOnDb('
		insert into comment(
			move_id,
			name,
			email,
			comment,
			created
		) values ('
			.escN($moveId).','
			.escS($name).','
			.escS($email).','
			.escS($comment).',
			now()
			
		)'
	);
}
?>