<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Seed initial data for new user
        $user->taskCategories()->createMany([
            ['name' => 'Work', 'color' => '#4F46E5'],
            ['name' => 'Personal', 'color' => '#10B981'],
            ['name' => 'Health', 'color' => '#F59E0B'],
        ]);

        $user->budgets()->createMany([
            ['category' => 'Groceries', 'limit_amount' => 500, 'period' => now()->startOfMonth()],
            ['category' => 'Entertainment', 'limit_amount' => 200, 'period' => now()->startOfMonth()],
        ]);

        $user->habits()->create([
            'name' => 'Drink Water',
            'frequency' => 'daily',
            'goal' => 8,
            'color' => '#06B6D4'
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
