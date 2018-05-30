var GUI = function( $, _class ){
	var CLASS = _class +'View';
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
			console.log('showView: '+ view);
			$('.'+ CLASS).fadeOut(200, function(){
				$('.'+ CLASS +'#view'+ view).fadeIn(200);
			});
		},
		
		setHTML: function( selector, value ) {
			console.log('setHTML', '.'+ CLASS +' '+ selector, value);
			$('.'+ CLASS +' '+ selector).html( value );
		},
	};
	
	return self;
};

/**
 * APP -> GUI HOOKS
**/
UKMrfidGUI = new GUI( jQuery, 'UKMrfid');
Auth = null;

$(document).ready(function(){
	UKMrfidGUI.showView('Start');
	Auth = new Auth();
	Auth.register(function(response) {
		console.log("Registration sent");
		console.log(response);
		if (response.success) {
			UKMrfidGUI.showView('RegisteredSuccess');
			$('#stationCode').html(Auth.getGUID());
			startPolling();
		}
		else {
			UKMrfidGUI.showView('RegisteredFail');
			$('#registrationError').html(response.message);
		}
	});
});

// Polling for updated status
function startPolling() {
	Auth.verifyStation(function(response) {
		if (response.success) {
			console.log("Stasjon godkjent, klar for bruk");
			UKMrfidGUI.showView('ReadyForBeeping');
			$('#rfidValue').focus();
		}
	});
}

/**
 * GUI HOOKS
**/

// Alle taps på skjermen burde sette fokus til input
$(document).ready( function () {
	$(document).on('click touch', function() {
		$('#rfidValue').focus();
	});
});

// Scann et armbånd!
$(document).ready(function() {
	$('#rfidValueForm').submit(function(e) {
		e.preventDefault();
		var rfidValue = $("#rfidValue").val();
		console.log("Scanned: "+rfidValue);

		$("#rfidValueForm")[0].reset();

		Auth.scan(rfidValue, function(response) {
			console.log(response.data);
			if(response.success) {
				playAllowed();
				UKMrfidGUI.showView('BeepedGreen');
				setTimeout(function() {
					UKMrfidGUI.showView('ReadyForBeeping');
					$("#rfidValue").focus();
				}, 1000);
			}
			else {
				playDenied();
				UKMrfidGUI.showView('BeepedRed');
				setTimeout(function() {
					UKMrfidGUI.showView('ReadyForBeeping');
					$("#rfidValue").focus();
				}, 4000);
			}
		});
	});
});