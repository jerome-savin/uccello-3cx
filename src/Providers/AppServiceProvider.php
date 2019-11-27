<?php

namespace JeromeSavin\Uccello3cx\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * App Service Provider
 */
class AppServiceProvider extends ServiceProvider
{
  /**
   * Indicates if loading of the provider is deferred.
   *
   * @var bool
   */
  protected $defer = false;

  public function boot()
  {
    // Views
    $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'uccello-3cx');

    // Translations
    $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'uccello-3cx');

    // Migrations
    $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

    // Routes
    $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');

    // Publish assets
    $this->publishes([
      __DIR__ . '/../../public' => public_path('vendor/jerome-savin/uccello-3cx'),
    ], 'uccello-3cx-assets');

  }

  public function register()
  {

  }
}