<?php

namespace App\Console\Commands;

use App\CanCopyFile;
use App\Enums\Category;
use App\Enums\Rarity;
use App\Models\Set;
use App\Models\Card;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use TCGdex\Model\Card as TCGdexCard;
use TCGdex\Model\CardResume;
use TCGdex\TCGdex;

class ImportCardsCommand extends Command
{
    use CanCopyFile;

    protected $signature = 'app:import-cards';

    protected $description = 'Create cards from TCGdex API';

    /**
     * @var Collection<string, int>
     */
    private Collection $setIds;

    public function handle(): int
    {
        $this->setIds = Set::pluck('id', 'tcgdex_id');

        $this->withProgressBar(
            collect((new TCGdex())->card->list())->whereNotIn('id', Card::pluck('tcgdex_id')),
            fn (CardResume $tcgdexCard) => $this->createCard($tcgdexCard->toCard())
        );

        $this->newLine();

        return Command::SUCCESS;
    }

    private function createCard(TCGdexCard $tcgdexCard): Card
    {
        return DB::transaction(function () use ($tcgdexCard): Card {
            $card = Card::create([
                'set_id' => $this->setIds->get($tcgdexCard->set->id),
                'tcgdex_id' => $tcgdexCard->id,
                'name' => $tcgdexCard->name,
                'category' => Category::from($tcgdexCard->category),
                'rarity' => Rarity::from($tcgdexCard->rarity),
                'set_position' => $tcgdexCard->localId,
            ]);

            if ($tcgdexCard->image !== null) {
                $card->update([
                    'scan_path' => $this->copyFileToPublic("$tcgdexCard->image/low.jpg", "card_scans/$card->id.jpg"),
                ]);
            }

            return $card;
        });
    }
}
