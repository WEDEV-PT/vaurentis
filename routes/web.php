<?php

use App\Filament\Pages\MyProjects;
use App\Filament\Resources\Projects\ProjectResource;
use App\Http\Controllers\ProjectClickController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::post('/project-clicks/{project}', [ProjectClickController::class, 'store'])
    ->middleware(['auth', 'throttle:240,1'])
    ->name('project-clicks.store');

Route::get('/branding/logo', function () {
    $logoPath = User::query()
        ->where('is_admin', true)
        ->whereNotNull('brand_logo_path')
        ->orderBy('id')
        ->value('brand_logo_path');

    abort_unless(filled($logoPath) && Storage::disk('public')->exists($logoPath), 404);

    return Storage::disk('public')->response($logoPath, 'Vaurentis logo');
})->name('branding.logo');

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
