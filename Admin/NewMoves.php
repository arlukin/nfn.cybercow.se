<?php
include('../Include/nfnConstant.php');
include('../Include/nfnGlobal.php');

function addMovie($move_id, $url, $user_id)
{
	$size = 10000;
	$downloads = 1;
	executeOnDb('
		insert into movie (
			url,
			size,
			user_id,
			downloads
		) values ('
		.escS($url).','
		.escN($size).','
		.escN($user_id).','
		.escN($downloads).'
		)'
	);

	$movie_id = executeOnDbReturnOneColumn('
		select last_insert_id()
	');

	executeOnDb('
		insert into movemovies (
			movie_id,
			move_id
		) values ('
		.escS($movie_id).','
		.escN($move_id).'
		)'
	);

}

function addMove($move_id, $type_id, $difficulty_id, $user_id, $movie_name, $url, $description)
{
	echo('<br>'.$movie_name);

	$visits = 1;
	executeOnDb('
		insert into move (
			move_id,
			type_id,
			difficulty_id,
			move_name,
			visits,
			description
		) values ('
		.escN($move_id).','
		.escN($type_id).','
		.escN($difficulty_id).','
		.escS($movie_name).','
		.escN($visits).','
		.escS($description).'
		)'
	);

	addMovie($move_id, $url, $user_id);
}

// Move
executeOnDb('delete from move');
executeOnDb('delete from movie');
executeOnDb('delete from movemovies');

// PASSES
addMove(101, 	T_PASSES, D_BEGINNER, U_MASTERPO, "Behind Back At Waist", 						"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(102, 	T_PASSES, D_BEGINNER, U_MASTERPO, "Between Leg Pass", 								"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(103, 	T_PASSES, D_BEGINNER, U_MASTERPO, "Diagonal Over Opposite Shoulder", 	"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(104, 	T_PASSES, D_BEGINNER, U_MASTERPO, "Diagonal Over Shoulder", 					"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(105, 	T_PASSES, D_BEGINNER, U_MASTERPO, "Handtohand Over Backhand", 				"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(106, 	T_PASSES, D_BEGINNER, U_MASTERPO, "Handtohand Under Backhand", 				"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(107, 	T_PASSES, D_BEGINNER, U_MASTERPO, "LegBounce Pass", 									"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(108, 	T_PASSES, D_BEGINNER, U_MASTERPO, "Overarm Neck Pass", 								"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(109, 	T_PASSES, D_BEGINNER, U_MASTERPO, "Over Shoulder Pass", 							"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(110, T_PASSES, D_BEGINNER, U_MASTERPO, "Under Arm Neck Pass", 							"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(111, T_PASSES, D_BEGINNER, U_MASTERPO, "Under Opposite Arm Pass", 					"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(112, T_PASSES, D_BEGINNER, U_MASTERPO, "Under Shoulder Pass", 							"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");

// SPINS
addMove(201, 	T_SPINS, D_BEGINNER, U_MASTERPO, "Figure 8.",													"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(202, 	T_SPINS, D_BEGINNER, U_MASTERPO, "Forward Spin.",											"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(203, 	T_SPINS, D_BEGINNER, U_MASTERPO, "Inverted Figure 8.",								"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(204, 	T_SPINS, D_BEGINNER, U_MASTERPO, "Overhead Spin.",										"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(205, 	T_SPINS, D_BEGINNER, U_MASTERPO, "Reverse Grip Figure 8.",						"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(206, 	T_SPINS, D_BEGINNER, U_MASTERPO, "Upwards Spin.",											"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");


// T_BOUCES
addMove(301, 	T_BOUNCES, D_BEGINNER, U_MASTERPO, "Between Leg Bounce.",							"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(302, 	T_BOUNCES, D_BEGINNER, U_MASTERPO, "Hip Bounce.",											"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(303, 	T_BOUNCES, D_BEGINNER, U_MASTERPO, "Over Shoulder Bounces.",  				"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(304, 	T_BOUNCES, D_BEGINNER, U_MASTERPO, "Topand Back Of Leg Bounce.",			"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(305, 	T_BOUNCES, D_BEGINNER, U_MASTERPO, "Torso Bounce.",										"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(306, 	T_BOUNCES, D_BEGINNER, U_MASTERPO, "Under Arm Over Arm Bounce.",			"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");

// T_STOPS

// T_STRIKES
addMove(501, 	T_STRIKES, D_BEGINNER, U_MASTERPO, "Backhand Strike.",								"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(502, 	T_STRIKES, D_BEGINNER, U_MASTERPO, "Backhand Waist Spin Strike.",			"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(503, 	T_STRIKES, D_BEGINNER, U_MASTERPO, "Backhand Waist Strike.",					"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(504,  T_STRIKES, D_BEGINNER, U_MASTERPO, "Downwards Strike.",								"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(505,  T_STRIKES, D_BEGINNER, U_MASTERPO, "Forehand strike.",								"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(506,  T_STRIKES, D_BEGINNER, U_MASTERPO, "Forehand Waist Spin Strike.",			"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(507,  T_STRIKES, D_BEGINNER, U_MASTERPO, "Forehand Waist Strike.",					"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(508,  T_STRIKES, D_BEGINNER, U_MASTERPO, "Front Strike.",										"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(509,  T_STRIKES, D_BEGINNER, U_MASTERPO, "Reverse Grip Upwards Strike.",		"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(510, T_STRIKES, D_BEGINNER, U_MASTERPO, "Upwards Strike.",									"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");


// T_BLOCKS

// T_TWIRLS
addMove(701, 	T_TWIRLS, D_BEGINNER, U_MASTERPO, "Double Spin Wristflip",						"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(702, 	T_TWIRLS, D_BEGINNER, U_MASTERPO, "Figure 8 Wristflip",								"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(703, 	T_TWIRLS, D_BEGINNER, U_MASTERPO, "Lower Waist Spin",									"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(704,  T_TWIRLS, D_BEGINNER, U_MASTERPO, "Over Backhand Wristflip",					"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(705,  T_TWIRLS, D_BEGINNER, U_MASTERPO, "Over Reverse backhand Wristflip",	"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(706,  T_TWIRLS, D_BEGINNER, U_MASTERPO, "Reverse Wrist flip",								"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(707,  T_TWIRLS, D_BEGINNER, U_MASTERPO, "Single Spin Two Handed Wrist Flip","Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(708,  T_TWIRLS, D_BEGINNER, U_MASTERPO, "Single Spin Wrist Flip",						"Passes/BehindBackAtWaist.avi", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");

// T_DOUBLES
addMove(801, T_DOUBLES, D_BEGINNER, U_MASTERPO, "American Style Double Nunchaku",	"Passes/American Style Double Nunchaku", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");

// T_MISC

// T_COMBOS
addMove(1001, T_COMBOS, D_BEGINNER, U_MASTERPO, "Pass Combo",								"Passes/NFN Passing The Baton", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(1002, T_COMBOS, D_BEGINNER, U_MASTERPO, "Spin Combo",								"Passes/Spin Combo", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(1003, T_COMBOS, D_BEGINNER, U_MASTERPO, "Bounce Combo",							"Passes/NFN Passing The Baton", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(1004, T_COMBOS, D_BEGINNER, U_MASTERPO, "Strike Combo",							"Passes/NFN Passing The Baton", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(1005, T_COMBOS, D_BEGINNER, U_MASTERPO, "Twirl Combo",							"Passes/NFN Passing The Baton", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(1006, T_COMBOS, D_MASTER, U_MASTERPO, "Advanced Twirl Combo",			"Passes/NFN Passing The Baton", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");

// T_FORMS
addMove(1101, T_FORMS, D_BEGINNER, U_MASTERPO, "FN Passing The Baton",			"Passes/NFN Passing The Baton", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(1102, T_FORMS, D_BEGINNER, U_MASTERPO, "FN Crawling For More",			"Passes/NFN Crawling For More", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(1103, T_FORMS, D_BEGINNER, U_MASTERPO, "FN No Return", 							"Passes/NFN No Return", 				"Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");

// T_FREEFORMS
addMove(1201, T_FREEFORMS, D_BEGINNER, U_MASTERPO, "Master Po Freestyle 1",	"Passes/Master Po Freestyle 1", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(1202, T_FREEFORMS, D_BEGINNER, U_MASTERPO, "Master Po Freestyle 2",	"Passes/Master Po Freestyle 2", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(1203, T_FREEFORMS, D_BEGINNER, U_MASTERPO, "Master Po Freestyle 3",	"Passes/Master Po Freestyle 3", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(1204, T_FREEFORMS, D_BEGINNER, U_MASTERPO, "Master Po Freestyle 4",	"Passes/Master Po Freestyle 4", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(1205, T_FREEFORMS, D_BEGINNER, U_MASTERPO, "Gigante Freestyle 1",		"Passes/Gigante Freestyle 1", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(1206, T_FREEFORMS, D_BEGINNER, U_MASTERPO, "Gigante Freestyle 2",		"Passes/Gigante Freestyle 2", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(1208, T_FREEFORMS, D_BEGINNER, U_MASTERPO, "Gigante Freestyle 3",		"Passes/Gigante Freestyle 3", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");
addMove(1209, T_FREEFORMS, D_BEGINNER, U_MASTERPO, "Gigante Freestyle 4",		"Passes/Gigante Freestyle 4", "Setting up with the thumb on the top, of the back of the handle is impossible,  due to the lack of motion. Your thumb is actually pinching the handle against the 3rd knuckle of the index finger. Pulling the hand back toward you at the point of release will help to keep the spin true using speed.");

?>