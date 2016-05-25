<?php

namespace KodiCMS\API\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use KodiCMS\API\Console\Commands\GenerateApiKeyCommand;
use KodiCMS\API\Facades\KeysHelper;
use KodiCMS\API\RouteApiFacade;
use KodiCMS\Support\ServiceProvider;
use KodiCMS\Users\Model\Permission;

class ModuleServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->registerAliases([
            'RouteAPI' => RouteApiFacade::class,
            'Keys' => KeysHelper::class,
        ]);

        $this->registerConsoleCommand(GenerateApiKeyCommand::class);

        Permission::register('api', 'api', [
            'view_keys',
            'refresh_key',
            'create_keys',
            'delete_keys',
        ]);
    }

    /**
     * @param DispatcherContract $events
     */
    public function contextBackend(DispatcherContract $events)
    {
        $events->listen('view.settings.bottom', function () {
            echo view('api::settings')->render();
        });
    }
}
