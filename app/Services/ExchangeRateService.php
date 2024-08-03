<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Saloon\XmlWrangler\XmlReader;

class ExchangeRateService
{
    const EXCHANGE_RATE_URL = 'http://www.bank.lv/vk/ecb.xml';

    public function fetchRatesByDate(string $date)
    {
        $url = self::EXCHANGE_RATE_URL . '?date=' . $date;

        $response = Http::get($url);

        if ($response->failed()) {
            throw new \Exception('Failed to fetch exchange rates');
        }

        $reader = XmlReader::fromString($response->body());
        $errorResponse = $reader->value('Error')->first();

        if (isset($errorResponse)) {
            throw new \Exception((string) $errorResponse);
        }

        $date = $reader->value('CRates.Date')->sole();
        $rates = $reader
            ->value('CRates.Currencies.Currency')
            ->collect()
            ->map(fn ($rate) => [
                'date' => $date,
                'currency' => (string) $rate['ID'],
                'rate' => (float) $rate['Rate'],
            ]);

        return $rates;
    }
}
