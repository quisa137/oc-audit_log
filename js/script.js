$(function(){
    var OCAudit_log={};

    OCAudit_log.Filter = {
        filter: undefined,
        currentPage: 0,
        grouping: true,
        navigation: $('#app-navigation'),
        pills: $('#auditlog_tabs>.btn-group:first'), //Grouping, Raw log switch
        
        searchFrm: $('#audit_log_search'),
        queryStr:[],
        initialize: function(reset) {
            if(reset) {
                this.searchFrm.find('form').each(function(i,item){item.reset();});
                this.queryStr = [];
            }
            if($.type(this.queryStr)==='array' && this.queryStr.length>0) {
                this.pills.parent().find('#list').prop('disabled', false);
            } else {
                this.pills.parent().find('#list').prop('disabled', true);
            }
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

            this.filter = filter;
            OC.Util.History.pushState('filter=' + filter);

            this.navigation.find('a[data-navigation=' + filter + ']').addClass('active');
        },
        setGrouping: function(grouping) {
            if(grouping === this.grouping) {
                return;
            }
            this.grouping = grouping;
            this.pills.children().removeClass('active');
            if(grouping) {
                this.pills.find('#grouped').addClass('active');
            } else {
                this.pills.find('#raw').addClass('active');
            }
            var fileHistorySelector = 'div.filename,div.checksum';
            if(this.grouping === false) {
                OCAudit_log.InfinitScrolling.container.on('click',fileHistorySelector,function(e){
                    var t = $(e.currentTarget),
                    row = t.parents('tr:first'),
                    filename = row.find('.filename').text(),
                    checksum = row.find('.checksum').text();
                    OCAudit_log.Filter.setFilter('fileHistory');
                    OCAudit_log.Filter.setSearchOption({'fileName':filename,'checksum':checksum});
                });
            } else {
                OCAudit_log.InfinitScrolling.container.off('click',fileHistorySelector);
            }
        },
        setSearchOption: function(valueObj){
            this.queryStr = [];
            this.setGrouping(false);
            var str = [];
            if($.type(valueObj) !== 'object') {
                var dateRange = this.searchFrm.find('.dateRange input').val().split(' ~ ');
                if($.type(dateRange)==='array' && dateRange.length>1) {
                    str['stdDate'] = dateRange[0];
                    str['endDate'] = dateRange[1];
                }
                str['os'] = $.makeArray(this.searchFrm.find('input[name=os]:checked').map(function(idx,elem){return $(elem).val();})).join('+');
                str['device'] = $.makeArray(this.searchFrm.find('select[name=device] option:selected').map(function(idx,elem){return $(elem).val();})).join('+');
                str['userIP'] = this.searchFrm.find('input[name=userIP]').val();
                str['user'] = this.searchFrm.find('input[name=user]').val();
                str['fileName'] = this.searchFrm.find('input[name=fileName]').val();
                str['checksum'] = this.searchFrm.find('input[name=checksum]').val();
            } else {
                str = valueObj;
            }

            for(var key in str) {
                if($.type(str[key]) === 'string' && str[key] !== '')
                    this.queryStr.push(key + '=' + str[key]);
            }
            this.initialize();
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
                        } else if (OCAudit_log.Filter.currentPage == 1) {
                        // First page is empty - No activities :(
                            $('#no_activities').removeClass('hidden');
                            $('#loading_activities').addClass('hidden');
                        } else {
                        // Page is empty - No more activities :(
                            $('#no_more_activities').removeClass('hidden');
                            $('#loading_activities').addClass('hidden');
                        }
                    });
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
            if(OCAudit_log.Filter.grouping){
                $(content).find('div.filename,div.checksum').css('cursor','default');
            } else {
                $(content).find('div.filename,div.checksum').css('cursor','pointer');
            }
            return content;
        }
    };
    $('body').tooltip({
        html:true,
        selector:'[data-toggle="tooltip"]'
    });
    OCAudit_log.Filter.setFilter(OCAudit_log.InfinitScrolling.container.attr('data-activity-filter'));
    OCAudit_log.Filter.initialize();
    OCAudit_log.InfinitScrolling.content.on('scroll', OCAudit_log.InfinitScrolling.onScroll);

    OCAudit_log.Filter.navigation.find('a[data-navigation]').on('click', function (event) {
        OCAudit_log.Filter.setFilter($(this).attr('data-navigation'));
        OCAudit_log.Filter.initialize();
        event.preventDefault();
    });
    OCAudit_log.Filter.pills.find('.btn').on('click',function(e) {
        OCAudit_log.Filter.setGrouping($(this).prop('id')==='grouped');
        OCAudit_log.Filter.initialize(true);
    });
    //목록 버튼
    OCAudit_log.Filter.pills.parent().on('click','#list',function(e){
        OCAudit_log.Filter.setFilter('all');
        OCAudit_log.Filter.initialize(true);
    });
    //사용자,아이피 히스토리 이동
    OCAudit_log.InfinitScrolling.container.on('click','.username,.userip',function(e){
        var t = $(e.currentTarget),
        row = t.parents('tr:first'),
        searchKey = t.data('key'),
        searchVal = t.data('val'),
        searchObj = {};
        searchObj[searchKey] = searchVal;
        if(searchKey==='user') {
            OCAudit_log.Filter.setFilter('userHistory');
        } else if(searchKey==='userIP') {
            OCAudit_log.Filter.setFilter('ipHistory');
        }

        OCAudit_log.Filter.setSearchOption(searchObj);
    });
    $.OCAudit_log = OCAudit_log;
});
