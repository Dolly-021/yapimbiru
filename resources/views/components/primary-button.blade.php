<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-linear-to-r from-yellow-400 to-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:from-yellow-500 hover:to-orange-600 focus:from-yellow-500 focus:to-orange-600 active:from-yellow-600 active:to-orange-700 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
