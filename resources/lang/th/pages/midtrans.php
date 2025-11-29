<?php

return [

    'hint' => 'การตรวจสอบ signature_key.',

    'summary' => <<<'TEXT'
Midtrans ใส่ค่า `signature_key` ที่คำนวณแล้วไว้ในทุกการแจ้งเตือน HTTP(S) เพื่อให้คุณตรวจสอบแหล่งที่มาก่อนจะประมวลผล สูตรชัดเจนและคงที่:
    SHA512(order_id + status_code + gross_amount + ServerKey)
สร้างสตริงอินพุตจากค่าที่อยู่ใน body ของแจ้งเตือน (เป็นสตริงตามเดิม) แล้วต่อด้วย `ServerKey` ส่วนตัวของคุณ จากนั้นคำนวณ SHA-512 แบบ hex digest และเปรียบเทียบกับ `signature_key` ด้วยการเทียบแบบ constant-time หากตรวจสอบไม่ผ่านให้ทิ้ง/ปฏิเสธการแจ้งเตือน สำหรับข้อความที่ถูกต้อง ให้ใช้ฟิลด์ที่ระบุในเอกสาร (เช่น `transaction_status`) เพื่อขับเคลื่อน state machine — ตอบรับเร็ว (2xx), ส่งงานหนักเข้า queue และทำให้อัปเดตเป็นแบบ idempotent เผื่อมี retries หรือส่งมาผิดลำดับ

ข้อผิดพลาดที่พบบ่อยมี 2 อย่าง: ฟอร์แมตและการแปลงชนิดข้อมูล เก็บ `gross_amount` ให้เหมือนที่ได้รับมาเป๊ะ ๆ (อย่า localize, อย่าเปลี่ยนทศนิยม) ตอนประกอบสตริง และหลีกเลี่ยงการ trim หรือเปลี่ยน whitespace/ขึ้นบรรทัดใหม่ เก็บ deduplication key ต่อ event หรือ ต่อ order เพื่อกัน race condition; log เฉพาะผลการตรวจสอบและ hash ของ body เพื่อ audit โดยไม่รั่ว secret เสริมด้วย rate limiting ที่ endpoint และรหัสข้อผิดพลาดที่ชัดเจน เพื่อให้ระบบมอนิเตอร์แยกได้ว่าเป็น error ชั่วคราว (ลองใหม่ได้) หรือการปฏิเสธถาวร (ลายเซ็นไม่ถูกต้อง)
TEXT
    ,

    // dari view midtrans
    'docs' => 'https://docs.midtrans.com/docs/https-notification-webhooks',

    'signature_notes' => [
        'นำ `order_id`, `status_code`, `gross_amount` จาก body (เป็นสตริง) แล้วต่อด้วย `ServerKey` ของคุณ.',
        'คำนวณ `SHA512(order_id + status_code + gross_amount + ServerKey)` และเปรียบเทียบกับ `signature_key` (constant-time).',
        'หากไม่ตรงให้ปฏิเสธ; หากตรงให้อัปเดตสถานะจาก `transaction_status` ทำให้ processing เป็น idempotent และตอบ 2xx ให้เร็ว.',
        'ระวังการเปลี่ยนฟอร์แมตของ `gross_amount` และ whitespace แปลกปลอมตอนต่อสตริง.',
    ],

    'example_payload' => [
        'order_id' => 'ORDER-001',
        'status_code' => '200',
        'gross_amount' => '25000.00',
        'transaction_status' => 'settlement',
        'signature_key' => '<sha512>',
        'provider' => 'midtrans',
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
            'path' => '/api/webhooks/midtrans',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
