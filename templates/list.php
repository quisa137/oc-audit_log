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
	<div id="audit_log_search" class='modal fade' tab-index='-1' role='dialog' aria-labelledby="searchLabel" aria-hidden="true">
	 <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="searchLabel"><?php p($l->t('Search Detail'))?></h4>
	      </div>
	      <div class="modal-body">
	        <form class="search form-horizontal">
	        	<div class="form-group">
	        		<label for="" class="col-sm-2 control-label"><?php p($l->t('Date Range'))?></label>
	        		<div class="col-sm-10">
	        			<div class="form-inline">
	        			<div class="form-group">
		        		<div class="dateRange input-group">
						    <div class="input-group-addon">
						    	<i class="glyphicon glyphicon-calendar"></i>
						    </div>
						    <input type="text" value="<?php p(date("Y-m-d", strtotime('-30 day'))) ?> ~ <?php p(date("Y-m-d")) ?>" class="input-sm form-control">
						</div>
						</div>
						</div>
	        		</div>
	        	</div>
	        	<div class="form-group">
	        		<label for="" class="col-sm-2 control-label"><?php p($l->t('DEVICE'))?></label>
	        		<div class="col-sm-10">
	        		  <select name="device" multiple></select>
					</div>
	        	</div>
	        	<div class="form-group">
	        		<label for="" class="col-sm-2 control-label"><?php p($l->t('OS'))?></label>
	        		<div class="col-sm-10">
	        		<div class="btn-group" data-toggle="buttons">
					  <label class="btn btn-primary">
					    <input type="checkbox"  name="os" value="Windows" autocomplete="off"> 윈도우
					  </label>
                   <label class="btn btn-primary">
					    <input type="checkbox"  name="os" value="Linux" autocomplete="off"> 리눅스
					  </label>
					  <label class="btn btn-primary">
					    <input type="checkbox"  name="os" value="OSX" autocomplete="off"> OS X/iOS
					  </label>
					  <label class="btn btn-primary">
					    <input type="checkbox"  name="os" value="Android" autocomplete="off"> 안드로이드
					  </label>
					</div>
					</div>
	        	</div>
	        	<div class="form-group">
	        		<label for="" class="col-sm-2 control-label"><?php p($l->t('user IP'))?></label>
	        		<div class="col-sm-10">
	        			<input type="text" name="userIP" id="" class="input"/>
					</div>
	        	</div>
	        	<div class="form-group">
	        		<label for="" class="col-sm-2 control-label"><?php p($l->t('user ID'))?></label>
	        		<div class="col-sm-10">
	        			<input type="text" name="user" id="" class="input typeahead" data-provide="typeahead" autocomplete="off"/>
					</div>
	        	</div>
	        	<div class="form-group">
	        		<label for="" class="col-sm-2 control-label"><?php p($l->t('File Name'))?></label>
	        		<div class="col-sm-10">
	        			<input type="text" name="fileName" id="" class="form-control"/>
					</div>
	        	</div>
	        	<div class="form-group">
	        		<label for="" class="col-sm-2 control-label"><?php p($l->t('Checksum'))?></label>
	        		<div class="col-sm-10">
	        			<input type="text" name="checksum" id="" class="form-control"/>
					</div>
	        	</div>
	        	</form>
	      </div>
	      <div class="modal-footer">
	        <button class="btn btn-default" data-dismiss="modal"><?php p($l->t('Close'))?></button>
	        <button id="btnSubmit" class="btn btn-primary"><?php p($l->t('Search'))?></button>
	      </div>
	    </div>
	  </div>
	</div>
	<div role="tabpanel">
	  <!-- Nav tabs -->
	  <ul id='auditlog_tabs' class="nav nav-tabs" role="tablist">
	    <li role="presentation" class="active"><a href="#grouped" aria-controls="container" role="tab" data-toggle="tab"><?php p($l->t('GROUPED'))?></a></li>
	    <li role="presentation"><a href="#raw" aria-controls="container" role="tab" data-toggle="tab"><?php p($l->t('RAW'))?></a></li>
	    <li role="presentation"><a href="#search" data-toggle="modal" data-target="#audit_log_search" tab-index='-1' onfocus='javascript:blur();'><?php p($l->t('Search'))?></a></li>
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
