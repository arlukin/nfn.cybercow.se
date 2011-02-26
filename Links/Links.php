<?php
include('../Include/nfnConstant.php');
include('../Include/nfnGlobal.php');
include('Db.php');
include('Validate.php');

if (!empty($Submit_x))
{
	if (isValid())
	{
		insLink($formName, $formEmail, $formWebpageName, $formUrl, $formComment);
		unset($formName);
		unset($formEmail);
		unset($formWebpageName);
		unset($formUrl);
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

	<table align="center"  border="2" cellspacing="4" cellpadding="8" bgcolor="#BFB080" width="750"style="border-style:solid; border-color:#000000; padding:4; border-collapse: collapse">
	<tr>
		<td colspan="2"><h1>Links</h1></td>
	</tr>
	<tr>
		<td valign="top" width="50%">
			<?php
				$resultTree = getLinks();



				$numOfLinks = count($resultTree);

				foreach ($resultTree as $row)
				{
					echo('<table cellspacing="0" cellpadding"0" border="0" style="margin-bottom: 6">');
						echo('<tr><td>&nbsp;</td>');
						echo('<td width="300">');
							echo('<a href="'.$row['url'].'" target="_new">'.$row['link_name'].'</a>');

							if (!empty($row['comment']))
								echo('<br>&nbsp;&nbsp;'.$row['comment']);
						echo('</td>');
					echo('</table>');


					if 	($row['link_id'] == ($numOfLinks/2))
					{
						echo('</td>');
						echo('<td valign="top" width="50%">');
					}
				}
			?>
		</td>
	</tr>

	<?php

		echo('<tr>');
			echo('<td colspan="2" class="cssErrorMsg">');
				echo($errMsg);
			echo('</td>');
		echo('</tr>');

		echo('<tr>');
			echo('<td colspan="2">');
			?>
				<form method="post" action="<?= $PHP_SELF ?>">
					<input type="hidden" value="<?=$formMovieId ?>"	name="formMovieId">

					<table cellspacing="0" cellpadding="0" border="0">
						<tr>
							<td class="cssTableCaption">Name:</td>
							<td>&nbsp;&nbsp;</td>
							<td class="cssTableCaption">Email:</td>
						</tr>

						<tr>
							<td><input maxlength="50" size="35" name="formName" value="<?=$formName?>"></td>
							<td>&nbsp;&nbsp;</td>
							<td><input maxlength="50" size="35" name="formEmail" value="<?=$formEmail?>"><br></td>
						</tr>

						<tr>
							<td class="cssTableCaption">Webpage name:</td>
							<td>&nbsp;&nbsp;</td>
							<td class="cssTableCaption">Url:</td>
						</tr>

						<tr>
							<td><input maxlength="50" size="35" name="formWebpageName" value="<?=$formWebpageName?>"></td>
							<td>&nbsp;&nbsp;</td>
							<td><input maxlength="50" size="35" name="formUrl" value="<?=$formUrl?>"><br></td>
						</tr>

						<tr>
							<td colspan ="3">
								<span class="cssTableCaption">Comment:</span> <br>
								<textarea cols="54" rows="4" wrap="soft" name="formComment"><?=$formComment?></textarea><br>
							</td>
						</tr>
					</table>
					<input type="image" src="<?=IMAGEURL ?>/ok.gif" name="Submit">
				</form>

			<?php
			echo('</td>');

		echo('</tr>');
	?>
	</table>

</body>
</html>