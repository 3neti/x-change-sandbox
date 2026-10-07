<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\QueueOperations\QueueTopologyInspector;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('settlement-os:queues:inspect {--strict : Fail when queue operations are not ready} {--json : Emit JSON}')]
#[Description('Inspect Settlement OS queue declarations and Horizon commissioning readiness')]
final class SettlementQueuesInspectCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(QueueTopologyInspector $inspector): int
    {
        $report = $inspector->inspect();

        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
        } else {
            $this->components->twoColumnDetail('Horizon', $report['horizon_enabled'] ? 'enabled' : 'disabled');
            $this->components->twoColumnDetail('Queue connection', $report['queue_connection']);
            $this->components->twoColumnDetail('Horizon connection', $report['horizon_connection']);
            $this->components->twoColumnDetail('Declared queues', implode(', ', $report['declared_queues']) ?: 'none');
            $this->components->twoColumnDetail('Authorized queues', implode(', ', $report['authorized_queues']) ?: 'none');

            foreach ($report['warnings'] as $warning) {
                $this->components->warn($warning);
            }

            foreach ($report['errors'] as $error) {
                $this->components->error($error);
            }
        }

        return $this->option('strict') && ! $report['ready']
            ? self::FAILURE
            : self::SUCCESS;
    }
}
