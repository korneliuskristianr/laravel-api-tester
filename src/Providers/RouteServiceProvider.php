<?php

namespace Asvae\ApiTester\Providers;

use Asvae\ApiTester\Http\Middleware\DebugState;
use Asvae\ApiTester\Http\Middleware\DetectRouteMiddleware;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as BaseRouteServiceProvider;
use Illuminate\Routing\Router;
use Symfony\Component\HttpKernel\Kernel;

class RouteServiceProvider extends BaseRouteServiceProvider
{
    /**
     * @type \Illuminate\Foundation\Http\Kernel
     */
    protected $kernel;

    /**
     * Define the routes for the application.
     *
     * @param  Router $router
     *
     * @return void
     */
    public function map(Router $router)
    {
        $router->group([
            'as'         => 'api-tester.',
            'prefix'     => config('api-tester.route'),
            'namespace'  => $this->getNamespace(),
            'middleware' => $this->getMiddlewares(),
        ], function () {
            $this->requireRoutes();
        });
    }

    public function boot(Router $router)
    {
        parent::boot($router);

        $this->kernel = $this->app->make(Kernel::class);
        $this->kernel->prependMiddleware(DetectRouteMiddleware::class);
    }

    protected function getMiddlewares()
    {
        $middlewares = config('api-tester.middleware');
        $middlewares[] = DebugState::class;

        return $middlewares;
    }

    /**
     * Get module namespace
     *
     * @return string
     */
    protected function getNamespace()
    {
        return 'Asvae\ApiTester\Http\Controllers';
    }

    /**
     * @return string
     */
    protected function requireRoutes()
    {
        require __DIR__ . '/../Http/routes.php';
    }
}
