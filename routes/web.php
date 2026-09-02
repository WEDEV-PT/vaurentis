<?php

use App\Filament\Pages\MyProjects;
use App\Filament\Resources\Projects\ProjectResource;
use App\Http\Controllers\ProjectClickController;
use Illuminate\Support\Facades\Route;

Route::post('/project-clicks/{project}', [ProjectClickController::class, 'store'])
    ->middleware(['auth', 'throttle:240,1'])
    ->name('project-clicks.store');

Route::get('/', function () {
    $user = auth()->user();

    if (! $user) {
        return redirect('/app/login');
    }

    return redirect(
        $user->is_admin
            ? ProjectResource::getUrl()
            : MyProjects::getUrl(),
    );
});
