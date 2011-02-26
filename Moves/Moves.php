<?php
include('../Include/nfnConstant.php');
include('../Include/nfnGlobal.php');
include('db.php');

?>


<html>
<head>
<TITLE>.::: NgauFuNunchaku :::.</TITLE>
<meta name="description" content="NgauFuNunchuku.com offers videoclips, pictures and more...">
<meta name="keywords" content="ngaufu,ngau fu,nunchaku,moves,chakus,flash,trix,extreme,balisong,twirling,spins,bounces,passes,stockholm,sweden,events,videoclips,pictures,film,how,to,learn">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php writeCSS() ?>
</head>

<body style="margin-top: 4; margin-left: 30;" background="../Images/ngaufu_bg.gif">

	<table align="center"  border="2" cellspacing="4" cellpadding="8" bgcolor="#BFB080" width="750"style="border-style:solid; border-color:#000000; padding:4; border-collapse: collapse">
	<tr>
		<td colspan="2"><h1>Moves</h1>
			<h2>New moves</h2>
			If you have any moves that you don't see in our move database,
			please send us an <a href="NFN@cybercow.se">email</a>. If the
			movie is larger then 5 megabytes please first ask me if it's okay
			to send the movie.
			<br>
		</td>
	</tr>
	<tr>
		<td valign="top" width="50%">
			<?php
				$resultTree = getMoves();

				foreach ($resultTree as $row)
				{
					if (count($row['types']) > 0)
					{
						echo('<h2>'.$row['type_name'].'</h2>');
							echo('<table cellspacing="0" cellpadding"0" border="0">');
							foreach($row['types'] as $typeRow)
							{
								echo('<tr><td>&nbsp;</td>');
								echo('<td width="300"><a href="MoveDetail/MoveDetail.php?formMovieId='.$typeRow['move_id'].'">'.$typeRow['move_name'].'</a></td>');
								echo('<td >'.$typeRow['difficulty_name'].'&nbsp;&nbsp;</td>');
								echo('<td ><a href="'.$typeRow['url'].'">Save</a></td></tr>');
							}
							echo('</table>');
					}


					if 	($row['type_id'] == 6)
					{
						echo('</td>');
						echo('<td valign="top" width="50%">');
					}
				}
			?>
		</td>
	</tr>
	</table>

</body>
</html>