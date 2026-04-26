<x-layouts::auth.branded :title="__('Register')">
    <div class="flex flex-col gap-5 sm:gap-6">
        <div class="flex items-center gap-2">
            <div class="h-px w-6 bg-[#110090] dark:bg-[#f4bf27]"></div>
            <span class="font-display text-xs font-semibold uppercase tracking-[0.18em] text-[#110090] dark:text-[#f4bf27]">
                Nueva cuenta
            </span>
        </div>

        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <!-- Session Status -->
        <x-auth-session-status
            class="rounded-2xl border border-green-600/15 bg-green-50 px-4 py-3 text-center text-sm text-green-700 dark:border-green-500/20 dark:bg-green-500/10 dark:text-green-300"
            :status="session('status')"
        />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-5 sm:gap-6">
            @csrf
            <!-- Name -->
            <flux:input
                name="name"
                :label="__('Name')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Full name')"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button
                    type="submit"
                    variant="primary"
                    class="w-full !bg-[#f4bf27] !text-[#342600] transition hover:!brightness-110"
                    data-test="register-user-button"
                >
                    {{ __('Create account') }}
                </flux:button>
            </div>
        </form>

        <div class="flex flex-wrap items-center justify-center gap-x-1 gap-y-2 text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link
                class="font-semibold text-[#110090] transition-colors hover:text-[#4c2e84] dark:text-[#f4bf27] dark:hover:text-[#dcb14b]"
                :href="route('login')"
                wire:navigate
            >
                {{ __('Log in') }}
            </flux:link>
        </div>
    </div>
</x-layouts::auth.branded>
