<?php

use App\Console\Commands\ImportCardsCommand;
use App\Console\Commands\ImportSeriesCommand;
use App\Console\Commands\ImportSetsCommand;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('app:import-all')->daily();

/**
 * @var Illuminate\Console\Command $this
 */
Artisan::command('app:import-all', function () {
    $this->info('Execute ' . ImportSeriesCommand::class);
    $this->call(ImportSeriesCommand::class);
    $this->newLine();

    $this->info('Execute ' . ImportSetsCommand::class);
    $this->call(ImportSetsCommand::class);
    $this->newLine();

    $this->info('Execute ' . ImportCardsCommand::class);
    $this->call('app:import-cards');
    $this->newLine();
})->describe('Import data from TCGdex API');
