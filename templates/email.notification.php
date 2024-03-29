<?php
/*
 * Copyright (c) 2014, Joas Schilling nickvergessen@owncloud.com
 * This file is licensed under the Affero General Public License version 3
 * or later. See the COPYING-README file.
 */

/**
 * @var OC_L10N $l
 */
/**
 * @var array $_
 */
?>
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td>
			<table cellspacing="0" cellpadding="0" border="0" width="600px">
				<tr>
					<td bgcolor="<?php p($theme->getMailHeaderColor());?>" width="20px">&nbsp;</td>
					<td bgcolor="<?php p($theme->getMailHeaderColor());?>"><img
						src="<?php p(OC_Helper::makeURLAbsolute(image_path('', 'logo-mail.gif'))); ?>"
						alt="<?php p($theme->getName()); ?>" /></td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td width="20px">&nbsp;</td>
					<td
						style="font-weight: normal; font-size: 0.8em; line-height: 1.2em; font-family: verdana, 'arial', sans;">
<?php

p ( $l->t ( 'Hello %s,', array (
 $_ ['username'] 
) ) );
echo ("<br>");
echo ("<br>");
if ($_ ['timeframe'] ==
  \OCA\Audit_log\UserSettings::EMAIL_SEND_HOURLY) {
 p ( $l->t ( 'You are receiving this email because in the last hour the following things happened at %s', array (
  $_ ['owncloud_installation'] 
 ) ) );
} else if ($_ ['timeframe'] ==
  \OCA\Audit_log\UserSettings::EMAIL_SEND_DAILY) {
 p ( $l->t ( 'You are receiving this email because in the last day the following things happened at %s', array (
  $_ ['owncloud_installation'] 
 ) ) );
} else {
 p ( $l->t ( 'You are receiving this email because in the last week the following things happened at %s', array (
  $_ ['owncloud_installation'] 
 ) ) );
}
echo ("<br>");
echo ("<br>");
foreach ( $_ ['activities'] as $activity ) {
 p ( $l->t ( '* %s', array (
  $activity 
 ) ) );
 echo ("<br>");
}
?>
</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<!--tr>
<td width="20px">&nbsp;</td>
<td style="font-weight:normal; font-size:0.8em; line-height:1.2em; font-family:verdana,'arial',sans;">--<br>
<?php p($theme->getName()); ?> -
<?php p($theme->getSlogan()); ?>
<br><a href="<?php p($theme->getBaseUrl()); ?>"><?php p($theme->getBaseUrl());?></a>
</td>
</tr-->
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
