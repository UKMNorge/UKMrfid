/* 
 * Auth.js er et API som registrerer enheten for bruk som RFID-boks,
 * og gir tilbakemelding til nettsiden når den er registrert som OK.
 * 
 * Lagrer en tilfeldig generert ID i local storage, slik at boksen kan 
 * restartes uten behov for ny godkjenning. 
 *
 * All lagring er separert i funksjoner så vi enkelt kan bytte til f.eks Cookies.
 *
 */
var Auth = function ( ) {

	var APIurl = "/api/";

	var GUID = null;

	var self = {
		/**
		 * Registrerer enheten mot serveren. Kjører hver oppstart.
		 *
		 */
		register: function(registeredCallback) {
			// Sjekk om vi har lagret en GUID.
			if ( null == self.getGUID() ) {
				self.generateGUID();
			} 
			
			// Send registrering til serveren.
			var data = {'endpoint': 'registerStation'};
			// Kjør RegisterCallback med dataene vi får tilbake.
			self.poll(data, registeredCallback);
		},
		/**
		 * Sjekker at enheten er godkjent og kan sende 
		 * registreringer med sin unike GUID.
		 */
		verifyStation: function(callback) {
			var data = {'endpoint': 'verifyStation'};
			self.poll(data, callback);
		},

		scan: function(value, callback) {
			var data= {'endpoint': 'scan', 'rfidValue': value};
			self.post(data, callback);
		},

		/**
		 * Hent GUID fra local storage.
		 *
		 */
		getGUID: function() {
			return self.GUID;
		},

		deleteGUID: function() {
			self.GUID = null;
		},

		/**
		 * Generer en tilfeldig GUID.
		 *
		 */
		generateGUID: function() {
			function s4() {
			    return Math.floor((1 + Math.random()) * 0x10000)
			    	.toString(16)
			    	.substring(1);
				}
			var guid = s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
			
			self.GUID = guid;
			console.log("Satt ny GUID for denne maskinen: " + guid);
		},

		/**
		 * Gjør et ajax-kall mot backend, ferdig linket opp mot URL og GUID.
		 * 
		 * Behøver kun riktig data i et sett med key-value pairs.
		 */
		post: function(postData, callback) {
			postData['guid'] = self.getGUID();
			// Add hash dersom dette er en scann
			if( null != postData['rfidValue'] ) {
				postData['hash'] = sha1(postData['rfidValue'] + self.getGUID());
			}
			$.post({
				url: APIurl,
				data: postData,
				success: callback,
				dataType: 'json'
			});
		},

		poll: function(postData, callback) {
			console.log('Polling '+ postData.endpoint +'...');
			var activePolling = true;
			postData['guid'] = self.getGUID();
			$.post({
				url: APIurl,
				data: postData,
				success: function(data) {
					if(data.success)
						activePolling = false;
					callback(data);
				},
				dataType: 'json',
				complete: function(){
					if(activePolling)
						setTimeout(function() {self.poll(postData, callback)}, 3000);	
				},
        		timeout: 1000
        	});
		}
	};

	return self;
}

