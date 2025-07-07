<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Regressors\SVR;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\Extractors\CSV;
use App\Models\DeliveryNote;
use App\Models\DeliveryBox;
use Carbon\Carbon;

class TrainYieldModel extends Command
{
    protected $signature = 'ml:train-yield';
    protected $description = 'Train ML model to predict next week\'s yield';

    public function handle()
    {
        // EXAMPLE: train on carrot deliveries per week
        $records = [];

        $boxes = DeliveryBox::with('note')
            ->where('crop', 'carrot') // <- change crop or loop for all
            ->get();

        foreach ($boxes as $box) {
            if ($box->note && $box->note->created_at) {
                $records[] = [
                    'week_of_year' => Carbon::parse($box->note->created_at)->weekOfYear,
                    // add other features: grower, term, weather, etc.
                ];
            }
        }

        // Features (X) and labels (y)
        $samples = [];
        $labels = [];
        foreach ($records as $rec) {
            $samples[] = [$rec['week_of_year']]; // add other features as columns
            $labels[] = $rec['quantity']; // what to predict (delivered kg)
        }

        if (count($samples) < 5) {
            $this->error('Not enough data to train!');
            return;
        }

        $dataset = new Labeled($samples, $labels);

        $estimator = new SVR();

        $estimator->train($dataset);

        // Save model to file
        $persister = new Filesystem(storage_path('ml/yield.model'));
        $persister->save($estimator);

        $this->info('Yield model trained and saved!');
    }
}