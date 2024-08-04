<?php

namespace App\Repositories;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ExchangeRateRepository
{
    private const AVAILABLE_DATES_CACHE_KEY = 'available_dates';

    private const AVAILABLE_CURRENCIES_CACHE_KEY = 'available_currencies';

    private const RATES_CACHE_KEY_PREFIX = 'rates_';

    private const CACHE_DURATION_MINUTES = 60 * 24; // 24 hours

    /**
     * Get all exchange rates
     *
     * @return array
     */
    public function getAll()
    {
        return DB::select('SELECT * FROM exchange_rates');
    }

    /**
     * Get unique currency list
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCurrencies()
    {
        return Cache::remember(self::AVAILABLE_CURRENCIES_CACHE_KEY, now()->addHours(1), function () {
            $currencies = DB::select('SELECT DISTINCT currency FROM exchange_rates');

            return collect($currencies)->pluck('currency');
        });
    }

    /**
     * Get unique past dates
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDates()
    {
        return Cache::remember(self::AVAILABLE_DATES_CACHE_KEY, now()->addHours(1), function () {
            $dates = DB::select('SELECT DISTINCT date FROM exchange_rates ORDER BY date DESC LIMIT 7');

            return collect($dates)->pluck('date');
        });
    }

    /**
     * Method to generate past 7 days
     */
    public function getLast7DaysDates(): array
    {
        $dates = [];
        $today = Carbon::now();

        for ($i = 0; $i < 7; $i++) {
            $dates[] = $today->format('Y-m-d');
            $today->subDay();
        }

        return $dates;
    }

    /**
     * Get rates for specific date and currency
     *
     * @param  mixed  $currencies
     * @return array
     */
    public function getRatesByDateAndCurrency(
        ?string $date = null,
        ?array $currencies = null
    ) {
        $cacheKey = self::RATES_CACHE_KEY_PREFIX . ($date ?? 'all') . '_' . (implode(',', $currencies) ?? 'all');

        return Cache::remember($cacheKey, self::CACHE_DURATION_MINUTES, function () use ($date, $currencies) {
            $query = 'SELECT * FROM exchange_rates WHERE 1=1';
            $bindings = [];

            if ($date) {
                $query .= ' AND date = :date';
                $bindings['date'] = $date;
            }

            if ($currencies && count($currencies) > 0) {
                $preparedPlaceholders = implode(',', array_fill(0, count($currencies), '?'));
                $query .= ' AND currency IN (' . $preparedPlaceholders . ')';
                $bindings = array_merge($bindings, $currencies);
            }

            $query .= ' ORDER BY date DESC';

            return DB::select($query, $bindings);
        });
    }

    /**
     * Bulk store exchange rates
     *
     * @param  mixed  $rates
     * @return void
     */
    public function insertMany($rates)
    {
        if ($rates->isEmpty()) {
            return;
        }

        $values = $rates->map(
            fn ($rate, $index) => "(:date{$index}, :currency{$index}, :rate{$index}, NOW(), NOW())"
        )->implode(', ');

        $bindings = $rates->flatMap(fn ($rate, $index) => [
            "date{$index}" => $rate['date'],
            "currency{$index}" => $rate['currency'],
            "rate{$index}" => $rate['rate'],
        ])->toArray();

        $query = "
            INSERT INTO exchange_rates (date, currency, rate, created_at, updated_at)
            VALUES {$values}
            ON DUPLICATE KEY UPDATE rate = VALUES(rate), updated_at = NOW()
        ";

        DB::statement($query, $bindings);

        // NOTE: Clear the cache after inserting new data
        Cache::forget(self::AVAILABLE_CURRENCIES_CACHE_KEY);
        Cache::forget(self::AVAILABLE_DATES_CACHE_KEY);
    }
}
