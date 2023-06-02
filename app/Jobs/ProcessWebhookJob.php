<?php

namespace App\Jobs;

use Illuminate\Support\Str;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob as SpatieProcessWebhookJob;

class ProcessWebhookJob extends SpatieProcessWebhookJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $transaction = $this->webhookCall->payload['transaction'];
        // Check if the transaction description contains the key of payment
        if (Str::contains($transaction['description'], 'TTDH')) {
            // Grab the order number from transaction description
            $order_number = preg_replace('/[^0-9]/', '', $transaction['description']);
            // Find the order
            $order = Order::find($order_number)->firstOrFail();
            // Set new status for order
            $order->status = 'payment_received';
            // Finish job
            $order->save();
        }
    }
}
