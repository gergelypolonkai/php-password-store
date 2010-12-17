$(document).ready(function() {
	$('#username').tooltip({
		bodyHandler: function() {
			return 'JUJ!';
		},
		track: true,
		delay: 0,
	});
});
