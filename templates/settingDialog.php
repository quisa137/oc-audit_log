<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="settingLabel"><?php p($l->t('Settings'))?></h4>
</div>
<div class="modal-body" style="max-height:420px;overflow-y:auto;">
<form class="form-horizontal listed">
    <div class="form-group">
        <button name="add"><?php p($l->t('add'))?></button>
        <ul id="filterList">
            <li class="col-xs-12">
                <div class="col-xs-4"><?php p($l->t('emails'))?></div>
                <div class="col-xs-4"><?php p($l->t('filtername'))?></div>
                <div class="col-xs-4"><?php p($l->t('action'))?></div>
            </li>
            <li class="loading col-xs-12 center"><?php p($l->t('loading'))?></li>
            <li class="noexist col-xs-12 center hidden"><?php p($l->t('filter no exist'))?></li>
            
        </ul>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label" for=""><?php p($l->t('default email'))?></label>
        <div class="col-sm-9">
            <input type="text" name="defaultEmails" id="" class="" value="<?php p($_['defaultEmails'])?>"/>
        </div>
    </div>
</form>

<form class="form-horizontal detailed">
    <div class="form-group">
        <label class="col-sm-3 control-label" for=""><?php p($l->t('filtername'))?></label>
        <div class="col-sm-9">
            <input type="hidden" name="idx" value="">
            <input type="text" name="filtername" id="" class=""/>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-3 control-label"><?php p($l->t('Notificate Range'))?></label>
        <div class="col-sm-9">
            <div class="form-inline">
                <div class="form-group">
                    <div class="dateRange input-group">
                        <div class="input-group-addon">
                            <i class="glyphicon glyphicon-calendar"></i>
                        </div>
                        <input type="text" name="daterange" value="<?php p(date("Y-m-d", strtotime('-30 day'))) ?> ~ <?php p(date("Y-m-d")) ?>" class="input-sm form-control">
                    </div>
                </div>
            </div>
            <input type="checkbox" name="consecutive" id="">계속해서 추적
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label" for=""><?php p($l->t('emails'))?></label>
        <div class="col-sm-9">
            <input type="text" name="emails" id="" class=""/>
            <span class="help-block">이메일이 여러 개이면 ; 으로 구분하여 주세요</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label" for=""><?php p($l->t('filename'))?></label>
        <div class="col-sm-9">
            <input type="text" name="filename" id="" class=""/>
            <span class="help-block">정규식으로 체크합니다.</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label" for=""><?php p($l->t('checksum'))?></label>
        <div class="col-sm-9">
            <input type="text" name="checksum" id="" class=""/>
            <span class="help-block">최우선 순위로 체크합니다.</span>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-3 control-label"><?php p($l->t('types'))?></label>
        <div class="col-sm-9">
          <select name="types" multiple></select>
          <span class="help-block">Shift키를 누르면 여러개를 선택 할 수 있습니다</span>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-3 control-label"><?php p($l->t('device'))?></label>
        <div class="col-sm-9">
          <select name="device" multiple></select>
          <span class="help-block">Shift키를 누르면 여러개를 선택 할 수 있습니다</span>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-3 control-label"><?php p($l->t('os'))?></label>
        <div class="col-sm-9">
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
        <label for="" class="col-sm-3 control-label"><?php p($l->t('priority'))?></label>
        <div class="col-sm-9">
            <select name="priority">
                <option value="10">긴급</option>
                <option value="20">중요</option>
                <option value="30" selected>보통</option>
                <option value="40">낮음</option>
            </select>
            <span class="help-block">알림 메일에 표시됩니다.</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label" for=""><?php p($l->t('userip'))?></label>
        <div class="col-sm-9">
            <input type="text" name="userip" id="" class=""/>
            <span class="help-block">아이피 대역을 체크 할 수 있습니다.(eg.192.168.0.0/24)</span>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <button id="btnSave" class="btn btn-primary"><?php p($l->t('Save'))?></button>
            <button id="btnCancel" class="btn btn-default"><?php p($l->t('Cancel'))?></button>
        </div>
    </div>
    </form>
</div>
<div class="modal-footer">
    <button class="btn btn-default" data-dismiss="modal"><?php p($l->t('Close'))?></button>
</div>
