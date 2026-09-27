<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-brand text-ink border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-brand/70 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
