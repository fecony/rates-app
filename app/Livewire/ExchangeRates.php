<?php

namespace App\Livewire;

use App\Repositories\ExchangeRateRepository;
use Livewire\Attributes\On;
use Livewire\Component;

class ExchangeRates extends Component
{
    public $selectedDate;

    public $selectedCurrencies = [];

    public $dates = [];

    public $currencies = [];

    public $rates = [];

    public $allRates = [];

    public $availableDates = [];

    public function mount(ExchangeRateRepository $exchangeRateRepository)
    {
        $this->dates = $exchangeRateRepository->getLast7DaysDates();
        $this->currencies = $exchangeRateRepository->getCurrencies();

        $this->allRates = $exchangeRateRepository->getRatesByDateAndCurrency(
            date: null,
            currencies: []
        );

        $latestDate = head($this->dates);
        $this->selectedDate = $latestDate;
        $this->selectedCurrencies = [];
        $this->availableDates = array_unique(array_column($this->allRates, 'date'));

        $this->loadRates();
    }

    #[On('rate:date-selected')]
    public function dateSelected($date)
    {
        $this->selectedDate = $date;
        $this->loadRates();
    }

    #[On('rate:currency-toggle')]
    public function currencyToggled($currencies)
    {
        $this->selectedCurrencies = $currencies;
        $this->loadRates();
    }

    private function loadRates()
    {
        $filteredRates = array_filter($this->allRates, function ($rate) {
            return $rate->date === $this->selectedDate &&
                (empty($this->selectedCurrencies) || in_array($rate->currency, $this->selectedCurrencies));
        });

        $this->rates = collect($filteredRates);
    }

    public function render()
    {
        return view('livewire.exchange-rates', [
            'dates' => $this->dates,
            'currencies' => $this->currencies,
            'rates' => $this->rates,
            'availableDates' => $this->availableDates,
        ]);
    }
}
