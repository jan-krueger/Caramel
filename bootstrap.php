<?php

use Caramel\Core\Database;
use Caramel\Core\Router;

app()->container()->singleton(Database::class, function() {
    $config = require base_path('config.php');
    return new Database($config['database'], 'jan', 'password');
});
app()->container()->singleton(Router::class, function() {
    return new Router();
});
