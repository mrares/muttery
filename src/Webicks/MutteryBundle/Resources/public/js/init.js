$(document).ready(function() {

	$("#close-mutter").click(function() {
		$("#create-mutter").overlay().close();
	})

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

function goLogIn() {
	window.location.href = "/login_check";
}

function onFbInit() {
	if (typeof (FB) != 'undefined' && FB != null) {
		FB.Event.subscribe('auth.statusChange', function(response) {
			if (response.session || response.authResponse) {
				setTimeout(goLogIn, 500);
			} else {
				window.location.href = "{{ path('_security_logout') }}";
			}
		});
		
//		FB.Event.subscribe('auth.authResponseChange', function(response) {
//			console.log(response);
//		});

//		FB.Event.subscribe('auth.login', function(response) {
//			console.log(response);
//		});

		FB.Event.subscribe('auth.logout', function(response) {
			window.location.href = "/";
		});

		setTimeout(function(){
			userid = FB.getUserID();
			if(userid) {
				$("#create-mutter").overlay().load();
			}
		},500);
		
	}
}