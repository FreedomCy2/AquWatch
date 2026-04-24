<?php

namespace App\Console\Commands;

use App\Models\FloodReading;
use App\Models\FlowReading;
use App\Models\RainReading;
use Illuminate\Console\Command;

class PruneSensorReadings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sensors:prune-readings {--days= : Retention period in days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune old flow, rain, and flood readings beyond retention period';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $defaultDays = (int) config('services.sensors.reading_retention_days', 30);
        $days = (int) ($this->option('days') ?: $defaultDays);

        if ($days < 1) {
            $this->error('Retention days must be at least 1.');

            return self::FAILURE;
        }

        $cutoff = now()->subDays($days);

        $flowDeleted = FlowReading::query()
            ->where('measured_at', '<', $cutoff)
            ->delete();

        $rainDeleted = RainReading::query()
            ->where('measured_at', '<', $cutoff)
            ->delete();

        $floodDeleted = FloodReading::query()
            ->where('measured_at', '<', $cutoff)
            ->delete();

        $totalDeleted = $flowDeleted + $rainDeleted + $floodDeleted;

        $this->info('Pruned old sensor readings.');
        $this->line('Retention days: '.$days);
        $this->line('Cutoff: '.$cutoff->toDateTimeString());
        $this->line('Flow deleted: '.$flowDeleted);
        $this->line('Rain deleted: '.$rainDeleted);
        $this->line('Flood deleted: '.$floodDeleted);
        $this->line('Total deleted: '.$totalDeleted);

        return self::SUCCESS;
    }
}
