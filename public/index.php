<?php

declare(strict_types=1);

require '../vendor/autoload.php';

use Caramel\Core\Exception\Exceptions\ValidationException;
use Caramel\Core\Exception\Handler;
use Caramel\Core\Router;
use Caramel\Core\Session;

session_start();

const BASE_PATH = __DIR__ . '/../';

try {
    require BASE_PATH . 'src/Core/functions.php';
    require base_path('bootstrap.php');

    // Parse the URI and method
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $requestMethod = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

    // Resolve the router instance from the container
    $router = app()->container()->resolve(Router::class);

    // Load the routes file
    require base_path('routes.php');

    // Handle the request
    $router->handle_request($requestMethod, $uri);

    // Cleanup session flash data
    Session::unflash();

} catch (ValidationException $ex) {
    // Flash errors and old input data to the session
    Session::flash('_errors', $ex->errors);
    Session::flash('_old', $ex->old);

    // Redirect the user back to the previous page
    $router->redirectToPrevious();

} catch (Throwable $ex) {
    // Clear the output buffer
    ob_clean();

    // Log the exception (optional improvement)
    // error_log($ex->getMessage());

    // Render the exception using the handler
    (new Handler)->render($ex);
}
