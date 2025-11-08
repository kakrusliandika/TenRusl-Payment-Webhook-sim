<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\WebhookEvent;
use App\Repositories\WebhookEventRepository;
use App\Services\Webhooks\RetryBackoff;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Throwable;

class RetryWebhookCommand extends Command
{
    /**
     * Nama command (jalankan: php artisan tenrusl:webhooks:retry)
     */
    protected $signature = 'tenrusl:webhooks:retry
        {--limit=50 : Maximum events to process per run}
        {--max=5 : Maximum retry attempts before giving up}
        {--dry-run : Simulate only, do not modify data}';

    protected $description = 'Retry failed webhook processing using exponential backoff (simulation)';

    public function __construct(
        protected WebhookEventRepository $eventsRepo,
        protected RetryBackoff $backoff = new RetryBackoff()
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $limit   = (int) $this->option('limit');
        $max     = (int) $this->option('max');
        $dryRun  = (bool) $this->option('dry-run');

        $due = $this->eventsRepo->dueForRetry($limit);

        if ($due->isEmpty()) {
            $this->info('No events due for retry.');
            return self::SUCCESS;
        }

        $this->line(sprintf(
            'Processing %d due event(s) [limit=%d, max=%d, dry-run=%s] ...',
            $due->count(), $limit, $max, $dryRun ? 'yes' : 'no'
        ));

        $processed = 0;
        $rescheduled = 0;
        $terminalFailed = 0;

        foreach ($due as $event) {
            /** @var WebhookEvent $event */
            $attempt = (int) $event->attempt_count + 1;
            $payload = (array) $event->payload;
            $provider = (string) $event->provider;
            $eventId = (string) $event->event_id;

            $this->comment(sprintf(
                '- [%s] %s attempt=%d (next=%s)',
                $provider, $eventId, $attempt, optional($event->next_retry_at)->toDateTimeString()
            ));

            if ($dryRun) {
                // Simulasi saja: tampilkan keputusan apa yang akan dilakukan
                $decision = $attempt >= $max ? 'give-up (terminal failed)' : 'reschedule';
                $this->line("  (dry-run) would process payload type=" . Arr::get($payload, 'type') . " => {$decision}");
                continue;
            }

            try {
                // ====== SIMULASI RE-PROCESS ======
                // Logika sederhana:
                // - Jika payload punya payment_id, update status payment sesuai type.
                // - Jika payment tidak ditemukan → dianggap gagal → reschedule.
                // - Jika ditemukan → anggap sukses → mark processed.
                $type = (string) Arr::get($payload, 'type', '');
                $paymentId = (string) Arr::get($payload, 'data.payment_id', '');

                if ($paymentId === '') {
                    // Tanpa payment_id kita tidak bisa memproses → reschedule
                    $this->warn("  Missing data.payment_id -> reschedule");
                    $this->reschedule($event, $attempt, $max);
                    $rescheduled++;
                    continue;
                }

                $payment = Payment::query()->find($paymentId);
                if (! $payment) {
                    $this->warn("  Payment not found ($paymentId) -> reschedule");
                    $this->reschedule($event, $attempt, $max, "Payment not found");
                    $rescheduled++;
                    continue;
                }

                // Terapkan status sesuai type (payment.paid|payment.failed)
                if ($type === 'payment.paid') {
                    $payment->status = 'paid';
                } elseif ($type === 'payment.failed') {
                    $payment->status = 'failed';
                } else {
                    // Tipe tak dikenal: tetap reschedule (atau bisa diabaikan)
                    $this->warn("  Unknown type '$type' -> reschedule");
                    $this->reschedule($event, $attempt, $max, "Unknown type");
                    $rescheduled++;
                    continue;
                }
                $payment->save();

                // Sukses → tandai processed
                $event->status = 'processed';
                $event->attempt_count = $attempt;
                $event->next_retry_at = null;
                $event->error_message = null;
                $event->save();

                $processed++;
                $this->info("  processed ✓");

            } catch (Throwable $e) {
                // Jika terjadi exception → reschedule atau terminal fail
                $this->error("  exception: " . $e->getMessage());
                if ($attempt >= $max) {
                    $event->status = 'failed';
                    $event->attempt_count = $attempt;
                    $event->next_retry_at = null;
                    $event->error_message = substr($e->getMessage(), 0, 1000);
                    $event->save();

                    $terminalFailed++;
                    $this->error("  terminal failed ✗");
                } else {
                    $seconds = $this->backoff->secondsFor($attempt);
                    $event->status = 'failed';
                    $event->attempt_count = $attempt;
                    $event->next_retry_at = now()->addSeconds($seconds);
                    $event->error_message = substr($e->getMessage(), 0, 1000);
                    $event->save();

                    $rescheduled++;
                    $this->warn("  rescheduled in {$seconds}s …");
                }
            }
        }

        $this->line(sprintf(
            'Done. processed=%d, rescheduled=%d, terminal_failed=%d',
            $processed, $rescheduled, $terminalFailed
        ));

        return self::SUCCESS;
    }

    /**
     * Reschedule helper dengan exponential backoff.
     */
    protected function reschedule(WebhookEvent $event, int $attempt, int $max, string $reason = 'retry'): void
    {
        if ($attempt >= $max) {
            $event->status = 'failed';
            $event->attempt_count = $attempt;
            $event->next_retry_at = null;
            $event->error_message = $reason . ' (max attempts reached)';
            $event->save();
            $this->error('  terminal failed ✗');
            return;
        }

        $seconds = $this->backoff->secondsFor($attempt);
        $event->status = 'failed';
        $event->attempt_count = $attempt;
        $event->next_retry_at = now()->addSeconds($seconds);
        $event->error_message = $reason;
        $event->save();

        $this->warn("  rescheduled in {$seconds}s …");
    }
}
