<?php

declare(strict_types=1);

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.production', false);
        Config::$is3ds = true;
        Config::$isSanitized = true;
    }

    public function createTransaction(array $params): array
    {
        $transaction = Snap::getSnapToken($params);

        return [
            'token' => $transaction,
            'redirect_url' => config('midtrans.snap_url').'/pay/'.$transaction,
        ];
    }

    public function getTransactionStatus(string $orderId): array
    {
        $status = new Transaction;

        return (array) $status->status($orderId);
    }

    public function handleNotification(array $notification): array
    {
        $transaction = new Transaction;
        $status = $transaction->status($notification['order_id']);

        return [
            'order_id' => $status->order_id,
            'transaction_status' => $status->transaction_status,
            'payment_type' => $status->payment_type ?? null,
            'gross_amount' => $status->gross_amount ?? null,
            'transaction_time' => $status->transaction_time ?? null,
            'settlement_time' => $status->settlement_time ?? null,
        ];
    }
}
