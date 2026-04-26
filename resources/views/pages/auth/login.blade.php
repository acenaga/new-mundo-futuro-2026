<x-layouts::auth.branded :title="__('Log in')">
    <div class="flex flex-col gap-5 sm:gap-6">
        <div class="flex items-center gap-2">
            <div class="h-px w-6 bg-[#110090] dark:bg-[#f4bf27]"></div>
            <span class="font-display text-xs font-semibold uppercase tracking-[0.18em] text-[#110090] dark:text-[#f4bf27]">
                Acceso seguro
            </span>
        </div>

        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

        <!-- Session Status -->
        <x-auth-session-status
            class="rounded-2xl border border-green-600/15 bg-green-50 px-4 py-3 text-center text-sm text-green-700 dark:border-green-500/20 dark:bg-green-500/10 dark:text-green-300"
            :status="session('status')"
        />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5 sm:gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <div class="flex flex-col gap-3 sm:relative sm:gap-0">
                <flux:input
                    name="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="self-end text-sm sm:absolute sm:top-0 sm:end-0" :href="route('password.request')" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button
                    variant="primary"
                    type="submit"
                    class="w-full !bg-[#f4bf27] !text-[#342600] transition hover:!brightness-110"
                    data-test="login-button"
                >
                    {{ __('Log in') }}
                </flux:button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="flex flex-wrap items-center justify-center gap-x-1 gap-y-2 text-center text-sm text-zinc-600 dark:text-zinc-400">
                <span>{{ __('Don\'t have an account?') }}</span>
                <flux:link
                    class="font-semibold text-[#110090] transition-colors hover:text-[#4c2e84] dark:text-[#f4bf27] dark:hover:text-[#dcb14b]"
                    :href="route('register')"
                    wire:navigate
                >
                    {{ __('Sign up') }}
                </flux:link>
            </div>
        @endif
    </div>
</x-layouts::auth.branded>
