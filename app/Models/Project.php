<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'html_content',
        'html_filename',
        'is_published',
        'status',
    ];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function accesses()
    {
        return $this->hasMany(ProjectAccess::class);
    }

    public function clicks()
    {
        return $this->hasMany(ProjectClick::class);
    }
}
