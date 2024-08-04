<?php

namespace App\Livewire;

use Livewire\Component;

class CurrencyFilter extends Component
{
    public $currencies = [];

    public $selectedCurrencies = [];

    public function mount($currencies, $selectedCurrencies)
    {
        $this->currencies = $currencies;
        $this->selectedCurrencies = $selectedCurrencies;
    }

    public function toggleCurrency($currency)
    {
        if (in_array($currency, $this->selectedCurrencies)) {
            $this->selectedCurrencies = array_diff($this->selectedCurrencies, [$currency]);
        } else {
            $this->selectedCurrencies[] = $currency;
        }

        $this->dispatch('rate:currency-toggle', $this->selectedCurrencies);
    }

    public function render()
    {
        return view('livewire.currency-filter');
    }
}
