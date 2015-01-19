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
		<td><div class="username" data-toggle="tooltip" data-placement="auto" title="<?php p($_['event']['user']) ?>" data-key="user" data-val="<?php p($_['event']['user']) ?>"><?php p($_['event']['username']) ?></div></td>
		<td><div class="type" data-toggle="tooltip" data-placement="auto" title="<?php p($_['event']['type']) ?>"><span class="glyphicon <?php p($_['event']['typeicon'])?>" aria-hidden="true" title="<?php p($_['event']['type']) ?>"></span></div></td>
		<td><div class="filename" data-toggle="tooltip" data-placement="auto" title="<?php p($_['event']['subjecttitle']) ?>"><?php p($_['event']['subjectformatted']['trimmed']) ?></div></td>
		<td><div class="os" data-toggle="tooltip" data-placement="auto" title="<?php p($_['event']['os']) ?>"><?php p($_['event']['os']) ?></div></td>
		<td><div class="device" data-toggle="tooltip" data-placement="auto" title="<?php p($_['event']['device']) ?>"><?php p($_['event']['device']) ?></div></td>
		<td><div class="browser" data-toggle="tooltip" data-placement="auto" title="<?php p($_['event']['browser']) ?>"><?php p($_['event']['browser']) ?></div></td>
		<td><div class="userip" data-toggle="tooltip" data-placement="auto" title="<?php p($_['event']['userip']) ?>" data-key="userIP" data-val="<?php p($_['event']['userip']) ?>"><?php p($_['event']['userip']) ?></div></td>
		<td><div class="time"
			data-toggle="tooltip" data-placement="auto"
			title="<?php p($_['formattedDate']) ?>"><?php /* p($_['formattedTimestamp']) */ ?><?php p($_['formattedDate']) ?></div></td>
		<?php if ($_['grouping']===false) { ?><td><div class="checksum" data-toggle="tooltip" data-placement="auto" title="<?php p($_['event']['checksum']) ?>"><?php p($_['event']['checksum']) ?></div></td><?php }; ?>
	</tr>