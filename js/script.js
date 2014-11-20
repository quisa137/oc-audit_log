$(function(){
	var OCAudit_log={};

	OCAudit_log.Filter = {
		filter: undefined,
		currentPage: 0,
		navigation: $('#app-navigation'),

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
					'filter=' + OCAudit_log.Filter.filter + '&page=' + OCAudit_log.Filter.currentPage,
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
					'filter=' + OCAudit_log.Filter.filter + '&page=' + OCAudit_log.Filter.currentPage,
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

				// Move content into the last box
				OCAudit_log.InfinitScrolling.processElements(appendedBoxes);
				lastBoxContainer.append(appendedBoxes);

				// Remove the first box, so it's not duplicated
				content = $(content).slice(1);
			} else {
				content = $(content);
			}

			OCAudit_log.InfinitScrolling.processElements(content);
			this.container.append(content);
		},

		processElements: function (parentElement) {
			$(parentElement).find('.avatar').each(function() {
				var element = $(this);
				element.avatar(element.data('user'), 28);
			});

			$(parentElement).find('.tooltip').tipsy({
				gravity:	's',
				fade:		true
			});
		}
	};

	OCAudit_log.Filter.setFilter(OCAudit_log.InfinitScrolling.container.attr('data-activity-filter'));
	OCAudit_log.InfinitScrolling.content.on('scroll', OCAudit_log.InfinitScrolling.onScroll);

	OCAudit_log.Filter.navigation.find('a[data-navigation]').on('click', function (event) {
		OCAudit_log.Filter.setFilter($(this).attr('data-navigation'));
		event.preventDefault();
	});

	$('#enable_rss').change(function () {
		if (this.checked) {
			$('#rssurl').removeClass('hidden');
		} else {
			$('#rssurl').addClass('hidden');
		}
		$.post(OC.filePath('audit_log', 'ajax', 'rssfeed.php'), 'enable=' + this.checked, function(data) {
			$('#rssurl').val(data.data.rsslink);
		});
	});

	$('#rssurl').on('click', function () {
		$('#rssurl').select();
	});
});

