<?php

namespace KodiCMS\API\Providers;

use Event;
use KodiCMS\API\RouteApiFacade;
use KodiCMS\API\Facades\KeysHelper;
use KodiCMS\Support\ServiceProvider;
use KodiCMS\API\Console\Commands\GenerateApiKeyCommand;
use KodiCMS\Users\Model\Permission;

class ModuleServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerAliases([
            'RouteAPI' => RouteApiFacade::class,
            'Keys'     => KeysHelper::class,
        ]);

        $this->registerConsoleCommand(GenerateApiKeyCommand::class);

        Permission::register('api', 'api', [
            'view_keys',
            'refresh_key',
            'create_keys',
            'delete_keys',
        ]);
    }

    public function boot()
    {
        Event::listen('view.settings.bottom', function () {
            echo view('api::settings')->render();
        });
    }
}
