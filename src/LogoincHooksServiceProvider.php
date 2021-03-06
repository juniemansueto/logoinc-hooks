<?php

namespace Logo\LogoincHooks;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use Larapack\Hooks\Events\Setup;
use Larapack\Hooks\HooksServiceProvider;
use ILOGO\Logoinc\Facades\Logoinc;

class LogoincHooksServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        $configPath = dirname(__DIR__).'/publishable/config/logoinc-hooks.php';

        $this->mergeConfigFrom($configPath, 'logoinc-hooks');

        // Register the HooksServiceProvider
        $this->app->register(HooksServiceProvider::class);

        if (!$this->enabled()) {
            return;
        }

        if ($this->app->runningInConsole()) {
            $this->publishes(
                [$configPath => config_path('logoinc-hooks.php')],
                'logoinc-hooks-config'
            );
        }

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'logoinc-hooks');
    }

    /**
     * Bootstrap the application services.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function boot(Dispatcher $events)
    {
        if (!$this->enabled()) {
            return;
        }

        if (config('logoinc-hooks.add-route', true)) {
            $events->listen('logoinc.admin.routing', [$this, 'addHookRoute']);
        }

        if (config('logoinc-hooks.add-hook-menu-item', true)) {
            $events->listen(Setup::class, [$this, 'addHookMenuItem']);
        }

        if (config('logoinc-hooks.add-hook-permissions', true)) {
            $events->listen(Setup::class, [$this, 'addHookPermissions']);
        }

        if (config('logoinc-hooks.publish-vendor-files', true)) {
            $events->listen(Setup::class, [$this, 'publishVendorFiles']);
        }
    }

    public function addHookRoute($router)
    {
        $namespacePrefix = '\\Logo\\LogoincHooks\\Controllers\\';

        $router->get('hooks', ['uses' => $namespacePrefix.'HooksController@index', 'as' => 'hooks']);
        $router->get('hooks/{name}/enable', ['uses' => $namespacePrefix.'HooksController@enable', 'as' => 'hooks.enable']);
        $router->get('hooks/{name}/disable', ['uses' => $namespacePrefix.'HooksController@disable', 'as' => 'hooks.disable']);
        $router->get('hooks/{name}/update', ['uses' => $namespacePrefix.'HooksController@update', 'as' => 'hooks.update']);
        $router->post('hooks', ['uses' => $namespacePrefix.'HooksController@install', 'as' => 'hooks.install']);
        $router->delete('hooks/{name}', ['uses' => $namespacePrefix.'HooksController@uninstall', 'as' => 'hooks.uninstall']);
    }

    public function addHookMenuItem()
    {
        $menu = Logoinc::model('Menu')::where('name', 'admin')->first();

        if (is_null($menu)) {
            return;
        }

        $parentId = null;

        $toolsMenuItem = Logoinc::model('MenuItem')::where('menu_id', $menu->id)
            ->where('title', 'Tools')
            ->first();

        if ($toolsMenuItem) {
            $parentId = $toolsMenuItem->id;
        }

        $menuItem = Logoinc::model('MenuItem')::firstOrNew([
            'menu_id' => $menu->id,
            'title'   => 'Hooks',
            'url'     => '',
            'route'   => 'logoinc.hooks',
        ]);

        if (!$menuItem->exists) {
            $menuItem->fill([
                'target'     => '_self',
                'icon_class' => 'logoinc-hook',
                'color'      => null,
                'parent_id'  => $parentId,
                'order'      => 13,
            ])->save();
        }
    }

    public function addHookPermissions()
    {
        Logoinc::model('Permission')::firstOrCreate([
            'key'        => 'browse_hooks',
            'table_name' => null,
        ]);
    }

    public function publishVendorFiles()
    {
        Artisan::call('vendor:publish', ['--provider' => static::class]);
    }

    public function enabled()
    {
        if (config('logoinc-hooks.enabled', true)) {
            return config('hooks.enabled', true);
        }

        return config('logoinc-hooks.enabled', true);
    }
}
