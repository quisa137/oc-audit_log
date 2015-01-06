<?php
/**
 * ownCloud - Activity App
 *
 * @author Frank Karlitschek
 * @copyright 2013 Frank Karlitschek frank@owncloud.org
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/** @var $l OC_L10N */
/** @var $theme OC_Defaults */
/** @var $_ array */

$lastDate = null;
foreach ($_['audit_log'] as $event) {
 $currentDate = (string)(\OCP\relative_modified_date($event['timestamp'], true));
 if ($currentDate !== $lastDate) {
  if($lastDate !== null) {
  ?>
  	</tbody>
  	</table>
  	</div>
  <?php 
  } 
  $lastDate = $currentDate;
  ?>
	<div class="page-header">
		<h1><?php p(ucfirst($currentDate))?></h1>
	</div>
	<div class="table-responsive">
	<table class="table tb">
	<thead>
	<tr>
		<th><?php p($l->t('user'))?></th>
		<th><?php p($l->t('type'))?></th>
		<th><?php p($l->t('filename'))?></th>
		<th><?php p($l->t('os'))?></th>
		<th><?php p($l->t('device'))?></th>
		<th><?php p($l->t('browser'))?></th>
		<th><?php p($l->t('userip'))?></th>
		<th><?php p($l->t('checksum'))?></th>
		<th><?php p($l->t('time'))?></th>
	</tr>
	</thead>
	<colgroup>
		<col class='col-sm-username col-xs-username'/>
		<col class='col-sm-type col-xs-type'/>
		<col/>
		<col class='col-sm-1'/>
		<col class='col-sm-1'/>
		<col class='col-sm-1'/>
		<col class='col-sm-1'/>
		<col class='col-sm-2'/>
		<col class="col-sm-1"/>
	</colgroup>
	<tbody>
<?php
 }
 echo \OCA\Audit_log\Display::show($event);
}
if (!empty($_['activity'])):
?>
</tbody>
</table>
</div>
<?php endif;