<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'experience', 'level', 'language', 'currency', 'pomodoro_focus', 'pomodoro_break'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the tasks for the user.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the task categories for the user.
     */
    public function taskCategories()
    {
        return $this->hasMany(TaskCategory::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    public function habits()
    {
        return $this->hasMany(Habit::class);
    }

    public function journals()
    {
        return $this->hasMany(Journal::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function finances()
    {
        return $this->hasMany(Finance::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function aiChats()
    {
        return $this->hasMany(AiChat::class);
    }

    /**
     * Add experience points and handle level up.
     */
    public function addExperience(int $points)
    {
        $this->experience += $points;
        
        // Simple formula: Level = floor(sqrt(XP / 100)) + 1
        // Level 1: 0-99
        // Level 2: 100-399
        // Level 3: 400-899
        // Level 4: 900-1599
        $newLevel = floor(sqrt($this->experience / 100)) + 1;
        
        if ($newLevel > $this->level) {
            $this->level = $newLevel;
            // Optionally, we could fire an event here
        }
        
        $this->save();
    }

    /**
     * Get the currency symbol for the user.
     */
    public function getCurrencySymbolAttribute()
    {
        return match($this->currency) {
            'IDR' => 'Rp',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            default => '$', // USD and default
        };
    }
}
