$(document).ready(function() {
	if(! $('#mutter_display').length) {
		return false;
	}
	
	if(currentMutter.startTime.getTime() < (new Date()).getTime()){
		$('#mutter_action').load('/mutterAction/' + currentMutter.id, function(){
			console.log('done');
		});
		$('#mutter_action').dialog({
			 autoOpen: true,
			 width: 850,
	         modal: true,     
	         title: "Action!",
		}, function(){
			
			console.log('dialog opened');
			});
	} else {
		$('#mutter_countdown').dialog({
			 autoOpen: true,
			 width: 850,
	         modal: true,     
	         title: "Countdown!",
		}, function(){console.log('dialog opened');});
		
		console.log(currentMutter);
		
		var diff = Math.round((currentMutter.startTime.getTime() - (new Date()).getTime())/1000);
		
		var interval = setInterval(function(){
			if (diff == 0) {
				clearInterval(interval);
				$('#mutter_countdown').dialog('close');
				$('#mutter_action').load('/mutterAction/' + currentMutter.id, 
				function(){
					console.log('done');
				});
				$('#mutter_action').dialog({
					 autoOpen: true,
					 width: 850,
			         modal: true,     
			         title: "Action!",
				}, function(){
					console.log('dialog opened');
					});
			}
			diff--;
			
			var text = '';
			
			text = (diff/86400 > 1) ? Math.floor(diff/86400) +' days, ' : '';
			text += ((diff%86400) >= 3600) ? Math.floor((diff%86400)/3600) + ' hours, ' : '';
			text += ((diff%3600) >= 60) ? Math.floor((diff%3600)/60) + ' minutes and ' : '';
			text += ((diff%60) >= 0) ? (diff%60) + ' seconds.' : '';
			$('#mutter_countdown').text(text);
		}, 1000);
	}
});