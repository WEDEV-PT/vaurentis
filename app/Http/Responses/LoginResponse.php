<?php

namespace App\Http\Responses;

use App\Filament\Pages\MyProjects;
use App\Filament\Pages\ProjectViewer;
use App\Filament\Resources\Projects\ProjectResource;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = Filament::auth()->user();

        if ($user->is_admin) {
            return redirect(ProjectResource::getUrl());
        }

        $projects = $user->projects()
            ->where('status', 'active')
            ->where('is_published', true)
            ->whereNotNull('html_content')
            ->orderBy('name')
            ->get();

        if ($projects->count() === 1) {
            return redirect(ProjectViewer::getUrl(['project' => $projects->first()]));
        }

        return redirect(MyProjects::getUrl());
    }
}
