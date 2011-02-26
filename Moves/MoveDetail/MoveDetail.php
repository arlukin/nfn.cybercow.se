<?php
include('../Include/nfnConstant.php');
include('../Include/nfnGlobal.php');

include('Validate.php');
include('Db.php');

// ACTION

if (!empty($Submit_x))
{
	if (isValid())
	{
		insComment($formMovieId, $formName, $formEmail, $formComment);
		unset($formName);
		unset($formEmail);
		unset($formComment);
	}
}

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

	<table align="center"  border="2" cellspacing="4" cellpadding="8" bgcolor="#BFB080" width="750" style="border-style:solid; border-color:#000000; padding:4; border-collapse: collapse">
			<?php
				$resultTree = getMoves();
				foreach ($resultTree as $row)
				{
					echo('<tr>');
						echo('<td colspan="2"><h1>'.$row['move_name'].'</h1></td>');
					echo('</tr>');
					echo('<tr>');
						echo('<td valign="top" width="50%">');

							echo('<h2>Details</h2>');
							echo('<table cellspacing="0" cellpadding"0" border="0">');
									echo('<tr>');
										echo('<td>&nbsp;</td>');
										echo('<td class="cssTableCaption">Type:</td>');
										echo('<td>'.$row['type_name'].'</td>');
									echo('</tr>');

									echo('<tr>');
										echo('<td>&nbsp;</td>');
										echo('<td class="cssTableCaption">Difficulty: &nbsp;</td>');
										echo('<td>'.$row['difficulty_name'].'</td>');
									echo('</tr>');

									echo('<tr>');
										echo('<td>&nbsp;</td>');
										echo('<td class="cssTableCaption">Visits:</td>');
										echo('<td>'.$row['visits'].'</td>');
									echo('</tr>');

							echo('</table>');

						echo('</td>');

						echo('<td valign="top" width="50%">');

							echo('<h2>Movies</h2>');
								echo('<table cellspacing="0" cellpadding"0" border="0">');
									echo('<tr><td>&nbsp;</td>');
									echo('<td width="400" class="cssTableCaption">Master</td>');
									echo('<td class="cssTableCaption">Size&nbsp;</td>');
									echo('<td class="cssTableCaption">Downloads&nbsp;</td>');
									echo('<td class="cssTableCaption">Mirror&nbsp;</td>');

								foreach($row['movies'] as $movieRow)
								{
									echo('<tr><td>&nbsp;</td>');
									echo('<td>'.$movieRow['user_name'].'</td>');
									echo('<td nowrap>'.$movieRow['size'].' kb&nbsp;&nbsp;</td>');
									echo('<td>'.$movieRow['downloads'].'&nbsp;&nbsp;</td>');
									echo('<td><a href="/ngaufu/'.$movieRow['url'].'">1</a>&nbsp;&nbsp;<a href="/gigante/'.$movieRow['url'].'">2</a></td>');
								}
								echo('</table>');

						echo('</td>');
					echo('</tr>');

					echo('<tr>');
						echo('<td colspan="2">');
							echo('<h2>Description</h2>');
								echo($row['description'].'&nbsp;');


						echo('</td>');
					echo('</tr>');

					echo('<tr>');
						echo('<td colspan="2">');

							echo('<h2>Comments</h2>');
								echo('<table cellspacing="0" cellpadding"0" border="0">');

									foreach($row['comments'] as $commentRow)
									{
										echo('<tr>');
											echo('<td>&nbsp;</td>');
											echo('<td>');
												echo('<a href="'.$commentRow['email'].'">'.$commentRow['name'].'<a>');
												echo(' ('.date('Y-m-d', strtotime($commentRow['created'])).')<br>');
												echo($commentRow['comment'].'<br>');
											echo('</td>');
										echo('</tr>');

									echo('<tr>');
										echo('<td colspan="3"><hr></td>');
									echo('</tr>');

									}
									echo('<tr>');
										echo('<td colspan="3">&nbsp;</td>');
									echo('</tr>');

									echo('<tr>');
										echo('<td>&nbsp;</td>');
										echo('<td colspan="2" class="cssErrorMsg">');
											echo($errMsg);
										echo('</td>');
									echo('</tr>');


									echo('<tr>');
										echo('<td>&nbsp;</td>');
										echo('<td colspan="2">');
										?>
											<form method="post" action="<?= $PHP_SELF ?>">
												<input type="hidden" value="<?=$formMovieId ?>"	name="formMovieId">

												<b>Name:</b> <br>
												<input maxlength="50" size="35" name="formName" value="<?=$formName?>"><br>
												<b>Email:</b> <br>
												<input maxlength="50" size="35" name="formEmail" value="<?=$formEmail?>"><br>
												<b>Comment:</b> <br>
												<textarea cols="54" rows="4" wrap="soft" name="formComment"><?=$formComment?></textarea><br>
												<input type="image" src="<?=IMAGEURL ?>/ok.gif" name="Submit">
											</form>

										<?php
										echo('</td>');

									echo('</tr>');

								echo('</table>');
						echo('</td>');
					echo('</tr>');

				}

			?>
	</table>

</body>
</html>