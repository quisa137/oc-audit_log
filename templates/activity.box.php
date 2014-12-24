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
		<td><div class="username"><?php p($_['event']['username']) ?></div></td>
		<td><div class="type"><span class="glyphicon <?php p($_['event']['typeicon'])?>" aria-hidden="true" title="<?php p($_['event']['type']) ?>"></span></div></td>
		<td><div class="filename" data-toggle="tooltip" data-placement="auto" title="<?php p($_['event']['file']) ?>"><span><?php p($_['event']['file']) ?></span></div></td>
		<td><div class="os"><?php p($_['event']['os']) ?></div></td>
		<td><div class="device"><?php p($_['event']['device']) ?></div></td>
		<td><div class="browser"><?php p($_['event']['browser']) ?></div></td>
		<td><div class="userip"><?php p($_['event']['userip']) ?></div></td>
		<td><div class="checksum"><?php p($_['event']['checksum']) ?></div></td>
		<td><div class="time"
			title="<?php p($_['formattedDate']) ?>">
			<?php /* p($_['formattedTimestamp']) */ ?>
			<?php p($_['formattedDate']) ?>
		</div></td>
	</tr>