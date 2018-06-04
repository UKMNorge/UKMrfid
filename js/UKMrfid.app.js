var GUI = function( $, _class ){
	var CLASS = _class +'View';
	var sounds = new Map();
	var current = null;
	
	var self = {
		
		fullscreen: function() {
			var el = document.documentElement,
				rfs = el.requestFullscreen
				|| el.webkitRequestFullScreen
				|| el.mozRequestFullScreen
				|| el.msRequestFullscreen 
			;
			
			rfs.call(el);
		},
		
		showView: function( view ) {
			current = view;
			console.log('showView: '+ view);
			console.log('showView'+ view + ' => .hide(.'+ CLASS +')');

			$('.'+ CLASS).hide();
			$('.'+ CLASS +'#view'+ view).fadeIn(200);
		},
		
		getActiveView: function() {
			return current;
		},
		
		setHTML: function( selector, value ) {
			console.log('setHTML', '.'+ CLASS +' '+ selector, value);
			$('.'+ CLASS +' '+ selector).html( value );
		},
		
		playSound: function( sound ) {
			var audio = document.getElementById( 'audiofile_'+ sound );
			if (audio.paused) {
				audio.play();
			}else{
				audio.currentTime = 0
			}
		},
	};
	
	return self;
};

var ScannerApp = function( GUI, Auth) {
	
	var self = {
		init: function() {
			GUI.showView('Start');
			Auth.register( self.registerHandler );
		},
		
		registerHandler: function( response ) {
			console.log("Registration sent");
			console.log(response);
			if (response.success) {
				GUI.showView('RegisteredSuccess');
				$('#stationCode').html( Auth.getGUID() );
				self.startPolling();
			}
			else {
				GUI.showView('RegisteredFail');
				$('#registrationError').html(response.message);
			}
		},
		
		doScan: function( rfidValue ) {
			console.log("Scanned: "+rfidValue);
			$("#rfidValueForm")[0].reset();
			
			GUI.showView('PleaseHold');
			
			Auth.scan(rfidValue, function(response) {
				console.log(response.data);
				if(response.success) {
					if( response.direction == 'in' ) {
						self.successIn( response );
					} else {
						self.successOut( response );
					}

					GUI.showView('BeepedGreen');
					self.goToReadyIn( 1000 );
				}
				else {
					GUI.playSound('error');
					GUI.showView('BeepedRed');
					self.goToReadyIn( 4000 );
				}
			});
		},
		
		goToReadyIn: function( milliseconds ) {
			setTimeout(function() {
				GUI.showView('ReadyForBeeping');
				$("#rfidValue").focus();
			}, milliseconds);
		},
		
		successIn: function( response ) {
			switch( response.herd_foreign_id ) {
				case 'UKM-festivalutvikler':
					$('#welcomeName').html('Husk å puste '+ response.person +'!');
					break;
				case 'landsbyhovding':
					$('#welcomeName').html('Hei sjef!');
					break;
				default:
					$('#welcomeName').html('Velkommen hjem, '+ response.person +'!');
					break;
			}
			GUI.playSound('success');
		},
		
		successOut: function( response ) {
			switch( response.herd_foreign_id ) {
				case 'UKM-festivalutvikler':
					$('#welcomeName').html('Have fun out there '+ response.person +'!');
					break;
				case 'UKM-hovding':
					$('#welcomeName').html('Be back soon!');
					break;
				default:
					$('#welcomeName').html('God tur, '+ response.person +'!');
					break;
			}
			GUI.playSound('error');
		},
		
		startPolling: function() {
			Auth.verifyStation( self.verifyStationHandler );
		},
		
		verifyStationHandler: function(response) {
			if (response.success) {
				self.readyToScan();
			} else {
				console.log('  => Stasjon ikke godkjent.');
				if( GUI.getActiveView() !== 'RegisteredSuccess' ) {
					GUI.showView('RegisteredSuccess');
				}
			}
		},
		
		readyToScan: function() {
			console.log("Stasjon godkjent, klar for bruk");
			GUI.showView('ReadyForBeeping');
			$('#rfidValue').focus();
		}
		
	}
	//self.init();
	
	return self;
	
}( new GUI( jQuery, 'UKMrfid'), new Auth() );


// Start app @ pageload
$(document).ready(function(){
	ScannerApp.init();
});

// Alle taps på skjermen burde sette fokus til input
$(document).on('click touch', function() {
	$('#rfidValue').focus();
});

// Scann et armbånd!
$(document).on('submit', '#rfidValueForm', function(e) {
	e.preventDefault();
	console.warn('Hola!');
	ScannerApp.doScan( $("#rfidValue").val() );
	return false;
});