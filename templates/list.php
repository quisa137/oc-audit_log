<?php

/**
 * ownCloud - Audit_log App
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

/**
 * @var $l OC_L10N
 */
/**
 * @var $theme OC_Defaults
 */
/**
 * @var $_ array
 */
?>
<?php $_['appNavigation']->printPage(); ?>

<div id="app-content">
	<div id="audit_log_search" class="hidden">
		<form class="searchbox">
			<input type="search" name="query" autocomplete="off" class="svg">
		</form>
	</div>
	<div role="tabpanel">
	  <!-- Nav tabs -->
	  <ul class="nav nav-tabs" role="tablist">
	    <li role="presentation" class="active"><a href="#grouped" aria-controls="container" role="tab" data-toggle="tab"><?php p($l->t('GROUPED'))?></a></li>
	    <li role="presentation"><a href="#raw" aria-controls="container" role="tab" data-toggle="tab"><?php p($l->t('RAW'))?></a></li>
	    <li role="presentation"><a href="#search" aria-controls="search" role="tab" data-toggle="tab"><?php p($l->t('Search'))?></a></li>
	  </ul>
	  <!-- Tab panes -->
	  <div class="tab-content">
		<div id="no_activities" class="hidden">
			<div class="body"><?php p($l->t('You will see a list of events here when you start to use your %s.', $theme->getTitle())) ?></div>
		</div>
		<div role="tabpanel" class="tab-pane active" id="container" data-activity-filter="all" data-activity-grouping="grouped"></div>
		<div id="loading_activities" class="icon-loading"></div>
		<div id="no_more_activities" class="hidden"><?php p($l->t('No more events to load'))?></div>
	  </div>
	</div>
</div>
