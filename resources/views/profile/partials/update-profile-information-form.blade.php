<section>
    <header>
        <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center">
            <span class="mr-2">??</span> ?箸鞈?
        </h2>

        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            ?湔?函?撣唾?憿舐內?迂?摮隞嗅???        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="憿舐內?迂" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-gray-50 dark:bg-gray-900 focus:bg-white dark:bg-gray-800 transition-colors" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="?餃??萎辣" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-gray-50 dark:bg-gray-900 focus:bg-white dark:bg-gray-800 transition-colors" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        ?函??餃??萎辣撠摰?撽???                        <button form="send-verification" class="underline text-sm text-indigo-600 hover:text-indigo-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            暺?甇方???潮?霅縑
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            銝撠??霅縑撌脩??函?靽∠拳??                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                ?脣?霈
            </button>

            @if (session('status') === 'profile-updated')
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
                    <span class="mr-1">??/span> ?脣???
                </p>
            @endif
        </div>
    </form>
</section>


