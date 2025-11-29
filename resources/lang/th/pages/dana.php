<?php

return [

    'hint' => 'RSA (ตรวจสอบด้วย public key ของ DANA).',

    'summary' => <<<'TEXT'
DANA ใช้รูปแบบลายเซ็นแบบ **อสมมาตร (asymmetric)**: คำขอจะถูกลงนามด้วย private key และผู้เชื่อมต่อระบบจะตรวจสอบด้วย **DANA public key** อย่างเป็นทางการ ในทางปฏิบัติ คุณดึงลายเซ็นจาก header ของ webhook (เช่น `X-SIGNATURE`) จากนั้นถอดรหัส base64 แล้วตรวจสอบ raw HTTP request body กับลายเซ็นนั้นด้วย RSA-2048 และ SHA-256 เฉพาะเมื่อผลการตรวจสอบเป็นบวกเท่านั้นจึงถือว่า payload “แท้จริง” หากการตรวจสอบล้มเหลว—หรือไม่มีลายเซ็น/ไม่มี header—ให้ตอบกลับด้วยโค้ดที่ไม่ใช่ 2xx และหยุดการประมวลผล

เพราะ webhook อาจถูกส่งซ้ำหรือมาถึงไม่เรียงลำดับ ควรออกแบบ handler ให้เป็น idempotent: บันทึกตัวระบุเหตุการณ์ที่ไม่ซ้ำและตัดทอนคำขอซ้ำทันที; ตรวจสอบความสดใหม่ของ timestamp/nonce เพื่อลดความเสี่ยง replay; และถือว่าทุกฟิลด์ไม่เชื่อถือได้จนกว่าจะผ่านการตรวจสอบลายเซ็น หลีกเลี่ยงการ re-serialize JSON ก่อนตรวจสอบ; ให้แฮช/ตรวจสอบจาก bytes ดิบที่มาถึงจริง ๆ เท่านั้น เก็บ secrets และ private keys ออกจาก log; หากต้อง log ให้บันทึกเพียงข้อมูลวินิจฉัยระดับสูง (ผลการตรวจสอบ, แฮชของ body, event ID) และปกป้อง log เหล่านั้นขณะจัดเก็บ

สำหรับทีม ควรจัดทำ runbook สั้น ๆ ครอบคลุม: วิธีโหลดหรือหมุนเวียน DANA public key, วิธีตรวจสอบในแต่ละภาษา/รันไทม์ที่ใช้, กติกา string-to-sign ที่แน่นอนสำหรับการเชื่อมต่อของคุณ, และอะไรคือความล้มเหลวแบบถาวร vs ชั่วคราว ผสานกับนโยบาย retry/backoff ที่แข็งแรง, work queue แบบจำกัด, health checks และระบบแจ้งเตือนเมื่อการตรวจสอบล้มเหลว ผลลัพธ์คือ webhook consumer ที่ปลอดภัยภายใต้โหลด ทนทานต่อการ retry และสอดคล้องกับการตรวจสอบเชิงคริปโตที่ DANA กำหนดไว้ตั้งแต่การออกแบบ
TEXT
    ,

    // dari view DANA
    'docs' => 'https://dashboard.dana.id/api-docs-v2/guide/authentication',

    'signature_notes' => [
        'ถอดรหัส base64 ของค่าจาก header `X-SIGNATURE`.',
        'ตรวจสอบ RSA-2048 + SHA-256 บน raw HTTP body ที่เหมือนเดิมเป๊ะ ๆ โดยใช้ DANA public key อย่างเป็นทางการ; รับเฉพาะเมื่อผลการตรวจสอบเป็นบวก.',
        'ปฏิเสธ webhook ที่ไม่มี/มีลายเซ็นไม่ถูกต้อง หรือ payload ผิดรูปแบบ; ห้ามเชื่อถือข้อมูลก่อนตรวจสอบสำเร็จ.',
    ],

    'example_payload' => [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'payment.paid',
        'provider' => 'dana',
        'data' => [
            'transaction_id' => 'DANA-001',
            'amount' => 25000,
            'currency' => 'IDR',
            'status' => 'SUCCESS',
        ],
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
            'path' => '/api/webhooks/dana',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
