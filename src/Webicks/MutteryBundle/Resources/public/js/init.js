$(document).ready(function() {
	$(document).bind('fbInit', function(){
		if (typeof (FB) != 'undefined' && FB != null) {
			FB.Event.subscribe('auth.statusChange', function(response) {
				if (!user || !user.groups || user.groups.indexOf('ROLE_FACEBOOK') == -1) {
					if ((response.session || response.authResponse)) {
						console.log("Redirecting to confirm login!");
						setTimeout(function(){
							window.location.href = "/login_check";
						}, 1000);
					} else {
						console.log("Redirecting to logout!");
						window.location.href = "/logout";
					}
				} else {
					if (response.authResponse) { 
						user.authResponse = response.authResponse;
					} else {
						user.authResponse = null;
					}
				}
			});

			//Update user.authReponse in case it's not there, we need the accessToken to call FB stuff...
			if (! user.authResponse) {
				FB.getLoginStatus();
			}
			
			FB.Event.subscribe('auth.logout', function(response) {
				window.location.href = "/";
			});
		}
	});
});
