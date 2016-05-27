<?php

namespace KodiCMS\API\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Auth;
use KodiCMS\API\TokenGuard;
use KodiCMS\Support\ServiceProvider;
use KodiCMS\Users\Model\Permission;
use KodiCMS\Users\Model\User;

class ModuleServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->registerAliases([
            'RouteAPI' => \KodiCMS\API\RouteApiFacade::class,
        ]);

        Permission::register('api', 'api', [
            'view_keys',
            'refresh_key',
            'create_keys',
            'delete_keys',
        ]);
    }

    public function boot(Router $router)
    {
        $router->middlewareGroup('api', [
            \KodiCMS\API\Http\Middleware\VerifyApiToken::class,
        ]);

        Auth::viaRequest('token', function ($request) {
            return app(TokenGuard::class)->user($request);
        });
    }

    /**
     * @param DispatcherContract $events
     */
    public function contextBackend(DispatcherContract $events)
    {
        $events->listen('view.user.edit.footer', function (User $user) {
            if(backend_auth()->id() == $user->id) {
                echo view('api::settings', compact('user'))->render();
            }
        });
    }
}
