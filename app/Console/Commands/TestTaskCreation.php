<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\TaskController;
use App\Models\User;

class TestTaskCreation extends Command
{
    protected $signature = 'test:task';
    protected $description = 'Test task creation to find validation errors';

    public function handle()
    {
        $user = User::first();
        auth()->login($user);

        $request = Request::create('/tasks', 'POST', [
            'title' => 'Test Task',
            'due_date' => '2026-08-04',
            'priority' => 'medium',
            'category_id' => '',
            'status' => 'pending',
            'progress' => '0',
            'is_recurring' => 'none'
        ]);

        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $kernel = app()->make(\Illuminate\Contracts\Http\Kernel::class);
        $app = app();
        $app->instance(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class, new class {
            public function handle($request, $next) { return $next($request); }
        });
        
        try {
            $response = $kernel->handle($request);
            $this->info("Task created successfully!");
            $this->info("Status: " . $response->getStatusCode());
            if ($response->isRedirect()) {
                $this->info("Redirect: " . $response->headers->get('Location'));
            }
            if (session()->has('errors')) {
                $this->error("Errors: " . json_encode(session('errors')->all()));
            }
        } catch (\Exception $e) {
            $this->error("Exception: " . $e->getMessage());
        }
    }
}
