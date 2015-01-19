
$(function(){
    var commonInit = function(targetFrm) {
                //검색창에서 검색기간 설정 시,
        targetFrm.find(".dateRange").daterangepicker({
            format: 'YYYY-MM-DD',
            locale:{
                applyLabel: '입력',
                cancelLabel: '취소',
                fromLabel: '부터',
                toLabel: '까지',
                weekLabel: '일',
                customRangeLabel: '날짜 선택',
                daysOfWeek: moment.weekdaysMin(),
                monthNames: moment.monthsShort(),
                firstDay: moment.localeData()._week.dow
            },
            ranges: {
                '오늘': [moment(), moment().add(1, 'days')],
                '어제': [moment().subtract(1, 'days'), moment()],
                '지난7일': [moment().subtract(6, 'days'), moment()],
                '지난30일': [moment().subtract(29, 'days'), moment()],
                '이달': [moment().startOf('month'), moment().endOf('month')],
                '지난달': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate: moment()
        },
        function(start, end) {
            $('.dateRange input').val(start.format('YYYY-MM-DD') + ' ~ ' + end.format('YYYY-MM-DD'));
        });
        //자동완성 기능 - 선택 시, 아이디도 같이 검색된다.
        var userlist = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('displayname'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            limit: 30,
            remote: {
                url: OC.generateUrl('/settings/ajax/userlist') +'?offset=0&limit=30&gid=&pattern=%QUERY',
                filter: function(list) {
                    return list.data;
                }
            }
        });
        userlist.initialize();
        targetFrm.find('[name=username].typeahead').typeahead({minLength:1,highlight:true}, {
            name: 'userlist',
            displayKey: 'displayname',
            source:userlist.ttAdapter(),
            templates:{
                empty: '<div class="empty-message">검색 결과 없음</div>',
                suggestion: Handlebars.compile('<p><strong data-userid="{{name}}">{{displayname}}</strong></p>')
            }
        });
        targetFrm.find('.tt-dataset-userlist').on('click','.tt-suggestion',function(e){
            targetFrm.find('[name=user]').val($(e.target).data('userid'));
        });
        //Search 폼에 디바이스를 박아 넣음 첫 실행시, 한번만 실행하면 된다.
        $.get(OC.filePath('audit_log','ajax','fetch_devices.php'),function(res){
            optionHTML = [];
            for(var i=0;i<res.length;i++) {
                optionHTML.push('<option value='+res[i]['device']+'>'+res[i]['device']+'</option>');
            }
            targetFrm.find('select[name=device]').append(optionHTML.join(''));
        });
        //Search 폼에 동작유형들을 박아 넣음 첫 실행시, 한번만 실행하면 된다.
        $.get(OC.filePath('audit_log','ajax','fetch_types.php'),function(res){
            optionHTML = [];
            for(var i=0;i<res.length;i++) {
                optionHTML.push('<option value='+res[i]['type']+'>'+res[i]['type']+'</option>');
            }
            targetFrm.find('select[name=types]').append(optionHTML.join(''));
        });
    };
    var Search = function() {
        var searchFrm = $('#audit_log_modal');
        searchFrm.find('#btnSubmit').on('click',function(e) {
            e.preventDefault();
            $.OCAudit_log.Filter.setSearchOption();
            $.OCAudit_log.Filter.setFilter('all')
            $.OCAudit_log.Filter.setGrouping(false);
            $('#audit_log_search').modal('hide');
        });
        commonInit(searchFrm);
    };
    var Setting = {
        currentPage: 0,
        filters: [],
        container:null,
        settingFrm:null,
        rowHTML: [
            '<li class="col-xs-12" data-id="{{id}}">',
                '<div class="col-xs-4">{{emails}}</div>',
                '<div class="col-xs-4">{{filtername}}</div>',
                '<div class="col-xs-4"><a href="" class="modify">수정</a>&nbsp;&nbsp;<a href="" class="delete">삭제</a></div>',
            '</li>'].join(""),
        initialize : function() {
            this.settingFrm = $('#audit_log_modal');
            this.settingFrm.find('.detailed').addClass('hidden');
            this.container = this.settingFrm.find('#filterList');
            this.getFilters();
            //수정
            this.container.on('click','.modify',function(e){
                e.preventDefault();
                //값 채워 넣고 화면 보이게 
                var t = $(e.currentTarget),
                    id = t.parents('tr:first').data('id'),
                    filter = Setting.filters[id];

                Setting.settingFrm.find('.listed').addClass('hidden');
                Setting.settingFrm.find('.detailed').removeClass('hidden');
            });
            //삭제
            this.container.on('click','.delete',function(e){
                e.preventDefault();
                if(confirm('정말 삭제하시겠습니까?')) {
                    Settings.deleteFilter($(e.currentTarget).data('id'));
                }
            });
            //필터 추가
            this.settingFrm.on('click','[name=add]',function(e){
                e.preventDefault();
                Setting.settingFrm.find('.listed').addClass('hidden');
                Setting.settingFrm.find('.detailed').removeClass('hidden');
            });
            this.settingFrm.on('click','#btnSave',function(e){
                e.preventDefault();
                var filter = Setting.formToArray();
                var idx = filter['idx'];
                delete filter['idx'];

                if(idx === '' || idx<=0 || idx===undefined) {
                    //쓰기 모드
                    Setting.filters.push(filter);
                } else if(idx > 0) {
                    //수정 모드
                    Setting.filters[idx] = filter;
                }
                Setting.setFilters();
            });
            commonInit(this.settingFrm);
        },
        formToArray : function() {
            var filterRow = {};
            this.settingFrm.find('.detailed').find('input,select,textarea').each(function(i,item){
                var item = $(item);
                if(item.is('select')) {
                    var selected = [];
                    item.find(':selected').val(function(idx,val){selected.push(val);return val;});
                    
                    if (selected.length > 1) {
                        filterRow[item.attr('name')] = selected.join(',');
                    } else if(selected.length==1) {
                        filterRow[item.attr('name')] = selected[0];
                    } else if(selected.length==0) {
                        filterRow[item.attr('name')] = '';
                    }
                } else {
                    if(item.is(':text') || item.is(':hidden') || item.is('textarea')) {
                        filterRow[item.attr('name')] = item.val();
                    } else if(item.is(':checkbox') || item.is(':radio')) {
                        if(filterRow[item.attr('name')]===undefined) {
                            var checked = [];
                            Setting.settingFrm.find('[name='+item.attr('name')+']').val(function(idx,val) {
                                if(val==='' || val==='on' || val==='off'){
                                    if(item.prop('checked')){
                                        checked.push(true);
                                    }else{
                                        checked.push(false);
                                    }
                                }else{
                                    if(item.prop('checked')){
                                        checked.push(val);
                                    }
                                }
                                return val;
                            });
                            if(checked.length > 1) {
                                filterRow[item.attr('name')] = checked.join(',');
                            } else if(checked.length===1) {
                                filterRow[item.attr('name')] = checked[0];
                            } else if(checked.length===0) {
                                filterRow[item.attr('name')] = '';
                            }
                        }
                    }
                }
            });
            return filterRow;
        },
        modifyFilter : function(id,name,val) {
            this.setFilters();
        },
        deleteFilter : function(id) {
            this.filters = $.grep(this.filters,function(elem,i){
                return id !== i;
            });
            this.setFilters();
        },
        reset : function() {
            Setting.filters = [];
            Setting.container.find('.loading').removeClass('hidden');
            Setting.container.find('.noexist').addClass('hidden');
        },
        getFilters : function() {
            this.currentPage++;
            $.get(OC.filePath('audit_log','ajax','fetch_filters.php'),{page:this.currentPage},function(resp) {
                if(resp && resp.length) {
                    Setting.filters = $.parseJSON(resp);
                    Setting.appendContent();
                }else{
                    Setting.container.find('.loading').addClass('hidden');
                    Setting.container.find('.noexist').removeClass('hidden');
                }
            });
        },
        setFilters : function() {
            $.post(OC.filePath('audit_log','ajax','save_filters.php'),{filters:JSON.stringify(Setting.filters)},function(resp) {
                if(resp.status==='success') {
                    alert('저장 되었습니다.');
                }
            });
        },
        appendContent : function() {
            var filters = Setting.filters;
            var html = [];
            for(var i=0;i < filters.length;i++) {
                var row = filters[i];
                html.push(Setting.rowHTML.replace(/{{id}}/gi,i).replace(/{{emails}}/gi,row['emails']).replace(/{{filtername}}/gi,row['filtername']));
            }
            Setting.container.append(html.join(""));
            if(html.length>0) {
                Setting.container.find('.loading').addClass('hidden');
                Setting.container.find('.noexist').addClass('hidden');
            }
        }
    };
    //모달 창 제어 스크립트
    $('#auditlog_tabs .btn-group:eq(1) [data-toggle=modal]').click(function(e){
        $('#audit_log_modal').data('uri',$(e.currentTarget).data('uri'));
    });
    $('#audit_log_modal').on('show.bs.modal',function(e){
        var tg = $(e.currentTarget),
        content = tg.find('.modal-content');
        if(tg.data('uri') !== content.data('uri')) {
            content.data('uri',tg.data('uri')).load(OC.filePath('audit_log','ajax','getDialog.php'),{dialog:tg.data('uri')},function(resp) {
                //검색 버튼 누를 때,
                if(resp.status !== 'error') {
                    switch($(this).data('uri')) {
                        case 'search':
                        Search();
                        break;
                        case 'setting':
                        Setting.initialize();
                        break;
                    }
                }
            });
        }
    });
    $.Setting = Setting;
});