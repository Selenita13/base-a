var theToken = 'not_set';  

function onPushwooshInitialized(pushNotification) {

    //if you need push token at a later time you can always get it from Pushwoosh plugin
    pushNotification.getPushToken(function(token) {
      console.info('push token: ' + token);
    }
    );
    
    //and HWID if you want to communicate with Pushwoosh API
    pushNotification.getPushwooshHWID(function(token) {
      console.info('Pushwoosh HWID: ' + token);
    }
    );
    
    //settings tags
    pushNotification.setTags({
      tagName: "tagValue",
      intTagName: 10
    },
    function(status) {
     console.log('setTags success: ' + JSON.stringify(status));
   },
   function(status) {
     console.log('setTags failed');
   }
   );
    
    pushNotification.getTags(function(status) {
       console.log('getTags success: ' + JSON.stringify(status));
     },
     function(status) {
       console.log('getTags failed');
     }
     );
    
    //start geo tracking.
    //pushNotification.startLocationTracking();
  }

  function initPushwoosh() {
    var pushNotification = cordova.require("pushwoosh-cordova-plugin.PushNotification");
    
    //set push notifications handler
    document.addEventListener('push-notification',
      function(event) {
        var message = event.notification.message;
        var userData = event.notification.userdata;
		console.log(message + " | " + userData);
	  //dump custom data to the console if it exists
	  if (typeof(userData) != "undefined") {
		console.warn('user data: ' + JSON.stringify(userData));
	  }
	}
	);
    
    //initialize Pushwoosh with projectid: "GOOGLE_PROJECT_ID", appid : "PUSHWOOSH_APP_ID". This will trigger all pending push notifications on start.
    pushNotification.onDeviceReady({
    	projectid: "85579554030",
     	appid: "EBDBB-A3F53",
     	serviceName: "upgradeapp"
   	});
	  
	  
    //register for push notifications
    pushNotification.registerDevice(
      function(status) {
		  
		theToken = status.pushToken; 
        //document.getElementById("pushToken").innerHTML = status.pushToken + "<p>";
        onPushwooshInitialized(pushNotification);
		  //console.log(status.pushToken);
      },
      function(status) {
        console.log("failed to register: " + status);
        console.warn(JSON.stringify(['failed to register ', status]));
      }
      );
  }

  var app = {
    // Application Constructor
    initialize: function() {
      this.bindEvents();
    },
    // Bind Event Listeners
    //
    // Bind any events that are required on startup. Common events are:
    // 'load', 'deviceready', 'offline', and 'online'.
    bindEvents: function() {
      document.addEventListener('deviceready', this.onDeviceReady, false);
    },
    // deviceready Event Handler
    //
    // The scope of 'this' is the event. In order to call the 'receivedEvent'
    // function, we must explicity call 'app.receivedEvent(...);'
    onDeviceReady: function() {
      
	// PUSHWOOSH	
	initPushwoosh();
    app.receivedEvent('deviceready');
		
	
	// GOOGLE ANALYTICS
	((!appDebug) ? window.analytics.startTrackerWithId('UA-116919639-1') : '');
	((!appDebug) ? window.analytics.trackView('Principal') : '');
		
	console.log(StatusBar);
	StatusBar.hide();	
	
	/*universalLinks.subscribe('ul_feedEvent', function (eventData) {
		// do some work
		// in eventData you'll see url и and parsed url with schema, host, path and arguments
		console.log('Did launch application from the link: ' + JSON.stringify(eventData));
		alert('Did launch application from the link: ' + JSON.stringify(eventData));
	});		
	console.log('universal');
	*/
		
    },
    // Update DOM on a Received Event
    receivedEvent: function(id) {
      "use strict";
		/*var parentElement = document.getElementById(id);
      	var listeningElement = parentElement.querySelector('.listening');
      	var receivedElement = parentElement.querySelector('.received');

      	listeningElement.setAttribute('style', 'display:none;');
      	receivedElement.setAttribute('style', 'display:block;');
			*/
      	console.log('Received Event: ' + id);
    }
  };

  app.initialize();

	$(document).on('click','#profile',function(){
		console.log(theToken);
	});