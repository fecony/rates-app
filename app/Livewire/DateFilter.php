<?php

namespace App\Livewire;

use Livewire\Component;

class DateFilter extends Component
{
    public $dates = [];

    public $availableDates = [];

    public $selectedDate;

    public function mount($dates, $availableDates, $selectedDate)
    {
        $this->dates = $dates;
        $this->availableDates = $availableDates;
        $this->selectedDate = $selectedDate;
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->dispatch('rate:date-selected', $date);
    }

    public function render()
    {
        return view('livewire.date-filter');
    }
}
