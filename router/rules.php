<?php

	require ('autonomous.inc.php'); 
	session_start ();
	ob_start ();
	
	$_SESSION["Rules"] = ShorewallGetRules ( "/home/aaron/rules" );

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>autonomous</title>
<link rel="STYLESHEET" href="style.css" type="text/css">
</head>
<body bgcolor="FFFFFF">
<form action="test.php" method="post">
<div align="center">
<table width="750" border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td colspan="3" width="750">
   <table width="750" border="0" cellpadding="0" cellspacing="0">
    <tr>
     <td width="750" colspan="3">
     <span style="font-size:28px;color:85C329">
     Autonomous
     </span>
     </td>
    </tr>
    <tr>
     <td width="330">
     <span style="font-size:20px;color:CCCCCC">
     Self-Governing Routing
     </span>
     </td>
     <td width="400" align="right">
      <table width="400" border="0" cellpadding="0" cellspacing="0">
       <tr>
        <td align="right" height="20">
        <span style="color:AAAAAA">
        search for term&nbsp;
        </span>
        <input type="text" onFocus="this.style.background='FFFFFF';" onBlur="this.style.background='F9F9F9';">&nbsp;
        <input type="submit" style="padding:0;background-color:85C329;border:0;color:FFFFFF;width:25;font-weight:bold" value="GO">
        </td>
       </tr>
      </table>
     </td>
     <td width="20"></td>
    </tr>
   </table>
  </td>
 </tr>
 <tr>
  <td colspan="3" height="15" bgcolor="FFFFFF"></td>
 </tr>
 <tr>
  <td colspan="3" height="1" bgcolor="CCCCCC"></td>
 </tr>
 <tr>
  <td colspan="3" height="10" bgcolor="FFFFFF"></td>
 </tr>
 <tr>
  <td width="170" bgcolor="FFFFFF" valign="top">
  <span style="font-size:6px"><br></span>
  <div align="center">
   <table width="140" border="0" cellpadding="0" cellspacing="0">
    <tr>
     <td><a href="#">company overview</a></td>
    </tr>
    <tr>
     <td>about our company</td>
    </tr>
    <tr>
     <td height="5" bgcolor="FFFFFF"></td>
    </tr>
    <tr>
     <td><a href="#">services & products</a></td>
    </tr>
    <tr>
     <td>what we provide</td>
    </tr>
    <tr>
     <td height="5" bgcolor="FFFFFF"></td>
    </tr>
    <tr>
     <td><a href="#">partnerships</a></td>
    </tr>
    <tr>
     <td>look at all our partners</td>
    </tr>
    <tr>
     <td height="5" bgcolor="FFFFFF"></td>
    </tr>
    <tr>
     <td><a href="#">online portfolio</a></td>
    </tr>
    <tr>
     <td>see all of our clients</td>
    </tr>
    <tr>
     <td height="5" bgcolor="FFFFFF"></td>
    </tr>
    <tr>
     <td><a href="#">registration</a></td>
    </tr>
    <tr>
     <td>how you can register</td>
    </tr>
    <tr>
     <td height="5" bgcolor="FFFFFF"></td>
    </tr>
    <tr>
     <td><a href="#">contacting us</a></td>
    </tr>
    <tr>
     <td>how you can reach us</td>
    </tr>
   </table>
   <br><span style="font-size:6px"><br></span>
   <table width="140" border="0" cellpadding="0" cellspacing="0">
    <tr>
     <td colspan="3" height="1" bgcolor="AAAAAA"></td>
    </tr>
    <tr>
     <td width="1" bgcolor="AAAAAA" rowspan="5"></td>
     <td width="138" height="26" bgcolor="F9F9F9" style="color:85C329;font-size:14px">
     &nbsp;&nbsp;announcements
     </td>
     <td width="1" bgcolor="AAAAAA" rowspan="5"></td>
    </tr>
    <tr>
     <td height="1" bgcolor="AAAAAA"></td>
    </tr>
    <tr>
     <td bgcolor="FFFFFF">
      <table width="138" border="0" cellpadding="7" cellspacing="0">
       <tr>
        <td style="color:AAAAAA">
        4/30 - idea spawned
        <br><span style="font-size:6px"><br></span>
        5/1 - design created
        <br><span style="font-size:6px"><br></span>
        5/1 - design submitted to OSWD for approval
        </td>
       </tr>
      </table>
     </td>
    </tr>
    <tr>
     <td height="1" bgcolor="AAAAAA"></td>
    </tr>
   </table>
  <span style="font-size:6px"><br></span>
  </div>
  </td>
  <td width="1" bgcolor="CCCCCC"></td>
  <td width="579" valign="top">
  <span style="font-size:6px"><br></span>
  <div align="center">
   
		<?php
			foreach ( GenerateDisplayArray ( $_SESSION["Rules"] ) as $rule ) {
			?>
					
		<table width="549" border="0" cellpadding="0" cellspacing="0">
    <tr>
     <td colspan="4" height="1" bgcolor="AAAAAA"></td>
     <td width="5" height="1" bgcolor="FFFFFF"></td>
    </tr>
    <tr>
     <td width="1" bgcolor="AAAAAA"></td>
     <td rowspan="2" colspan="2" width="542" height="27" bgcolor="F9F9F9" style="color:85C329;font-size:15px;font-weight:bold;" valign='middle'><div style='float:right;padding-right:5px;' ><input name="Action[<?= $rule["ID"] ?>]" value="Delete" type='image' src='delete-15x15.png' style='border:none;' /></div><div style='padding:0px 0px 0px 5px'><input type='text' value='<?= htmlentities ( $rule["Description"] ) ?>' name="Description[<?= $rule["ID"] ?>]" style='border:none; color:#85C329; font-size:15px;' /></div></td>
     <td width="1" bgcolor="AAAAAA"></td>
     <td width="5" height="4" bgcolor="FFFFFF"></td>
    </tr>
    <tr>
     <td width="1" bgcolor="AAAAAA"></td>
     <td width="1" bgcolor="AAAAAA"></td>
     <td width="5" bgcolor="F0F0F0" height="23"></td>
    </tr>
    <tr>
     <td width="1" bgcolor="AAAAAA"></td>
     <td colspan="2" height="1" bgcolor="AAAAAA"></td>
     <td width="1" bgcolor="AAAAAA"></td>
     <td width="5" bgcolor="F0F0F0"></td>
    </tr>
    <tr>
     <td width="1" bgcolor="AAAAAA"></td>
     <td colspan="2" bgcolor="FFFFFF">
      <table width="542" border="0" cellpadding="17" cellspacing="0">
       <tr>
        <td style="color:999999;line-height:1.6em">
        <div align="justify">
			<table cellpadding='3' cellspacing='0' border='0'>
				<tr>
					<th>LAN Computer IP</th>
					<td><input type='text' name="LAN IP[<?= $rule["ID"] ?>]" value="<?= htmlentities ( $rule["LAN IP"] ) ?>" size='15' /></td>
					<th>Protocol</th>
					<td>
						<select name="Protocol[<?= $rule["ID"] ?>]">
							<option value="tcp" <?php echo ( $rule["Protocol"] == "tcp" ? "selected" : "" ); ?>>TCP</option>
							<option value="udp" <?php echo ( $rule["Protocol"] == "udp" ? "selected" : "" ); ?>>UDP</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>LAN Port</th>
					<td colspan='1'>
						<input type='text' name="LAN Port Start[<?= $rule["ID"] ?>]" value="<?= htmlentities ( $rule["LAN Port Start"] ) ?>" size='5' />
						<input type='text' name="LAN Port End[<?= $rule["ID"] ?>]" value="<?= htmlentities ( $rule["LAN Port End"] ) ?>" size='5' />
					</td>
					<th align='right'>Internet Port</th>
					<td colspan='1' align='left'>
						<input type='text' name="WAN Port Start[<?= $rule["ID"] ?>]" value="<?= htmlentities ( $rule["WAN Port Start"] ) ?>" size='5' />
						<input type='text' name="WAN Port End[<?= $rule["ID"] ?>]" value="<?= htmlentities ( $rule["WAN Port End"] ) ?>" size='5' />
					</td>
				</tr>
			</table>
        </div>
        </td>
       </tr>
      </table>
     </td>
     <td width="1" bgcolor="AAAAAA"></td>
     <td width="5" bgcolor="F0F0F0"></td>
    </tr>
    <tr>
     <td width="1" bgcolor="AAAAAA"></td>
     <td colspan="2" height="1" bgcolor="AAAAAA"></td>
     <td width="1" bgcolor="AAAAAA"></td>
     <td width="5" bgcolor="F0F0F0"></td>
    </tr>
    <tr>
     <td width="1" height="5" bgcolor="FFFFFF"></td>
     <td width="4" height="5" bgcolor="FFFFFF"></td>
     <td width="538" height="5" bgcolor="F0F0F0"></td>
     <td width="1" height="5" bgcolor="F0F0F0"></td>
     <td width="5" height="5" bgcolor="F0F0F0"></td>
    </tr>
   </table>
		<br>

			<?php
			}
		?>
  
   <table width="549" border="0" cellpadding="0" cellspacing="0">
    <tr>
     <td colspan="4" height="1" bgcolor="AAAAAA"></td>
     <td width="5" height="1" bgcolor="FFFFFF"></td>
    </tr>
    <tr>
     <td width="1" bgcolor="AAAAAA"></td>
     <td rowspan="2" colspan="2" width="542" height="27" bgcolor="F9F9F9" style="color:85C329;font-size:15px">&nbsp;&nbsp;thoughts on this design</td>
     <td width="1" bgcolor="AAAAAA"></td>
     <td width="5" height="4" bgcolor="FFFFFF"></td>
    </tr>
    <tr>
     <td width="1" bgcolor="AAAAAA"></td>
     <td width="1" bgcolor="AAAAAA"></td>
     <td width="5" bgcolor="F0F0F0" height="23"></td>
    </tr>
    <tr>
     <td width="1" bgcolor="AAAAAA"></td>
     <td colspan="2" height="1" bgcolor="AAAAAA"></td>
     <td width="1" bgcolor="AAAAAA"></td>
     <td width="5" bgcolor="F0F0F0"></td>
    </tr>
    <tr>
     <td width="1" bgcolor="AAAAAA"></td>
     <td colspan="2" bgcolor="FFFFFF">
      <table width="542" border="0" cellpadding="17" cellspacing="0">
       <tr>
        <td style="color:999999;line-height:1.6em">
        <div align="justify">
        After <a href="http://www.oswd.org/design/1529/orangray/" style="font-size:12px">orangray</a>, I wasn't happy with what I had made, so I decided to make another orange and gray design. This one I like much better and everything doesn't feel so cramped.
        </div>
        </td>
       </tr>
      </table>
     </td>
     <td width="1" bgcolor="AAAAAA"></td>
     <td width="5" bgcolor="F0F0F0"></td>
    </tr>
    <tr>
     <td width="1" bgcolor="AAAAAA"></td>
     <td colspan="2" height="1" bgcolor="AAAAAA"></td>
     <td width="1" bgcolor="AAAAAA"></td>
     <td width="5" bgcolor="F0F0F0"></td>
    </tr>
    <tr>
     <td width="1" height="5" bgcolor="FFFFFF"></td>
     <td width="4" height="5" bgcolor="FFFFFF"></td>
     <td width="538" height="5" bgcolor="F0F0F0"></td>
     <td width="1" height="5" bgcolor="F0F0F0"></td>
     <td width="5" height="5" bgcolor="F0F0F0"></td>
    </tr>
   </table>
  <br>
   <table width="549" border="0" cellpadding="0" cellspacing="0">
    <tr>
     <td colspan="4" height="1" bgcolor="AAAAAA"></td>
     <td width="5" height="1" bgcolor="FFFFFF"></td>
    </tr>
    <tr>
     <td width="1" bgcolor="AAAAAA"></td>
     <td rowspan="2" colspan="2" width="542" height="27" bgcolor="F9F9F9" style="color:85C329;font-size:15px">&nbsp;&nbsp;design usage</td>
     <td width="1" bgcolor="AAAAAA"></td>
     <td width="5" height="4" bgcolor="FFFFFF"></td>
    </tr>
    <tr>
     <td width="1" bgcolor="AAAAAA"></td>
     <td width="1" bgcolor="AAAAAA"></td>
     <td width="5" bgcolor="F0F0F0" height="23"></td>
    </tr>
    <tr>
     <td width="1" bgcolor="AAAAAA"></td>
     <td colspan="2" height="1" bgcolor="AAAAAA"></td>
     <td width="1" bgcolor="AAAAAA"></td>
     <td width="5" bgcolor="F0F0F0"></td>
    </tr>
    <tr>
     <td width="1" bgcolor="AAAAAA"></td>
     <td colspan="2" bgcolor="FFFFFF">
      <table width="542" border="0" cellpadding="17" cellspacing="0">
       <tr>
        <td style="color:999999;line-height:1.6em">
        <div align="justify">
        This design was made to be full screen on a 800 x 600, but works with any other resolution.
        <br>
        Anyone is free to use this design, but if you do, <a href="mailto:webmaster@principleofdesign.com" style="font-size:12px">email me</a> please. Thanks! 
        </div>
        </td>
       </tr>
      </table>
     </td>
     <td width="1" bgcolor="AAAAAA"></td>
     <td width="5" bgcolor="F0F0F0"></td>
    </tr>
    <tr>
     <td width="1" bgcolor="AAAAAA"></td>
     <td colspan="2" height="1" bgcolor="AAAAAA"></td>
     <td width="1" bgcolor="AAAAAA"></td>
     <td width="5" bgcolor="F0F0F0"></td>
    </tr>
    <tr>
     <td width="1" height="5" bgcolor="FFFFFF"></td>
     <td width="4" height="5" bgcolor="FFFFFF"></td>
     <td width="538" height="5" bgcolor="F0F0F0"></td>
     <td width="1" height="5" bgcolor="F0F0F0"></td>
     <td width="5" height="5" bgcolor="F0F0F0"></td>
    </tr>
   </table>
  <span style="font-size:6px"><br></span>
  <input type='submit' value='Update' />&nbsp;&nbsp;<input type='reset' name='reset' value='Retry' />
  </div>
  </td>
 </tr>
 <tr>
  <td colspan="3" height="10" bgcolor="FFFFFF"></td>
 </tr>
 <tr>
  <td colspan="3" height="1" bgcolor="CCCCCC"></td>
 </tr>
 <tr>
  <td colspan="3" height="5" bgcolor="FFFFFF"></td>
 </tr>
 <tr>
  <td colspan="3" bgcolor="FFFFFF" align="right">
  Copyright &#0169; 2004, <a href="http://www.principleofdesign.com" style="font-size:12px;color:AAAAAA">Adam Particka</a>. All Rights Reserved.
  </td>
 </tr>
</table>
</div>
</form>
</body>
</html>