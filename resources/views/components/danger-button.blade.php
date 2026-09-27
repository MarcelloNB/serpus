<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-red-100 border border-red-200 rounded-md font-semibold text-xs text-red-700 uppercase tracking-widest hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
