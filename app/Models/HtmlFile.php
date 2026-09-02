<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HtmlFile extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'name', 'path', 'content', 'is_published'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
