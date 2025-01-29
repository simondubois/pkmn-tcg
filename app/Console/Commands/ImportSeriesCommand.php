<?php

namespace App\Console\Commands;

use App\CanCopyFile;
use App\Models\Serie;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use TCGdex\Model\SerieResume;
use TCGdex\TCGdex;

class ImportSeriesCommand extends Command
{
    use CanCopyFile;

    protected $signature = 'app:import-series';

    protected $description = 'Create series from TCGdex API';

    public function handle(): int
    {
        $this->withProgressBar(
            collect((new TCGdex())->serie->list())->whereNotIn('id', Serie::pluck('tcgdex_id')),
            fn (SerieResume $tcgdexSerie) => $this->createSerie($tcgdexSerie)
        );

        $this->newLine();

        return Command::SUCCESS;
    }

    private function createSerie(SerieResume $tcgdexSerie): Serie
    {
        return DB::transaction(function () use ($tcgdexSerie): Serie {
            $serie = Serie::create([
                'tcgdex_id' => $tcgdexSerie->id,
                'name' => $tcgdexSerie->name,
            ]);

            if ($tcgdexSerie->logo !== null) {
                $serie->update([
                    'logo_path' => $this->copyFileToPublic("$tcgdexSerie->logo.jpg", "serie_logos/$serie->id.jpg"),
                ]);
            }

            return $serie;
        });
    }
}
