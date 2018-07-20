<?php

require_once __DIR__.'/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__.'/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new App\Application(
    realpath(__DIR__.'/../')
);

$app->configure('database');
$app->configure('custom');
$app->configure('cache');
$app->configure('jwt');
$app->configure('services');
//$app->configure('filesystems');


$app->withFacades();    // Facades 提供一个静态接口给在应用程序的服务容器中可以取用的类
$app->withEloquent();  // 如果需要使用 Eloquent ORM，应该去掉注释


/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton( Illuminate\Contracts\Filesystem\Factory::class, function ($app) { return new Illuminate\Filesystem\FilesystemManager($app); } );

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

 $app->middleware([
     'cors' => App\Http\Middleware\CORS::class,  // by caoxl
 ]);

 $app->routeMiddleware([
     'auth'       => App\Http\Middleware\Authenticate::class, // by caoxl
     'jwt_auth'   => App\Http\Middleware\Auth\JWT::class,    // by caoxl
 ]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

// Command Demo
$app->register(App\Providers\CommandServiceProvider::class);

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
// $app->register(App\Providers\EventServiceProvider::class);

// redis
$app->register(Illuminate\Redis\RedisServiceProvider::class);


/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

//require_once __DIR__.'/../vendor/qcloud/weapp-sdk/AutoLoader.php';

$app->router->group(['namespace' => 'App\Http\Controllers'], function ($app) {
    require __DIR__.'/../routes/web.php';
    require __DIR__.'/../routes/api.php';  // by caoxl
    require __DIR__.'/../routes/master.php';  // by caoxl
    require __DIR__.'/../routes/user.php';  // by caoxl
    require __DIR__.'/../routes/common.php';  // by caoxl
    require __DIR__.'/../routes/orders.php';  // by jizw
    require __DIR__.'/../routes/system.php';  // by jizw
});

return $app;
