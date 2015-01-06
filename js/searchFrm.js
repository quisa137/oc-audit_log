/**
 * 검색폼 작동 시, 이벤트 스크립트 들
 */
$(function(){
    //검색 버튼 누를 때,
    var searchFrm = $('#audit_log_search');
    searchFrm.find('#btnSubmit').on('click',function(e) {
        e.preventDefault();
        $.OCAudit_log.Filter.setSearchOption();
        $.OCAudit_log.Filter.setFilter('all')
        $.OCAudit_log.Filter.setGrouping(false);
        $('#audit_log_search').modal('hide');
    })
    //검색창에서 검색기간 설정 시,
    searchFrm.find(".dateRange").daterangepicker({
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
         }
    );
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
    searchFrm.find('[name=username].typeahead').typeahead({minLength:1,highlight:true}, {
        name: 'userlist',
        displayKey: 'displayname',
        source:userlist.ttAdapter(),
        templates:{
            empty: '<div class="empty-message">검색 결과 없음</div>',
            suggestion: Handlebars.compile('<p><strong data-userid="{{name}}">{{displayname}}</strong></p>')
         }
    });
    searchFrm.find('.tt-dataset-userlist').on('click','.tt-suggestion',function(e){
        searchFrm.find('[name=user]').val($(e.target).data('userid'));
    });
    //Search 폼에 디바이스를 박아 넣음 첫 실행시, 한번만 실행하면 된다.
    $.get(OC.filePath('audit_log','ajax','fetch_devices.php'),function(res){
        optionHTML = [];
        for(var i=0;i<res.length;i++) {
            optionHTML.push('<option value='+res[i]['device']+'>'+res[i]['device']+'</option>');
         }
        searchFrm.find('select[name=device]').append(optionHTML.join(''));
    });
    //Search 폼에 동작유형들을 박아 넣음 첫 실행시, 한번만 실행하면 된다.
    $.get(OC.filePath('audit_log','ajax','fetch_types.php'),function(res){
        optionHTML = [];
        for(var i=0;i<res.length;i++) {
            optionHTML.push('<option value='+res[i]['type']+'>'+res[i]['type']+'</option>');
         }
        searchFrm.find('select[name=types]').append(optionHTML.join(''));
    });    
});