<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-red-700 flex items-center">
            <span class="mr-2">??</span> ?芷撣唾?
        </h2>

        <p class="mt-2 text-sm text-red-600">
            銝?行?董?◤?芷嚗????鞈????撠◤瘞訾?皜??芷撣唾?銋?嚗???頛?喃???隞颱?鞈???閮?        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-red-600 hover:bg-red-700"
    >
        ?芷撣唾?
    </x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                ?函Ⅱ摰??芷?董??嚗?            </h2>

            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                銝?行?董?◤?芷嚗????鞈????撠◤瘞訾?皜??頛詨?函?撖Ⅳ隞亦Ⅱ隤?唾?瘞訾??芷?函?撣唾???            </p>

            <div class="mt-6">
                <x-input-label for="password" value="撖Ⅳ" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full"
                    placeholder="隢撓?亦??蝣潔誑蝣箄?"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" x-on:click="$dispatch('close')" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    ??
                </button>

                <x-danger-button class="ml-3">
                    瘞訾??芷撣唾?
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>


