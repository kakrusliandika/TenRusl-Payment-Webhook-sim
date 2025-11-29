<?php

return [

    'hint' => 'Verify Webhook Signature API.',

    'summary' => <<<'TEXT'
PayPal กำหนดให้ต้องตรวจสอบ webhook ทุกครั้งฝั่งเซิร์ฟเวอร์ผ่าน Verify Webhook Signature API อย่างเป็นทางการ ตัว listener ของคุณต้องดึงเฮดเดอร์ที่มากับการแจ้งเตือน—`PAYPAL-TRANSMISSION-ID`, `PAYPAL-TRANSMISSION-TIME`, `PAYPAL-CERT-URL`, และ `PAYPAL-TRANSMISSION-SIG`—พร้อมกับ `webhook_id` ของคุณ และ **raw** request body (`webhook_event`) จากนั้น POST ค่าทั้งหมดไปยัง endpoint สำหรับตรวจสอบ และยอมรับอีเวนต์ก็ต่อเมื่อ PayPal ส่งผลลัพธ์เป็น “สำเร็จ” วิธีนี้มาแทนกลไกตรวจสอบแบบเดิม และช่วยให้สอดคล้องกันในผลิตภัณฑ์ REST ต่าง ๆ

ออกแบบ consumer ให้เป็น gate ที่เร็วและเป็น idempotent: ตรวจสอบก่อน บันทึก event record ตอบกลับ 2xx แล้วค่อยส่งงานหนักไปที่คิว ใช้การเปรียบเทียบแบบ constant-time สำหรับการตรวจสอบภายใน และคง raw bytes เดิมไว้เมื่อส่งต่อไปยัง PayPal เพื่อเลี่ยงบั๊กจากการ re-serialize บังคับใช้ช่วงเวลายอมรับที่แคบรอบ `PAYPAL-TRANSMISSION-TIME` เพื่อลด replay window และบันทึกข้อมูล audit เท่าที่จำเป็น (request ID, ผลการตรวจสอบ, body hash—ไม่ใส่ความลับ) ด้วยรูปแบบนี้ การส่งซ้ำหรือ outage บางส่วนจะไม่ทำให้เกิดการประมวลผลซ้ำ และ audit trail จะน่าเชื่อถือระหว่างการแก้ไขเหตุขัดข้อง
TEXT
    ,

    // Dari view PayPal
    'docs' => 'https://developer.paypal.com/docs/api/webhooks/v1/',

    'signature_notes' => [
        'รวบรวมเฮดเดอร์: PAYPAL-TRANSMISSION-ID, PAYPAL-TRANSMISSION-TIME, PAYPAL-CERT-URL, PAYPAL-TRANSMISSION-SIG; เก็บ raw body ไว้.',
        'เรียก Verify Webhook Signature API ด้วยค่านี้พร้อม webhook_id และ webhook_event; ยอมรับเฉพาะเมื่อสำเร็จ.',
        'มองการตรวจสอบเป็น gate; บังคับใช้ time tolerance สั้นเพื่อลด replay และทำให้ consumer เป็น idempotent.',
        'ตอบ 2xx ให้เร็ว, ส่งงานหนักเข้าคิว, และ log เฉพาะข้อมูลจำเป็น (ไม่มีความลับ).',
    ],

    'example_payload' => [
        'id' => 'WH-'.now()->timestamp,
        'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
        'resource' => [
            'id' => 'CAP-001',
            'status' => 'COMPLETED',
        ],
        'provider' => 'paypal',
        'create_time' => now()->toIso8601String(),
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
            'path' => '/api/webhooks/paypal',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
