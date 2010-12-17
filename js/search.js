var max_search_results = 8;
var groups_visible = true;
var passwords_visible = false;

function pwitem_update() {
	$('#error').html('');
	$('#passwordinfo').html('Fetching...');

	$.post('getpw.php', { id: $(this).attr('id').replace(/^pw_/, '') }, function(pwxml) {
		pwinfo = $('result', pwxml);
		if (pwinfo.length > 0)
		{
			$('#passwordinfo').html(
				'<ul>' +
				'<li>Short description: ' + $('short', pwinfo).text() + '</li>' +
				'<li>Long description: ' + $('long', pwinfo).text() + '</li>' +
				'<li>Username: ' + $('username', pwinfo).text() + '</li>' +
				'<li>Password: ' + $('password', pwinfo).text() + '</li>' +
				'<li>Additional information: ' + $('additional', pwinfo).text() + '</li>' +
				'</ul>'
			);
		}
	});
};

$(document).ready(function() {
	$('#passwords').hide();

	$('#query').keyup(function(e) {
		if ($('#query').val().length > 2)
		{
			$('#error').html('');
			$('#results').html('Searching...');
			$('#passwordgroups').hide();
			$('#passwords').hide();

			output = '';

			$.post('results.php', { querytext: $('#query').val() }, function(listxml) {
				results = $('row', listxml);
				$('#info').html('Search for "' + $('query', listxml).text() + '" ready after ' + $('elapsed-time', listxml).text() + ' seconds. ' + results.length + ' records found' + ((results.length > max_search_results) ? ' (top ' + max_search_results + ' shown)' : '') + '.');
				if (results.length > 0)
				{
					output += '<ul>';
					for (i = 0; i < results.length; i++)
					{
						output += '<li class="pwitem" id="pw_' + $('id', results[i]).text() + '">' + $('short', results[i]).text() + '</li>';
						if (i == max_search_results - 1)
						{
							break;
						}
					}
					output += '</ul>';
				}

				$('#results').html(output);

				$('.pwitem').click(pwitem_update);
			});
		}
		else
		{
			$('#error').html('The search string must be at least 3 characters long to search');
			$('#results').html('');
			$('#info').html('');
			if (groups_visible)
			{
				$('#passwordgroups').show();
			}
			else if (passwords_visible)
			{
				$('#passwords').show();
			}
		}
	});

	$('#clearsearch').click(function(e) {
		$('#error').html('');
		$('#info').html('');
		$('#results').html('');
		$('#passwordinfo').html('');
		$('#query').val('');
		if (groups_visible)
		{
			$('#passwordgroups').show();
		}
		else if (passwords_visible)
		{
			$('#passwords').show();
		}
	});

	pwgnames = $('.pwgname');
	for (a = 0; a < pwgnames.length; a++)
	{
		$.post('getpwgname.php', { name: pwgnames[a].id.replace(/^pwgname_/, '') }, function (pwgxml) {
			name = $('name', pwgxml).text();
			$('#pwgname_' + name).html($('description', pwgxml).text());
		});
	}

	$('.pwgname').click(function(e) {
		pwgid = this.id.replace(/^pwgname_/, '');
		$('#openedgroupname').html(pwgid);
		$('#passwordlist').html('Fetching, please wait...');

		$.post('getpwgname.php', { name: pwgid }, function (pwgxml) {
			$('#openedgroupname').html($('description', pwgxml).text());
		});

		$.post('getpasswordlist.php', { group: pwgid }, function (listxml) {
			results = $('row', listxml);
			if (results.length > 0)
			{
				output = '<ul>';
				for (i = 0; i < results.length; i++)
				{
					output += '<li class="pwitem" id="pw_' + $('id', results[i]).text() + '">' + $('short', results[i]).text() + '</li>';
				}
				output += '</ul>';
			}

			$('#passwordlist').html(output);
			$('.pwitem').click(pwitem_update);
		});

		$('#passwordgroups').hide();
		$('#passwords').show();
	});

	$('#showpasswordgroups').click(function(e) {
		$('#passwordgroups').show();
		$('#passwords').hide();
		$('#passwordinfo').html('');
	});
});

