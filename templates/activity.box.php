<?php
/*
 * Copyright (c) 2014, Joas Schilling nickvergessen@owncloud.com
 * This file is licensed under the Affero General Public License version 3
 * or later. See the COPYING-README file.
 */

/**
 * @var $l OC_L10N
 */
/**
 * @var $_ array
 */
?>

	<tr>
		<td><div class="username"><?php p($_['event']['user']) ?></div></td>
		<td><div class="type"><?php p($_['event']['type']) ?></div></td>
		<td><div class="filename"><?php p($_['event']['file']) ?></div></td>
		<td><div class="os"><?php p($_['event']['os']) ?></div></td>
		<td><div class="device"><?php p($_['event']['device']) ?></div></td>
		<td><div class="browser"><?php p($_['event']['browser']) ?></div></td>
		<td><div class="userip"><?php p($_['event']['userip']) ?></div></td>
		<td><div class="time"
			title="<?php p($_['formattedDate']) ?>">
			<?php /* p($_['formattedTimestamp']) */ ?>
			<?php p($_['formattedDate']) ?>
		</div></td>
	</tr>