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
    <div id="audit_log_modal" class='modal fade' role='dialog' aria-labelledby="searchLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content"></div>
        </div>
    </div>
    <div role="tabpanel">
      <!-- Nav pills -->
      <div id="auditlog_tabs" class="btn-toolbar" role="toolbar">
      <div class="btn-group" role="group">
          <button id="grouped" aria-controls="container" class='active btn btn-default btn-sm'><?php p($l->t('GROUPED'))?></button>
          <button id="raw" aria-controls="container" class='btn btn-default btn-sm'><?php p($l->t('RAW'))?></button>
      </div>
      <div class="btn-group" role="group">
          <button data-toggle="modal" data-target="#audit_log_modal" onfocus='javascript:blur();' class='btn btn-default btn-sm' data-uri='search'><?php p($l->t('Search'))?></button>
          <button data-toggle="modal" data-target="#audit_log_modal" onfocus='javascript:blur();' class='btn btn-default btn-sm' data-uri='setting'><?php p($l->t('Settings'))?></button>
          <button id="list" class='btn btn-default btn-sm' disabled><?php p($l->t('List'))?></button>
      </div>
      </div>
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
