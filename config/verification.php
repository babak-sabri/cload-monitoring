<?php
return [
	'verification-method'				=> env('VERIFICATION_METHOD', 'SMS'),
	'verify-model'						=> \App\Models\Users\Verify::class,
	'verification-expiration-seconds'	=> env('VERIFICATION_EXP_SECONDS', 60)
];