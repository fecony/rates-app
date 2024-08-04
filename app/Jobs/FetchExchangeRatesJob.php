<?php

namespace App\Jobs;

use App\Repositories\ExchangeRateRepository;
use App\Services\ExchangeRateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class FetchExchangeRatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    const RETRY_AFTER = 3600;

    protected $date;

    public function __construct($date = null)
    {
        $this->date = $date;
    }

    /**
     * Execute the job.
     */
    public function handle(
        ExchangeRateService $exchangeRateService,
        ExchangeRateRepository $exchangeRateRepository
    ): void {
        try {
            $date = $this->date ?? Carbon::now()->format('Ymd');
            $rates = $exchangeRateService->fetchRatesByDate($date);

            if ($rates->isEmpty()) {
                throw new \Exception("No exchange rates available for date {$date}");
            }

            // Validate the date in the rates
            $rateDate = data_get($rates->first(), 'date');
            if (!$rateDate !== $date) {
                throw new \Exception("Exchange rates are not up to date: {$date}");
            }

            $exchangeRateRepository->insertMany($rates);

            Log::info("Exchange rates for {$date} have been fetched and stored successfully.");
        } catch (\Exception $exception) {
            Log::error('Failed to fetch latest exchange rates: ' . $exception->getMessage());

            $this->release(self::RETRY_AFTER);
        }
    }

    public function failed(?Throwable $exception)
    {
        // Send user notification of failure, etc...
        Log::error('Job failed after retries: ' . $exception->getMessage());
    }
}
