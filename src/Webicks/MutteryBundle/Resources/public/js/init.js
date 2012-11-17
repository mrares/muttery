$(document).ready(function() {
	$(document).bind('fbInit', function(){
		console.log("FbInit called!");
		if (typeof (FB) != 'undefined' && FB != null) {
			console.log("FB is defined!");
			
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
			
			if (user && user.groups) {
				if (user.groups.indexOf('ROLE_FACEBOOK')!=-1 && FB.getUserID() == user.userid) {
					$("#create-mutter").overlay().load();
				}
			}
		}
	});
	
	$("#close-mutter").click(function() {
		$("#create-mutter").overlay().close();
	});

	$("#invite-friends").click(function(){
		$("#create-mutter").overlay().close();
		
		FB.getLoginStatus(function(response){
			FB.ui({
				method: 'apprequests',
				display: "iframe",
				access_token: user.authResponse.accessToken,
				message: 'Invitation to Muttery!'
			}, function(res){console.log(res);});
		});
		
	});
	
	// select the overlay element - and "make it an overlay"
	$("#create-mutter").overlay({
		// custom top position
		top : 260,

		// some mask tweaks suitable for facebox-looking dialogs
		mask : {
			// you might also consider a "transparent" color for the mask
			color : '#fff',

			// load mask a little faster
			loadSpeed : 200,

			// very transparent
			opacity : 0.5
		},

		// disable this for modal dialog-type of overlays
		closeOnClick : false,

		// load it immediately after the construction
		load : false
	});

});
