<?php

namespace TemporalKey\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class PruneTemporalKeysCommand extends Command
{
    protected $signature = 'temporal-key:prune';

    protected $description = 'Remove expired temporal keys';

    public function handle()
    {
        \TemporalKey\TemporalKey::$storeModel::query()
            ->where('valid_until', '<=', Carbon::now())
            ->orWhere(function (Builder $q) {
                $q->where('usage_max', '>', 0)
                  ->whereRaw('`usage_max` <= `usage_counter`');
            })
            ->delete();
    }
}
