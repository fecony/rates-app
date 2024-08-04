<div class="space-y-2">
    <x-label label="Exchange Rates" />

    @if ($rates->isEmpty())
    <x-alert>
        No data available for the selected date and currency
    </x-alert>
    @env(['staging', 'local'])
    <x-alert>
        Don't forget to fetch exchange rates data with <code>fetch:exchange-rates</code> command.
    </x-alert>
    @endenv

    @else
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
        @foreach ($rates as $rate)
        <div class="flex items-center justify-between p-4 bg-white border rounded text-slate-700">
            <h3 class="mr-4 font-semibold text">{{ $rate->currency }}</h3>
            <p class="truncate">{{ $rate->rate }}</p>
        </div>
        @endforeach
    </div>
    @endif
</div>
