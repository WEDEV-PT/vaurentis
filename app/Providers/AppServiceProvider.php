<?php

namespace App\Providers;

use App\Http\Responses\LoginResponse;
use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use App\Support\AuditLogger;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LoginResponseContract::class, LoginResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerAuditEvents(Project::class, 'project');
        $this->registerAuditEvents(User::class, 'user');
        $this->registerAuditEvents(Category::class, 'category');
    }

    /** @param class-string<Model> $model */
    private function registerAuditEvents(string $model, string $resource): void
    {
        $model::created(fn (Model $record) => AuditLogger::record("{$resource}.created", $record));
        $model::updated(fn (Model $record) => AuditLogger::recordUpdate("{$resource}.updated", $record, $record->getChanges()));
        $model::deleted(fn (Model $record) => AuditLogger::record("{$resource}.deleted", $record));
    }
}
