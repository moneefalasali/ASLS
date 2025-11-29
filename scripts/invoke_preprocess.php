<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Api\SignController;

$controller = new SignController();
$method = new ReflectionMethod(SignController::class, 'preprocessText');
$method->setAccessible(true);
$input = 'مرحبا';
$result = $method->invoke($controller, $input, 'ar');
var_dump($result);

$input2 = 'نعم';
var_dump($method->invoke($controller, $input2, 'ar'));

$input3 = 'hello';
var_dump($method->invoke($controller, $input3, 'en'));

$input4 = 'A';
var_dump($method->invoke($controller, $input4, 'en'));
