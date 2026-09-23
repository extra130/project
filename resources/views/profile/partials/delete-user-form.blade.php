<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-red-700 flex items-center">
            <span class="mr-2">⚠️</span> 刪除帳號
        </h2>

        <p class="mt-2 text-sm text-red-600">
            一旦您的帳號被刪除，所有相關的資源和資料都將被永久清除。在刪除帳號之前，請先下載您想保留的任何資料或資訊。
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-red-600 hover:bg-red-700"
    >
        刪除帳號
    </x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                您確定要刪除這個帳號嗎？
            </h2>

            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                一旦您的帳號被刪除，所有相關的資源和資料都將被永久清除。請輸入您的密碼以確認您想要永久刪除您的帳號。
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="密碼" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full"
                    placeholder="請輸入目前密碼以確認"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" x-on:click="$dispatch('close')" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    取消
                </button>

                <x-danger-button class="ml-3">
                    永久刪除帳號
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>



