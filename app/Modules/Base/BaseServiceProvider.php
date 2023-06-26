<?php

namespace App\Modules\Base;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

abstract class BaseServiceProvider extends ServiceProvider
{
    /**
     * Pasta do ServiceProvider, usado para mapear
     * as rotas, migrations e mensagens
     * @var string
     */
    protected string $directory = '';

    /**
     * Namespace do ServiceProvider, usado
     * para mapear o controller das rotas
     * @var string
     */
    protected string $namespace = '';

    /**
     * Nome do módulo, usado para as mensagens e views
     * @var string
     */
    protected string $name = '';

    /**
     * Usado para dar nome às relações polimórficas no banco
     * @var array
     */
    protected array $morphMaps = [];

    /**
     * Regras de permissionamentos para os modelos do módulo
     * @var array
     */
    protected array $policies = [];

    public function boot()
    {
        $this->loadMigrationsFrom($this->directory . '/Database/Migrations');

        if ($this->name) {
            $this->loadTranslationsFrom($this->directory . '/Messages', $this->name);
            $this->loadViewsFrom($this->directory . '/Views', $this->name);
        }

        if ($this->morphMaps) {
            Relation::morphMap($this->morphMaps);
        }
    }

    public function register()
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    /**
     * Mapea rotas baseado no prefixo,
     * middlewares e arquivos informados
     */
    protected function mapRoutes(
        string $prefix = 'api',
        array $middlewares = ['api-auth'],
        string $file = 'api.php'
    ): void {
        Route::middleware($middlewares)
            ->prefix($prefix)
            ->namespace($this->namespace)
            ->group($this->directory . '/Routes//' . $file);
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }
}
