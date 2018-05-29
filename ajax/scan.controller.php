<?php
if (false) {
	$JSON->success = true;
	$JSON->message = "Scan OK";
	// TOOD: Trigger GPIO på RPI
} else {
	// TOOD: Trigger GPIO på RPI
	$JSON->success = false;
	$JSON->message = "Bruker har ikke lov inn!";
	$JSON->data = $_POST;
}
