$(document).ready(function() {
	if(! $('#mutter_display').length) {
		return false;
	}
	
	var socket = io.connect('http://muttery.rares.webicks.com:8080',{'username':'cucu'});
	
	socket.on('msg', function(data) {
		var msg = JSON.parse(data);
		appendMsg(msg);
	});
	
	socket.on('disconnect', function(res){
		console.log('connection dropped');
		console.log(res);
		if(res == 'booted') {
			//@todo: handle disconnects nicely (you are not allowed to connect if you've been booted!)
			console.log('connection was terminated remotely');
		}
	});
	
	socket.on('authChallenge', function(challenge) {
		console.log(challenge);
		FB.getLoginStatus(function(res){
			console.log(res);
			if(res.status != 'connected') {
				//Handly fb connection here!
				socket.disconnect();
				return false;
			}
			
			response = {
					'id': res.authResponse.userID,
					'accessToken': FB.getAccessToken()
			};
			
			socket.emit('authResponse', JSON.stringify(response));
		});
	});
	
	socket.on('init', function(data) {
		var messages = JSON.parse(data);
		for (i in messages) {
			appendMsg(messages[i]);
		}
	});
	
	function appendMsg(msg) {
		$('#msgs').append(
				function() {
					var div = $('<div>');
					var span = $('<span>');
					div.html('<b>' + msg.username + ':</b> '
							+ span.text((msg.message)).html());
					return div;
				});
		$('#msgs')[0].scrollTop = $('#msgs')[0].scrollHeight;
	}
	
	$('#chatForm').submit(function(){
		var msg = {'message': $('#chatMsg').val()};
		$("#chatMsg").val("");
		socket.emit('msg', JSON.stringify(msg));
		msg.username = userName;
		appendMsg(msg);
		return false;
	});
	
	
});
