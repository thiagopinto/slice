<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use App\Models\ClearingTransaction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessClearingJson implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     */
    public function __construct(public string $filePath)
    {
        //
    }

    private function classify(array $item): ?string
    {
        if ($item['operation_code'] === '01' && $item['clearing_debit']) {
            return 'COMPRA';
        }

        if ($item['operation_code'] === '03' && $item['clearing_debit']) {
            return 'QUASI-CASH';
        }

        if ($item['operation_code'] === '04' && $item['clearing_debit']) {
            return 'SAQUE';
        }

        if ($item['operation_code'] === '20' && $item['clearing_credit']) {
            return 'ORIGINAL-CREDIT';
        }

        if (
            $item['clearing_action_code'] === 2 &&
            $item['clearing_confirm'] === 1 &&
            $item['clearing_cancel'] === 1
        ) {
            return 'REVERSO';
        }

        return null;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $json = Storage::get($this->filePath);
        $data = json_decode($json, true);

        foreach ($data as $item) {
            ClearingTransaction::create([
                'arn' => $item['arn'],
                'slice_code' => $item['slice_code'],
                'clearing_value' => $item['clearing_value'],
                'clearing_currency' => $item['clearing_currency'],
                'clearing_commission' => $item['clearing_commission'],
                'issuer_exchange_rate' => $item['issuer_exchange_rate'],
                'operation_code' => $item['operation_code'],
                'classified_type' => $this->classify($item),
            ]);
        }
    }
}
