<?php

// Bootstrap Laravel
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Hash;

// Your password to be hashed
$passwordToHash = '123456';

// Hash the password
$hashedPassword = Hash::make($passwordToHash);

echo "Hashed Password: " . $hashedPassword . "\n";

?>
