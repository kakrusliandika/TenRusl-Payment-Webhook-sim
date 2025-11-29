<?php

return [

    'hint' => 'X-Callback-Signature.',

    'summary' => <<<'TEXT'
TriPay จะส่ง callbacks ไปยัง URL ที่คุณกำหนด และใส่เฮดเดอร์ที่ใช้ระบุอีเวนต์และช่วยยืนยันตัวตนผู้ส่ง โดยเฉพาะ callbacks จะมี `X-Callback-Event` (เช่น `payment_status`) และ `X-Callback-Signature` สำหรับการตรวจสอบลายเซ็นตามที่ระบุไว้ในเอกสารของ TriPay ผู้รับ (consumer) ควรอ่านเฮดเดอร์เหล่านี้ ตรวจสอบความถูกต้องของคำขอ และค่อยอัปเดตสถานะภายในหลังจากผ่านการตรวจสอบแล้วเท่านั้น

ออกแบบเอ็นด์พอยต์ให้เร็วและเป็นแบบ idempotent ใช้หน้าต่างความสดใหม่ (freshness window) ที่สั้นหากมี timestamp/nonce และรักษาที่เก็บ dedup แบบเบาโดยใช้ reference หรือ event identifier เป็นคีย์ เมื่อบันทึกอีเวนต์แล้วให้ตอบ 2xx อย่างรวดเร็ว จากนั้นค่อยจัดการผลข้างเคียงแบบอะซิงโครนัส เพื่อความโปร่งใสและการรับมือเหตุการณ์ ให้มี audit trail ที่บันทึกเวลารับ เมตาดาต้าอีเวนต์ และผลการตรวจสอบ โดยไม่บันทึกความลับลงในล็อก
TEXT
    ,

    // dari view Tripay
    'docs' => 'https://tripay.co.id/developer',

    'signature_notes' => [
        'ตรวจสอบ `X-Callback-Event` (เช่น `payment_status`) และ `X-Callback-Signature`.',
        'ตรวจสอบลายเซ็นตามเอกสารของ TriPay; ปฏิเสธเมื่อไม่ตรงกันหรือไม่มีเฮดเดอร์.',
        'ทำให้การประมวลผลเป็นแบบ idempotent (dedup ด้วย reference/event ID) และตอบรับอย่างรวดเร็ว (2xx).',
    ],

    'example_payload' => [
        'reference' => 'TRX-001',
        'status' => 'PAID',
        'amount' => 125000,
        'currency' => 'IDR',
        'provider' => 'tripay',
        'sent_at' => now()->toIso8601String(),
    ],

    'endpoints' => [
        [
            'method' => 'POST',
            'path' => '/api/payments',
            'desc' => __('pages.create_payment'),
        ],
        [
            'method' => 'GET',
            'path' => '/api/payments/{id}',
            'desc' => __('pages.get_payment'),
        ],
        [
            'method' => 'POST',
            'path' => '/api/webhooks/tripay',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
