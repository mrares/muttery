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
	
	$("#close-mutter").click(function() {
		$("#create-mutter").dialog('close');
	});
	
	$("#open-new-mutter").click(function() {
		$("#create-mutter").dialog('open');
	});

	$('[name="mutter-type"]').change(function(ev){
		$('.create-action').removeClass('active');
		$('.create-' + $(ev.target).val()).addClass('active');
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
				from: $('#date-from').val(),
				until: $('#date-until').val(), 
				data: mutterActionData,
				invites: []
		};
		$("#create-mutter").dialog('close');
		
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
	
	$('#create-mutter').dialog({
		 autoOpen: false,
		 width: 730,
         modal: true,
	});
		
    $( "#date-from" ).datetimepicker({
        defaultDate: 0,
        changeMonth: true,
        changeYear: true,
        minDate: 0,
        onClose: function( selectedDate ) {
        	var newDate=new Date(selectedDate);
        	newDate.setDate(newDate.getDate()+1);
            $( "#date-until" ).datepicker( "option", "minDate", newDate );
        }
    });
    
    $( "#date-until" ).datetimepicker({
        defaultDate: 0,
        changeMonth: true,
        changeYear: true,
        minDate: +1,
        onClose: function( selectedDate ) {
            $( "#date-from" ).datepicker( "option", "maxDate", selectedDate );
        }
    });

	$('#ytLoginAction').click(function(){
		var windowDest = $('#GAuth_win').text();
		if(windowDest) {
			loginWindow = window.open(windowDest, "YTLogin", 'width=500');
		}
	});
	
	$(document).bind('YTLoggedin', function(param){
		$('#createActionYoutube').mask('Loading');
		$('#createActionYoutube').load('/ytUpload', function(){
			$('#createActionYoutube').unmask();
		});
	});
});
