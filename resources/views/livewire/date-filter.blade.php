<div class="mb-4">
    <x-label label="Select Date" />

    <div class="grid grid-cols-2 gap-2 mt-2 sm:grid-cols-3">
        @foreach ($dates as $date)
        @php
        $isAvailable = in_array($date, $availableDates);
        $isCurrentDate = $date === $selectedDate;
        @endphp
        <button wire:click="selectDate('{{ $date }}')" @disabled(!$isAvailable) wire:loading.attr="disabled" class="text-xs py-2 px-1 rounded-sm
               {{ $isAvailable ? ($isCurrentDate ? 'bg-indigo-500 text-white' : 'bg-gray-200 dark:text-slate-900') : 'bg-gray-400 text-gray-700 dark:text-white cursor-not-allowed' }}
               {{ !$isAvailable ?  $isCurrentDate ?'bg-gray-500 text-gray-300 dark:text-white' : 'disabled' : '' }}">
            {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
        </button>
        @endforeach
    </div>
</div>
