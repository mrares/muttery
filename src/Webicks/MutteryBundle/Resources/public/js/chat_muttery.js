var socket = io.connect('http://muttery.marius.webicks.com:8080');

socket.on('msg', function(data) {
	var msg = JSON.parse(data);
	appendMsg(msg);
});



socket.on('init', function(data) {
	var messages = JSON.parse(data)
	for (i in messages)
		appendMsg(messages[i])
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

function sendMsg() {
	var msg = {};
	msg['username'] = userName;
	msg['message'] = $('#msg').val();
	$("#msg").val("");
	appendMsg(msg);
	socket.emit('msg', JSON.stringify(msg));
}	