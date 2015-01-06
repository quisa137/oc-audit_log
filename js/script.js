$(function(){
    var OCAudit_log={};

    OCAudit_log.Filter = {
        filter: undefined,
        currentPage: 0,
        grouping: true,
        navigation: $('#app-navigation'),
        tabs: $('#auditlog_tabs'),
        searchFrm: $('#audit_log_search'),
        queryStr:[],
        reset: function() {
            this.currentPage = 0;
            OCAudit_log.InfinitScrolling.container.animate({ scrollTop: 0 }, 'slow');
            OCAudit_log.InfinitScrolling.container.children().remove();
            $('#no_activities').addClass('hidden');
            $('#no_more_activities').addClass('hidden');
            $('#loading_activities').removeClass('hidden');
            OCAudit_log.InfinitScrolling.ignoreScroll = false;
            OCAudit_log.InfinitScrolling.prefill();
         },
        setFilter: function (filter) {
            if (filter === this.filter) {
                return;
              }

            this.navigation.find('a[data-navigation=' + this.filter + ']').removeClass('active');
            this.currentPage = 0;

            this.filter = filter;
            OC.Util.History.pushState('filter=' + filter);

            OCAudit_log.InfinitScrolling.container.animate({ scrollTop: 0 }, 'slow');
            OCAudit_log.InfinitScrolling.container.children().remove();
            $('#no_activities').addClass('hidden');
            $('#no_more_activities').addClass('hidden');
            $('#loading_activities').removeClass('hidden');
            OCAudit_log.InfinitScrolling.ignoreScroll = false;

            this.navigation.find('a[data-navigation=' + filter + ']').addClass('active');

            OCAudit_log.InfinitScrolling.prefill();
         },
        setGrouping: function(grouping) {
            if(grouping === this.grouping) {
                return;
              }
            this.grouping = grouping;

            this.reset();
         },
        setSearchOption: function(){
            this.queryStr = [];
            this.grouping = false;
            var dateRange = this.searchFrm.find('.dateRange input').val().split(' ~ ');
            var str = [];
            str['stdDate'] = dateRange[0];
            str['endDate'] = dateRange[1];
            str['os'] = $.makeArray(this.searchFrm.find('input[name=os]:checked').map(function(idx,elem){return $(elem).val();})).join('+');
            str['device'] = $.makeArray(this.searchFrm.find('select[name=device] option:selected').map(function(idx,elem){return $(elem).val();})).join('+');
            str['userIP'] = this.searchFrm.find('input[name=userIP]').val();
            str['user'] = this.searchFrm.find('input[name=user]').val();
            str['fileName'] = this.searchFrm.find('input[name=fileName]').val();
            str['checksum'] = this.searchFrm.find('input[name=checksum]').val();
            
            
            for(var key in str) {
                if($.type(str[key]) === 'string' && str[key] !== '')
                    this.queryStr.push(key + '=' + str[key]);
            }
            
           this.reset();
        }
    };

    OCAudit_log.InfinitScrolling = {
        ignoreScroll: false,
        container: $('#container'),
        content: $('#app-content'),

        prefill: function () {
            if (this.content.scrollTop() + this.content.height() > this.container.height() - 100) {
                OCAudit_log.Filter.currentPage++;
        
                $.get(
                    OC.filePath('audit_log', 'ajax', 'fetch.php'),
                    'filter=' + OCAudit_log.Filter.filter + '&grouping=' + OCAudit_log.Filter.grouping + '&page=' + OCAudit_log.Filter.currentPage + 
                    ((OCAudit_log.Filter.queryStr.length > 0)?'&' + OCAudit_log.Filter.queryStr.join('&'):''),
                    function(data) {
                        if (data.length) {
                            OCAudit_log.InfinitScrolling.appendContent(data);
        
                            // Continue prefill
                            OCAudit_log.InfinitScrolling.prefill();
                        }
                        else if (OCAudit_log.Filter.currentPage == 1) {
                            // First page is empty - No activities :(
                            $('#no_activities').removeClass('hidden');
                            $('#loading_activities').addClass('hidden');
                        }
                        else {
                            // Page is empty - No more activities :(
                            $('#no_more_activities').removeClass('hidden');
                            $('#loading_activities').addClass('hidden');
                        }
                    }
                );
            }
        },

        onScroll: function () {
            if (!OCAudit_log.InfinitScrolling.ignoreScroll && OCAudit_log.InfinitScrolling.content.scrollTop() +
             OCAudit_log.InfinitScrolling.content.height() > OCAudit_log.InfinitScrolling.container.height() - 100) {
                OCAudit_log.Filter.currentPage++;
        
                OCAudit_log.InfinitScrolling.ignoreScroll = true;
                $.get(
                    OC.filePath('audit_log', 'ajax', 'fetch.php'),
                    'filter=' + OCAudit_log.Filter.filter + '&grouping=' + OCAudit_log.Filter.grouping + '&page=' + OCAudit_log.Filter.currentPage + 
                    ((OCAudit_log.Filter.queryStr.length > 0)?'&' + OCAudit_log.Filter.queryStr.join('&'):''),
                    function(data) {
                        OCAudit_log.InfinitScrolling.appendContent(data);
                        OCAudit_log.InfinitScrolling.ignoreScroll = false;
        
                        if (!data.length) {
                            // Page is empty - No more activities :(
                            $('#no_more_activities').removeClass('hidden');
                            $('#loading_activities').addClass('hidden');
                            OCAudit_log.InfinitScrolling.ignoreScroll = true;
                        }
                    }
                );
            }
        },

        appendContent: function (content) {
            var firstNewGroup = $(content).first(),
                lastGroup = this.container.children().last();

            // Is the first new container the same as the last one?
            if (lastGroup && lastGroup.data('date') === firstNewGroup.data('date')) {
                var appendedBoxes = firstNewGroup.find('.box'),
                    lastBoxContainer = lastGroup.find('.boxcontainer');
            } else {
                content = $(content);
              }
            OCAudit_log.InfinitScrolling.processElements(content);
            this.container.append(content);
        },

        processElements: function (content) {
            return content;
        }
    };
    $('body').tooltip({
        html:true,
        selector:'[data-toggle="tooltip"]'
    });
    OCAudit_log.Filter.setFilter(OCAudit_log.InfinitScrolling.container.attr('data-activity-filter'));
    OCAudit_log.InfinitScrolling.content.on('scroll', OCAudit_log.InfinitScrolling.onScroll);

    OCAudit_log.Filter.navigation.find('a[data-navigation]').on('click', function (event) {
        OCAudit_log.Filter.setFilter($(this).attr('data-navigation'));
        event.preventDefault();
    });
    OCAudit_log.Filter.tabs.find('a[role=tab]').on('click',function(e) {
        OCAudit_log.Filter.setGrouping($(this).attr('href')==='#grouped');
        e.preventDefault();
    });
    
    //검색 버튼 누를 때,
    OCAudit_log.Filter.searchFrm.find('#btnSubmit').on('click',function(e) {
        e.preventDefault();
        OCAudit_log.Filter.setSearchOption();
        OCAudit_log.Filter.tabs.find('a[href=#raw]').tab('show');
        $('#audit_log_search').modal('hide');
    })
    //검색창에서 검색기간 설정 시,
    OCAudit_log.Filter.searchFrm.find(".dateRange").daterangepicker({
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
    //Search 폼에 디바이스를 박아 넣음 첫 실행시, 한번만 실행하면 된다.
    $.get(OC.filePath('audit_log','ajax','fetch_devices.php'),function(res){
        optionHTML = [];
        for(var i=0;i<res.length;i++) {
            optionHTML.push('<option value='+res[i]['device']+'>'+res[i]['device']+'</option>');
        }
        OCAudit_log.Filter.searchFrm.find('select[name=device]').append(optionHTML.join(''));
    });
    //Search 폼에 동작유형들을 박아 넣음 첫 실행시, 한번만 실행하면 된다.
    $.get(OC.filePath('audit_log','ajax','fetch_types.php'),function(res){
        optionHTML = [];
        for(var i=0;i<res.length;i++) {
            optionHTML.push('<option value='+res[i]['type']+'>'+res[i]['type']+'</option>');
        }
        OCAudit_log.Filter.searchFrm.find('select[name=types]').append(optionHTML.join(''));
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
    OCAudit_log.Filter.searchFrm.find('[name=username].typeahead').typeahead({
        minLength:1,
        highlight:true
    }, {
        name: 'userlist',
        displayKey: 'displayname',
        source:userlist.ttAdapter()
        ,templates: {
            empty: [
              '<div class="empty-message">',
              '검색 결과 없음',
              '</div>'
            ].join('\n'),
            suggestion: Handlebars.compile('<p><strong data-userid="{{name}}">{{displayname}}</strong></p>')
         }
    });
    OCAudit_log.Filter.searchFrm.find('.tt-dataset-userlist').on('click','.tt-suggestion',function(e){
        OCAudit_log.Filter.searchFrm.find('[name=user]').val($(e.target).data('userid'));
    });
});
