<?php

namespace App\Console\Commands;

use App\Models\Deposit;
use App\Services\SparkProxyWebhookService;
use App\Constants\Status;
use Illuminate\Console\Command;

class RetrySparkProxyWebhooks extends Command
{
    protected $signature   = 'sparkproxy:retry-webhooks';
    protected $description = 'Retry failed/unsent SparkProxy payment webhooks (max 3 attempts)';

    public function handle(): int
    {
        $deposits = Deposit::query()
            ->whereNotNull('sparkproxy_ref')
            ->whereNull('webhook_sent_at')
            ->where('webhook_attempts', '<', 3)
            ->where('status', Status::PAYMENT_SUCCESS)
            ->where('created_at', '>=', now()->subDay())
            ->get();

        if ($deposits->isEmpty()) {
            $this->info('No pending SparkProxy webhooks.');
            return 0;
        }

        foreach ($deposits as $deposit) {
            $this->info("Retrying ref={$deposit->sparkproxy_ref} (attempts={$deposit->webhook_attempts})");
            SparkProxyWebhookService::dispatch($deposit);
        }

        return 0;
    }
}
