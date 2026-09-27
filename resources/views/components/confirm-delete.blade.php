@props(['action', 'message' => 'Hapus data ini?'])

<form method="POST" action="{{ $action }}" onsubmit="return confirm({{ json_encode($message) }})">
    @csrf
    @method('DELETE')

    <button
        type="submit"
        class="rounded-md border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50"
    >
        Hapus
    </button>
</form>
