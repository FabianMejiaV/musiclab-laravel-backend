<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectContent extends Model
{
    protected $fillable = [
        'content'
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
        ];
    }

    public function project(): BelongsTo
    {
        return  $this->belongsTo(Project::class, 'project_id');
    }
}
