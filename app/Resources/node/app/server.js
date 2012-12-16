var DEBUG = true;
var PORT = 8080;
var adminPORT = 8088;
var INIT_MESSAGES = 5;

var http = require('http');
var url = require('url');

var authorizedClients = {};

var adminServer = http.createServer(function(req,res){
	req.setEncoding('utf8');
	myUrl = url.parse(req.url).path;

	//Get the body
	var body = '';
	req.on('data', function(chunk){
		body += chunk;
	});
	
	var handlers = {
			'/test': function(req, data, res) {
				return "test";
			},
			'/push': function(req, data, res) {
				var data = JSON.parse(data);

				if(authorizedClients[data.id] == undefined) {
					authorizedClients[data.id] = {}
				}

				authorizedClients[data.id] = data;
				console.log("Authorization completed");
				return 'OK!'
			}
	}

	req.on('end', function(){
		if (!(myUrl in handlers)) {
			res.writeHead(404, {'Content-Type': 'text/plain'});
			res.end('Action not found!');
		}

		try {
			var ret = handlers[myUrl](req, body, res);
			if (ret){
				res.writeHead(200, {'Content-Type': 'text/plain'});
				res.end(ret);
			}
		} catch (e) {
			// TODO: handle exception
			res.writeHead(500, {'Content-Type': 'text/plain'});
			res.end(JSON.stringify(e));
		}
	});
}).listen(adminPORT);

var server = http.createServer().listen(PORT);
var io = require('../lib/socket.io').listen(server);
io.set ('transports', ['websocket', 'xhr-polling', 'jsonp-polling']);

var messages = new Array();

Array.prototype.inject = function(element) {
    if (this.length >= INIT_MESSAGES) {
        this.shift();
    }
    this.push(element);
}

io.sockets.on('connection', function(client) {

    if (DEBUG) {
    	console.log(client.id);
    }

    client.emit("authChallenge", 'are you high?!');
    client.on("authResponse", function(res){
    	try {
			response = JSON.parse(res);
			console.log(response);
			
			if( response.id in authorizedClients ){
				if(authorizedClients[response.id].accessToken == response.accessToken) {
					client.fbID = response.id;
					client.emit("init", JSON.stringify(messages));
				}
			} else {
				console.log('DROP UNAUTHORIZED CONNECTION');
				client.disconnect('UNAUTHORIZED');
			}
    	} catch (e) {
			// TODO: handle exception
    		console.log("ECEPTION EXCEPTION");
    		console.log(e);
    		client.disconnect('EXCEPTION');
		}
    })

    client.on('msg', function(msg) {
    	if(! ('fbID' in client)) {
    		//Unauthorized clients should not say anything!
    		return false;
    	}
    	
    	var message = JSON.parse(msg);
    	console.log(message);

    	//Logging
        messages.inject(message);
        
        //Decorating
        message['username'] = authorizedClients[client.fbID].first_name;
        client.broadcast.emit('msg', JSON.stringify(message));
    })

    client.on('disconnect', function() {
        if (DEBUG)
            console.log("Disconnected: ", client.id);
    });
});