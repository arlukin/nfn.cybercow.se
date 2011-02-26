<?php
include('../Include/nfnConstant.php');
include('../Include/nfnGlobal.php');


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
		<td colspan="2"><h1>Shop</h1></td>
	</tr>
	<tr>
		<td valign="top" width="50%">
			<?php
						echo('<table cellspacing="0" cellpadding"0" border="0" style="margin-bottom: 6">');

							echo('<tr>');
								echo('<td>&nbsp;</td>');
								echo('<td width="300">');
									echo('<a href="http://www.AllBlackBelt.com">AllBlackBelt.com</a>');
									echo('<br>&nbsp;&nbsp;Good site, good selection');
								echo('</td>');
							echo('</tr>');

							echo('<tr>');
								echo('<td>&nbsp;</td>');
								echo('<td width="300">');
									echo('<a href="http://www.sakura-0.com/">Sakura Martial Arts Supply</a>');
									echo('<br>&nbsp;&nbsp;some unusual nunchakus');
								echo('</td>');
							echo('</tr>');

							echo('<tr>');
								echo('<td>&nbsp;</td>');
								echo('<td width="300">');
									echo('<a href="http://www.Gungfu.com">Gungfu.com</a>');
									echo('<br>&nbsp;&nbsp;some different nunchakus');
								echo('</td>');
							echo('</tr>');

							echo('<tr>');
								echo('<td>&nbsp;</td>');
								echo('<td width="300">');
								echo('<a href="http://www.tansmartialartssupplier.com.au/">www.tansmartialartssupplier.com.au</a>');
								echo('</td>');
							echo('</tr>');

							echo('<tr>');
								echo('<td>&nbsp;</td>');
								echo('<td width="300">');
								echo('<a href="http://www.allblackbelt.com/wnunchakus.html">www.allblackbelt.com</a>');
								echo('</td>');
							echo('</tr>');

							echo('<tr>');
								echo('<td>&nbsp;</td>');
								echo('<td width="300">');
								echo('<a href="http://www.martialartsupply.com/Weapons/Nunchaku/nunchaku.html">www.martialartsupply.com</a>');
								echo('</td>');
							echo('</tr>');

							echo('<tr>');
								echo('<td>&nbsp;</td>');
								echo('<td width="300">');
								echo('<a href="http://www.amas.net/weapon.cfm ">www.amas.net</a>');
								echo('</td>');
							echo('</tr>');

							echo('<tr>');
								echo('<td>&nbsp;</td>');
								echo('<td width="300">');
								echo('<a href="http://www.martialartsgear.com/weapons/chucks/chucks.htm">www.martialartsgear.com</a>');
								echo('</td>');
							echo('</tr>');

							echo('<tr>');
								echo('<td>&nbsp;</td>');
								echo('<td width="300">');
								echo('<a href="http://www.thesteelsource.com/html/maequip.htm">www.thesteelsource.com</a>');
								echo('</td>');
							echo('</tr>');

						echo('</table>');

					echo('</td>');
					echo('<td valign="top" width="50%">');
						echo('<table cellspacing="0" cellpadding"0" border="0" style="margin-bottom: 6">');
							echo('<tr>');
								echo('<td>&nbsp;</td>');
								echo('<td width="300">');
								echo('<a href="http://store.yahoo.com/tigerstrike-martial-arts/nunchuka.html">store.yahoo.com/tigerstrike-martial-arts</a>');
								echo('</td>');
							echo('</tr>');

							echo('<tr>');
								echo('<td>&nbsp;</td>');
								echo('<td width="300">');
								echo('<a href="http://www.superfoots.com/nunchaku.html">www.superfoots.com</a>');
								echo('</td>');
							echo('</tr>');

							echo('<tr>');
								echo('<td>&nbsp;</td>');
								echo('<td width="300">');
								echo('<a href="http://mwmas.com/nunchaku.htm">mwmas.com</a>');
								echo('</td>');
							echo('</tr>');

							echo('<tr>');
								echo('<td>&nbsp;</td>');
								echo('<td width="300">');
								echo('<a href="http://www.goldxpress.twoffice.com/catalog2343.html">www.goldxpress.twoffice.com</a>');
								echo('</td>');
							echo('</tr>');

							echo('<tr>');
								echo('<td>&nbsp;</td>');
								echo('<td width="300">');
								echo('<a href="http://www.okiadventures.com/html/nunchuku.html">www.okiadventures.com</a>');
								echo('</td>');
							echo('</tr>');

							echo('<tr>');
								echo('<td>&nbsp;</td>');
								echo('<td width="300">');
								echo('<a href="http://www.stewartgallery.net/blades/chucks.htm">www.stewartgallery.net</a>');
								echo('</td>');
							echo('</tr>');

							echo('<tr>');
								echo('<td>&nbsp;</td>');
								echo('<td width="300">');
								echo('<a href="http://store.yahoo.com/asuperbuy/nunchakus.html">store.yahoo.com/asuperbuy</a>');
								echo('</td>');
							echo('</tr>');

							echo('<tr>');
								echo('<td>&nbsp;</td>');
								echo('<td width="300">');
								echo('<a href="http://www.dragonsupply.com/ma/nunchaku.htm">www.dragonsupply.com</a>');
								echo('</td>');
							echo('</tr>');

							echo('<tr>');
								echo('<td>&nbsp;</td>');
								echo('<td width="300">');
								echo('<a href="http://www.martialmart.com/karatemart1/nunchuckus.html">www.martialmart.com</a>');
								echo('</td>');
							echo('</tr>');

							echo('<tr>');
								echo('<td>&nbsp;</td>');
								echo('<td width="300">');
								echo('<a href="http://www.themeware-ipagebuilder.com/home/ns/ninjaarts/catalog2689.html">www.themeware-ipagebuilder.com/home/ns/ninjaarts</a>');
								echo('</td>');
							echo('</tr>');


						echo('</table>');
					echo('</td>');
			?>
	</tr>

	</table>

</body>
</html>