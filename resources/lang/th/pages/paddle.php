<?php

return [

    'hint' => 'ลายเซ็นกุญแจสาธารณะ (Classic) / ซีเคร็ต (Billing).',

    'summary' => <<<'TEXT'
Paddle Billing จะลงนามทุก webhook ด้วยเฮดเดอร์ `Paddle-Signature` ซึ่งประกอบด้วย Unix timestamp (`ts`) และลายเซ็น (`h1`) สำหรับการตรวจสอบแบบทำเอง ให้ต่อสตริงที่ต้องลงนามด้วย `ts` ตามด้วยเครื่องหมายโคลอน และ raw request body แบบ “ตรงตามที่รับมา” เพื่อสร้าง signed payload จากนั้นคำนวณ HMAC-SHA256 ด้วย secret ของ notification destination ของคุณ และเปรียบเทียบกับ `h1` ด้วยฟังก์ชันแบบ constant-time (timing-safe) Paddle สร้าง secret แยกต่อ notification destination — ให้ปฏิบัติเหมือนรหัสผ่านและอย่าเก็บไว้ในซอร์สคอนโทรล

แนะนำให้ใช้ SDK ทางการหรือ middleware ของคุณเพื่อทำการตรวจสอบก่อนการ parse ใด ๆ เพราะปัญหาเรื่องเวลาและการแปลง body เป็นกับดักที่เกิดบ่อย ให้แน่ใจว่าเฟรมเวิร์กของคุณเข้าถึง raw bytes ได้ (เช่น Express `express.raw({ type: 'application/json' })`) และกำหนดช่วงยอมรับของ `ts` ให้สั้นเพื่อป้องกัน replay หลังตรวจสอบผ่าน ให้ ACK เร็ว (2xx) เก็บ event ID เพื่อ idempotency และย้ายงานหนักไปทำเป็น background jobs วิธีนี้ช่วยให้การส่งมอบเชื่อถือได้และลดผลข้างเคียงซ้ำเมื่อมี retries

หากกำลังย้ายจาก Paddle Classic โปรดทราบว่าการตรวจสอบได้เปลี่ยนจากลายเซ็นแบบ public-key มาเป็น HMAC ที่อาศัย secret สำหรับ Billing ให้ปรับปรุง runbook และการจัดการ secrets ให้เหมาะสม และเฝ้าดูตัวชี้วัดการตรวจสอบระหว่าง rollout การเปลี่ยนแปลง บันทึก log ที่ชัดเจน (ไม่ใส่ secret) และการตอบกลับ error แบบคงที่ช่วยให้รับมือเหตุขัดข้องและซัพพอร์ตพาร์ทเนอร์ได้ง่ายขึ้นมาก
TEXT
    ,

    // Dari view Paddle
    'docs' => 'https://developer.paddle.com/webhooks/signature-verification',

    'signature_notes' => [
        'อ่านเฮดเดอร์ `Paddle-Signature`; แยกค่า `ts` และ `h1`.',
        'สร้าง signed payload = `ts + ":" + <raw request body>`; คำนวณ HMAC ด้วย secret ของ endpoint.',
        'เปรียบเทียบค่า HMAC ของคุณกับ `h1` ด้วยฟังก์ชัน timing-safe; บังคับใช้ช่วงเวลา `ts` ที่สั้นเพื่อป้องกัน replay.',
        'ควรใช้ SDK ทางการหรือ middleware สำหรับตรวจสอบ; parse JSON หลังตรวจสอบสำเร็จเท่านั้น.',
    ],

    'example_payload' => [
        'event_id' => 'evt_'.now()->timestamp,
        'event_type' => 'transaction.completed',
        'provider' => 'paddle',
        'data' => [
            'transaction_id' => 'txn_001',
            'amount' => 25000,
            'currency_code' => 'IDR',
            'status' => 'completed',
        ],
        'occurred_at' => now()->toIso8601String(),
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
            'path' => '/api/webhooks/paddle',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
