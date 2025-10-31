<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Helpers\CipherHelper;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';
    public string $password = '';

    public function login(): void
    {
        $this->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $this->email)->first();

        if ($user) {
            try {
                // Dekripsi gabungan (AES → Vigenere → Caesar)
                $decrypted = CipherHelper::decryptCombined($user->password, 5, 'ENKRIPSI');

                // Bandingkan tanpa memperhatikan huruf besar kecil
                if (strcasecmp($decrypted, $this->password) === 0) {
                    Auth::login($user);
                    $this->redirect(RouteServiceProvider::HOME, navigate: true);
                    return;
                }
            } catch (\Exception $e) {
                \Log::error('Decrypt error: '.$e->getMessage());
            }
        }

        $this->addError('email', __('Invalid credentials.'));
    }
};
?>

<div>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full"
                          type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                          type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input id="remember" type="checkbox"
                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                       name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900"
                   href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</div>
