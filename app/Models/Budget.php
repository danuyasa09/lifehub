<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['category', 'limit_amount', 'period'])]
class Budget extends Model
{
    /** @use HasFactory<\Database\Factories\BudgetFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'period' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
