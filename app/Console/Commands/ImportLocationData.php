<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportLocationData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'location:import 
                            {--countries : Import only countries}
                            {--states : Import only states}
                            {--cities : Import only cities}
                            {--fresh : Clear existing data before import}
                            {--debug : Enable debug mode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import location data (countries, states, cities) from JSON files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ini_set('memory_limit', '1G'); // Increased memory limit

        $importCountries = $this->option('countries') || (!$this->option('states') && !$this->option('cities'));
        $importStates = $this->option('states') || (!$this->option('countries') && !$this->option('cities'));
        $importCities = $this->option('cities') || (!$this->option('countries') && !$this->option('states'));
        $fresh = $this->option('fresh');
        $debug = $this->option('debug');

        if ($fresh) {
            if ($this->confirm('This will delete ALL existing location data. Are you sure?')) {
                $this->clearExistingData();
            } else {
                $this->info('Import cancelled.');
                return;
            }
        }

        if ($importCountries) {
            $this->importCountries($debug);
        }

        if ($importStates) {
            $this->importStates($debug);
        }

        if ($importCities) {
            $this->importCities($debug);
        }

        $this->info('✅ Location data import completed!');
    }

    private function clearExistingData()
    {
        $this->info('Clearing existing data...');
        
        // Handle foreign key constraints
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        
        City::truncate();
        State::truncate();
        Country::truncate();
        
        // Re-enable foreign key checks
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
        
        $this->info('✓ Existing data cleared');
    }

    private function importCountries($debug = false)
    {
        $this->info('Importing countries...');
        
        $jsonPath = database_path('data/countries.json');
        if (!File::exists($jsonPath)) {
            $this->error('Countries JSON file not found at: ' . $jsonPath);
            return;
        }

        $countriesJson = File::get($jsonPath);
        $countries = json_decode($countriesJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON in countries file: ' . json_last_error_msg());
            return;
        }

        $bar = $this->output->createProgressBar(count($countries));
        $bar->start();

        $errors = 0;
        foreach ($countries as $countryData) {
            try {
                Country::updateOrCreate(
                    ['id' => $countryData['id']],
                    [
                        'name' => $countryData['name'],
                        'phone_code' => $countryData['phone_code'],
                        'currency' => $countryData['currency'],
                        'currency_symbol' => $countryData['currency_symbol'],
                        'timezones' => $countryData['timezones'],
                        'icon' => $countryData['icon'],
                        'is_active' => $countryData['is_active'],
                    ]
                );
            } catch (\Throwable $th) {
                $errors++;
                Log::error('Error importing country: ' . json_encode($countryData) . ' - ' . $th->getMessage());
                if ($debug) {
                    $this->error('Country error: ' . $th->getMessage());
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->info('');
        $this->info('✓ Imported ' . (count($countries) - $errors) . ' countries (' . $errors . ' errors)');
    }

    private function importStates($debug = false)
    {
        $this->info('Importing states...');
        
        $jsonPath = database_path('data/states.json');
        if (!File::exists($jsonPath)) {
            $this->error('States JSON file not found at: ' . $jsonPath);
            return;
        }

        $statesJson = File::get($jsonPath);
        $states = json_decode($statesJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON in states file: ' . json_last_error_msg());
            return;
        }

        // Get all existing country IDs for validation
        $existingCountryIds = Country::pluck('id')->toArray();
        
        $bar = $this->output->createProgressBar(count($states));
        $bar->start();

        $errors = 0;
        $skipped = 0;
        $processed = 0;

        foreach ($states as $stateData) {
            try {
                // Validate country_id exists
                if (!in_array($stateData['country_id'], $existingCountryIds)) {
                    $skipped++;
                    if ($debug) {
                        $this->warn('Skipping state: ' . $stateData['name'] . ' - Country ID ' . $stateData['country_id'] . ' does not exist');
                    }
                    Log::warning('Skipping state - Country ID not found: ' . json_encode($stateData));
                    $bar->advance();
                    continue;
                }

                // Validate required fields
                if (empty($stateData['name']) || empty($stateData['country_id'])) {
                    $skipped++;
                    if ($debug) {
                        $this->warn('Skipping state due to missing required fields: ' . json_encode($stateData));
                    }
                    Log::warning('Skipping state - Missing required fields: ' . json_encode($stateData));
                    $bar->advance();
                    continue;
                }

                State::updateOrCreate(
                    ['id' => $stateData['id']],
                    [
                        'country_id' => $stateData['country_id'],
                        'name' => trim($stateData['name']),
                        'state_code' => $stateData['state_code'] ?? null,
                        'is_active' => $stateData['is_active'] ?? 1,
                    ]
                );
                $processed++;

            } catch (\Throwable $th) {
                $errors++;
                Log::error('Error importing state: ' . json_encode($stateData) . ' - ' . $th->getMessage());
                if ($debug) {
                    $this->error('State error: ' . $th->getMessage() . ' for state: ' . ($stateData['name'] ?? 'Unknown'));
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->info('');
        $this->info('✓ Processed: ' . $processed . ' states');
        $this->info('✓ Skipped: ' . $skipped . ' states (missing country or invalid data)');
        $this->info('✓ Errors: ' . $errors . ' states');
        
        // Verify final count
        $totalStatesInDb = State::count();
        $this->info('✓ Total states in database: ' . $totalStatesInDb);
    }

    private function importCities($debug = false)
    {
        ini_set('memory_limit', '1G'); 
        $this->info('Importing cities...');
        
        $jsonPath = database_path('data/cities.json');
        if (!File::exists($jsonPath)) {
            $this->error('Cities JSON file not found at: ' . $jsonPath);
            return;
        }

        $citiesJson = File::get($jsonPath);
        $cities = json_decode($citiesJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON in cities file: ' . json_last_error_msg());
            return;
        }

        // Get all existing state IDs for validation
        $existingStateIds = State::pluck('id')->toArray();

        $bar = $this->output->createProgressBar(count($cities));
        $bar->start();

        $errors = 0;
        $skipped = 0;
        $processed = 0;

        foreach ($cities as $cityData) {
            try {
                // Validate state_id exists
                if (!in_array($cityData['state_id'], $existingStateIds)) {
                    $skipped++;
                    if ($debug) {
                        $this->warn('Skipping city: ' . $cityData['name'] . ' - State ID ' . $cityData['state_id'] . ' does not exist');
                    }
                    $bar->advance();
                    continue;
                }

                City::updateOrCreate(
                    ['id' => $cityData['id']],
                    [
                        'state_id' => $cityData['state_id'],
                        'name' => trim($cityData['name']),
                        'is_active' => $cityData['is_active'] ?? 1,
                    ]
                );
                $processed++;

            } catch (\Throwable $th) {
                $errors++;
                Log::error('Error importing city: ' . json_encode($cityData) . ' - ' . $th->getMessage());
                if ($debug) {
                    $this->error('City error: ' . $th->getMessage());
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->info('');
        $this->info('✓ Processed: ' . $processed . ' cities');
        $this->info('✓ Skipped: ' . $skipped . ' cities (missing state or invalid data)');
        $this->info('✓ Errors: ' . $errors . ' cities');
        
        // Verify final count
        $totalCitiesInDb = City::count();
        $this->info('✓ Total cities in database: ' . $totalCitiesInDb);
    }
}