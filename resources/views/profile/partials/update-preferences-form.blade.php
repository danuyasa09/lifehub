<section>
    <header>
        <h2 class="text-lg font-medium text-text">
            {{ __('Preferences') }}
        </h2>

        <p class="mt-1 text-sm text-text-secondary">
            {{ __('Update your app preferences like language, currency, and pomodoro settings.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.preferences.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Language -->
            <div>
                <x-input-label for="language" :value="__('Language')" />
                <select id="language" name="language" class="mt-1 block w-full border-border focus:border-primary focus:ring-primary rounded-md shadow-sm bg-surface text-text">
                    <option value="en" {{ old('language', $user->language) === 'en' ? 'selected' : '' }}>English</option>
                    <option value="id" {{ old('language', $user->language) === 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('language')" />
            </div>

            <!-- Currency -->
            <div>
                <x-input-label for="currency" :value="__('Currency')" />
                <select id="currency" name="currency" class="mt-1 block w-full border-border focus:border-primary focus:ring-primary rounded-md shadow-sm bg-surface text-text">
                    <option value="USD" {{ old('currency', $user->currency) === 'USD' ? 'selected' : '' }}>USD ($)</option>
                    <option value="IDR" {{ old('currency', $user->currency) === 'IDR' ? 'selected' : '' }}>IDR (Rp)</option>
                    <option value="EUR" {{ old('currency', $user->currency) === 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                    <option value="GBP" {{ old('currency', $user->currency) === 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                    <option value="JPY" {{ old('currency', $user->currency) === 'JPY' ? 'selected' : '' }}>JPY (¥)</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('currency')" />
            </div>

            <!-- Pomodoro Focus -->
            <div>
                <x-input-label for="pomodoro_focus" :value="__('Pomodoro Focus Duration (Minutes)')" />
                <x-text-input id="pomodoro_focus" name="pomodoro_focus" type="number" class="mt-1 block w-full" :value="old('pomodoro_focus', $user->pomodoro_focus)" min="5" max="120" required />
                <x-input-error class="mt-2" :messages="$errors->get('pomodoro_focus')" />
            </div>

            <!-- Pomodoro Break -->
            <div>
                <x-input-label for="pomodoro_break" :value="__('Pomodoro Break Duration (Minutes)')" />
                <x-text-input id="pomodoro_break" name="pomodoro_break" type="number" class="mt-1 block w-full" :value="old('pomodoro_break', $user->pomodoro_break)" min="1" max="60" required />
                <x-input-error class="mt-2" :messages="$errors->get('pomodoro_break')" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary/90 focus:bg-primary/90 active:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'preferences-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-text-secondary"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
