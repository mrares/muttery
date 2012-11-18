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

	$('[name="mutter-type"]').change(function(ev){
		
		active = $(ev.target);
		console.log(active.val());
		
		$('.create-action').removeClass('active');
		$('.create-' + active.val()).addClass('active');
		
	});
	
	$("#invite-friends").click(function(){
		
		var mutterActionData = [];
		mutterType = $('[name="mutter-type"]:checked').val();
		
		if (mutterType == "message") {
			mutterActionData = $('#mutter-data-message').val();
		} else if (mutterType == "youtube") {
			mutterActionData = "YOUTUBE";
		} else if (mutterType == "redirect") {
			mutterActionData = $('#mutter-data-redirect').val();
		}
		
		var MutterData = {
				name: $('#mutter-name').val(),
				type: mutterType,
				data: mutterActionData,
				invites: []
		};
		$("#create-mutter").overlay().close();
		
		FB.getLoginStatus(function(response){
			FB.ui({
				method: 'apprequests',
				display: "iframe",
				access_token: user.authResponse.accessToken,
				message: 'Invitation to Muttery!'
			}, function(res){
					MutterData.invites = res.to;
					
					$.ajax(
						'http://' + document.domain + '/saveMutter',
						{
						type: "POST",
						data: {data: MutterData},
						dataType: 'json',
						success: function(){
							console.log("SUCCESS!");
						},
						error: function(){
							console.log("REQUEST FAILED! ");
						}
					});
				});
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
