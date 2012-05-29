// note, io.listen(<port>) will create a http server for you
var io = require('socket.io').listen(8080);
var nick = "";
var socketList = [];
var counter = 0;
var userList = [];

io.sockets.on('connection', function (socket) {

	socket.on('signIn', function (name) {
		for(var i=0;i<counter;i++) {
			if(userList[i] == name) {
				socket.emit('error', "Username already taken!");
				return;
			}
		}
		userList[counter] = name;		
		console.log("User Added : "+name);
		socket.emit('ready', counter, userList);
		for(var i=0;i<counter;i++)
			socketList[i].emit('addPeer', i, userList);		
		socketList[counter] = socket;
		counter++;
		console.log("Users added!");
	});
	
	socket.on('sendMessage', function (from, to, content) {
		console.log(content);
		socketList[to].emit('receiveMessage',from, content);
		console.log("Message Sent!");
	});
	
	socket.on('reject', function (myId, remoteId) {
		socketList[remoteId].send(from, 'REJECT', userList[from]);
	});
	
	socket.on('disconnect', function () {
		for(var i=0;i<socketList.length;i++)
			if(socket==socketList[i]) { 
				userList[i]=-1; 
				console.log(i+"th User Deleted!"); 
				for(var j=0;j<counter;j++) {
					if(userList[j]!=-1)
						socketList[j].emit('addPeer', j, userList);
				}
				break; 
			}
	});

});