$(document).ready(function() {

	//Do not run this unless there is a create-mutter box
	if (! $('#create-mutter')) {
		return false;
	}
	
	
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
			if(!response.authResponse) {
				return false;
			}

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
	
	//Toggle Mutter Name in and out of the field, this is sexy!
	$('#mutter-name').focus(function(self){
		target = $(self.target);
		if(target.val() == target.attr('rel')) {
			target.val('');
		}
	})
	
	$('#mutter-name').blur(function(self){
		target = $(self.target);
		if(target.val() == '') {
			target.val(target.attr('rel'));
		}
	})
	
	var mindate = false;
	$('#add-start-date').click(function(){
	    $( "#date-from" ).datetimepicker({
	        defaultDate: 0,
	        changeMonth: true,
	        changeYear: true,
	        minDate: 0,
	        onClose: function( selectedDate ) {
	        	var newDate=new Date(selectedDate);
	        	newDate.setDate(newDate.getDate()+1);
	        	mindate = newDate;
	        }
	    });
	    $('#add-start-date').hide();
	    $('#date-from').addClass('active');
	});
    
	$('#add-end-date').click(function(){
	    $( "#date-until" ).datetimepicker({
	        defaultDate: 0,
	        changeMonth: true,
	        changeYear: true,
	        minDate: +1,
	        onClose: function( selectedDate ) {
	            $( "#date-from" ).datepicker( "option", "maxDate", selectedDate );
	        }
	    });
	    if(mindate) {
	    	$( "#date-until" ).datepicker( "option", "minDate", mindate );
	    }
	    
	    $('#add-end-date').hide();
	    $('#date-until').addClass('active');
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
