<?php

namespace App\Console\Commands;

use App\CanCopyFile;
use App\Models\Serie;
use App\Models\Set;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use TCGdex\Model\Set as TCGdexSet;
use TCGdex\Model\SetResume;
use TCGdex\TCGdex;

class ImportSetsCommand extends Command
{
    use CanCopyFile;

    protected $signature = 'app:import-sets';

    protected $description = 'Create sets from TCGdex API';

    /**
     * @var Collection<string, int>
     */
    private Collection $serieIds;

    public function handle(): int
    {
        $this->serieIds = Serie::pluck('id', 'tcgdex_id');

        $this->withProgressBar(
            collect((new TCGdex())->set->list())->whereNotIn('id', Set::pluck('tcgdex_id')),
            fn (SetResume $tcgdexSet) => $this->createSet($tcgdexSet->toSet())
        );

        $this->newLine();

        return Command::SUCCESS;
    }

    private function createSet(TCGdexSet $tcgdexSet): Set
    {
        return DB::transaction(function () use ($tcgdexSet): Set {
            $set = Set::create([
                'serie_id' => $this->serieIds->get($tcgdexSet->serie->id),
                'tcgdex_id' => $tcgdexSet->id,
                'name' => $tcgdexSet->name,
            ]);

            if ($tcgdexSet->logo !== null) {
                $set->update([
                    'logo_path' => $this->copyFileToPublic("$tcgdexSet->logo.jpg", "set_logos/$set->id.jpg"),
                ]);
            }

            return $set;
        });
    }
}
