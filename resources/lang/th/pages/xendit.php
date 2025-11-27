<?php

return [

    'hint' => 'ลายเซ็นด้วยโทเค็น callback.',

    'summary' => <<<'TEXT'
Xendit “เซ็น” อีเวนต์ webhook ด้วยโทเค็นประจำบัญชีซึ่งส่งมาในเฮดเดอร์ `x-callback-token` การเชื่อมต่อของคุณต้องเทียบค่าเฮดเดอร์นี้กับโทเค็นที่ได้จาก Xendit dashboard และปฏิเสธคำขอที่ไม่มีโทเค็นหรือโทเค็นไม่ตรงกัน บางผลิตภัณฑ์ webhook ยังมี `webhook-id` ที่คุณสามารถบันทึกไว้เพื่อป้องกันการประมวลผลซ้ำเมื่อมีการ retry

ในเชิงปฏิบัติการ ให้ทำการตรวจสอบเป็นขั้นตอนแรกเสมอ บันทึก event record แบบแก้ไขไม่ได้ (immutable) ตอบกลับ 2xx อย่างรวดเร็ว และย้ายงานหนักไปทำในคิว/แบ็กกราวด์ รักษาความเป็น idempotent ด้วย `webhook-id` (หรือคีย์ของคุณเอง) และหากมีเมตาดาต้า timestamp ให้ใช้หน้าต่างเวลา (time window) ที่เข้มงวด เอกสารควรครอบคลุมทั้งเส้นทาง (การตรวจสอบ, การกันซ้ำ, การ retry และ error code) เพื่อให้ทีมและบริการอื่น ๆ เชื่อมต่อได้สอดคล้องกันทุก environment
TEXT
    ,

    // dari view Xendit
    'docs' => 'https://docs.xendit.co/docs/handling-webhooks',

    'signature_notes' => [
        'เปรียบเทียบ `x-callback-token` กับโทเค็นเฉพาะของคุณจาก Xendit dashboard; ไม่ตรงกันให้ปฏิเสธ',
        'ใช้ `webhook-id` (ถ้ามี) เพื่อกันซ้ำ; ถือว่าการตรวจสอบเป็น gate ที่เข้มงวดก่อน parse JSON',
        'ตอบ 2xx ให้เร็วและเลื่อนงานหนักไปทำภายหลัง; ล็อกเฉพาะข้อมูลวินิจฉัยขั้นต่ำโดยไม่เปิดเผยความลับ',
    ],

    'example_payload' => [
        'id'       => 'evt_xnd_' . now()->timestamp,
        'event'    => 'invoice.paid',
        'data'     => [
            'id'     => 'inv_001',
            'status' => 'PAID',
        ],
        'provider' => 'xendit',
        'sent_at'  => now()->toIso8601String(),
    ],

    'endpoints' => [
        [
            'method' => 'POST',
            'path'   => '/api/payments',
            'desc'   => __('pages.create_payment'),
        ],
        [
            'method' => 'GET',
            'path'   => '/api/payments/{id}',
            'desc'   => __('pages.get_payment'),
        ],
        [
            'method' => 'POST',
            'path'   => '/api/webhooks/xendit',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
