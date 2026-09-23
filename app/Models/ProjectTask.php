<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['title', 'description', 'status', 'order'])]
class ProjectTask extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectTaskFactory> */
    use HasFactory;

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
