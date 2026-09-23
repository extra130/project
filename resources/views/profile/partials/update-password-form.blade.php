<section>
    <header>
        <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center">
            <span class="mr-2">??</span> 靽格撖Ⅳ
        </h2>

        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            隢Ⅱ靽?董?蝙?券摨西雲憭??冽???蝣潘?隞乩??董???具?        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" value="?桀?撖Ⅳ" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full bg-gray-50 dark:bg-gray-900 focus:bg-white dark:bg-gray-800 transition-colors" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" value="?啣?蝣? />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full bg-gray-50 dark:bg-gray-900 focus:bg-white dark:bg-gray-800 transition-colors" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="蝣箄??啣?蝣? />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full bg-gray-50 dark:bg-gray-900 focus:bg-white dark:bg-gray-800 transition-colors" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                ?湔撖Ⅳ
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-90"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-90"
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm font-medium text-green-600 flex items-center bg-green-50 dark:bg-green-900/50 px-3 py-1 rounded"
                >
                    <span class="mr-1">??/span> 撖Ⅳ撌脫??                </p>
            @endif
        </div>
    </form>
</section>


