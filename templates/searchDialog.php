<?php
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="searchLabel"><?php p($l->t('Detail Search'))?></h4>
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
        <label for="" class="col-sm-2 control-label"><?php p($l->t('types'))?></label>
        <div class="col-sm-10">
          <select name="types" multiple></select>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label"><?php p($l->t('device'))?></label>
        <div class="col-sm-10">
          <select name="device" multiple></select>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label"><?php p($l->t('os'))?></label>
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
        <label for="" class="col-sm-2 control-label"><?php p($l->t('userip'))?></label>
        <div class="col-sm-10">
            <input type="text" name="userIP" id="" class="input"/>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label"><?php p($l->t('user'))?></label>
        <div class="col-sm-10">
            <input type="hidden" name="user"/>
            <input type="text" name="username" id="" class="input typeahead" data-provide="typeahead" autocomplete="off"/>
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
