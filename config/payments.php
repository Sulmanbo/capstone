<?php

/**
 * Payment-related display config.
 *
 * Bank / e-wallet accounts shown to students on the payments page.
 * Each account has an optional `qr_path` pointing to a PNG/JPG you upload
 * under public/images/payment-qr/. The student can scan the QR with their
 * banking or GCash app to auto-fill the recipient details.
 *
 * To swap a placeholder QR for the real one, just replace the image file at
 * public/images/payment-qr/<file>.png — no code change needed.
 */
return [

    'accounts' => [
        [
            'id'             => 'bdo',
            'label'          => 'BDO Unibank',
            'account_name'   => 'Philippine Academy of Sakya, Inc.',
            'account_number' => '1234-5678-9012',
            'branch'         => 'Manila Main Branch',
            'qr_path'        => 'images/payment-qr/bdo-placeholder.svg',
        ],
        [
            'id'             => 'bpi',
            'label'          => 'BPI',
            'account_name'   => 'Philippine Academy of Sakya, Inc.',
            'account_number' => '9876-5432-1098',
            'branch'         => 'Binondo Branch',
            'qr_path'        => 'images/payment-qr/bpi-placeholder.svg',
        ],
        [
            'id'             => 'gcash',
            'label'          => 'GCash',
            'account_name'   => 'Phil. Academy of Sakya',
            'account_number' => '0917-123-4567',
            'branch'         => null,
            'qr_path'        => 'images/payment-qr/gcash-placeholder.svg',
        ],
    ],

    'instructions' => 'Choose an account below, scan the QR with your banking or GCash app (or transfer manually using the account number). After paying, enter your transfer reference number and upload a clear screenshot of the receipt. The registrar will verify and confirm your payment.',
];
