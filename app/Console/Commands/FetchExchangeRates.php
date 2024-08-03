<?php

namespace App\Console\Commands;

use App\Repositories\ExchangeRateRepository;
use App\Services\ExchangeRateService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class FetchExchangeRates extends Command
{
    const PAST_DAYS = 7;

    protected $signature = 'fetch:exchange-rates
                            {--date= : The specific date to fetch rates (yyyy-mm-dd or yyyymmdd)}
                            {--last-7-days : Fetch rates for the last 7 days}';

    protected $description = 'Fetch exchange rates for a specified date or for the last 7 days';

    protected $exchangeRateRepository;

    protected $exchangeRateService;

    public function __construct(
        ExchangeRateService $exchangeRateService,
        ExchangeRateRepository $exchangeRateRepository
    ) {
        parent::__construct();

        $this->exchangeRateService = $exchangeRateService;
        $this->exchangeRateRepository = $exchangeRateRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('last-7-days')) {
            $this->fetchRatesForLast7Days();

            return;
        }

        $dateOption = $this->option('date');

        if ($dateOption && !$this->isValidDate($dateOption)) {
            $this->error('Invalid date format. Please use yyyy-mm-dd or yyyymmdd.');

            return;
        }

        $date = Carbon::parse($dateOption ?? now())->format('Ymd');
        $this->fetchRatesForDate($date);
    }

    protected function fetchRatesForDate(string $date)
    {
        try {
            $rates = $this->exchangeRateService->fetchRatesByDate($date);
            $this->exchangeRateRepository->insertMany($rates);
            $this->info("Exchange rates for {$date} have been fetched and stored successfully.");
        } catch (\Exception $e) {
            $this->error('Failed to fetch exchange rates: ' . $e->getMessage());
        }
    }

    protected function fetchRatesForLast7Days()
    {
        $progress = $this->output->createProgressBar(self::PAST_DAYS);

        $startDate = Carbon::now()->subDays(self::PAST_DAYS)->format('Ymd');
        $endDate = Carbon::now()->format('Ymd');

        $progress->start();

        try {
            for ($date = $startDate; $date < $endDate; $date = Carbon::parse($date)->addDay()->format('Ymd')) {
                $rates = $this->exchangeRateService->fetchRatesByDate($date);
                $this->exchangeRateRepository->insertMany($rates);
                $progress->advance();
            }

            $progress->finish();
            $progress->clear();

            $this->info('Exchange rates for the last 7 days have been fetched and stored.');
        } catch (\Exception $e) {
            $this->error('Failed to fetch rates: ' . $e->getMessage());
        }
    }

    protected function isValidDate(string $date)
    {
        $formats = ['Y-m-d', 'Ymd'];

        foreach ($formats as $format) {
            $dateTime = \DateTime::createFromFormat($format, $date);

            if ($dateTime) {
                $carbonDate = Carbon::instance($dateTime);

                return $carbonDate->format('Ymd');
            }
        }

        return false;
    }
}
