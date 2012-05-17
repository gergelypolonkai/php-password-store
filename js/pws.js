$(document).ready(function() {
	$('#username').tooltip({
		bodyHandler: function() {
			return 'User information will go here';
		},
		track: true,
		delay: 0,
	});
});
