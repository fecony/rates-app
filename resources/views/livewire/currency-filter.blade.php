<div class="mb-4">
    <x-label label="Select Currencies" />

    <div class="grid grid-cols-3 gap-2 mt-2 sm:grid-cols-6 lg:grid-cols-10">
        @foreach($currencies as $currency)
        <button wire:click="toggleCurrency('{{ $currency }}')" wire:loading.attr="disabled" class="text-sm p-1 rounded-sm hover:bg-indigo-400 hover:text-white {{ in_array($currency, $selectedCurrencies) ? 'bg-indigo-500 text-white' : 'bg-gray-200 text-gray-700 ' }}">
            {{ $currency }}
        </button>
        @endforeach
    </div>
</div>
