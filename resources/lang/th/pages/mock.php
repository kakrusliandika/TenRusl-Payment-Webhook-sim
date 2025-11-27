<?php

return [

    'hint' => 'ทดสอบอย่างรวดเร็ว.',

    'summary' => <<<'TEXT'
ผู้ให้บริการ mock นี้เป็นสนามทดสอบที่กำหนดผลได้ (deterministic) และไม่ต้องใช้ข้อมูลรับรอง เพื่อฝึกฝนวงจรชีวิต webhook ทั้งหมด: การสร้างคำขอ, การเปลี่ยนสถานะแบบ idempotent, การส่งมอบ, การตรวจสอบ, การ retry และการจัดการความล้มเหลว เนื่องจากทำงานได้โดยไม่มีการพึ่งพาภายนอก คุณจึงสามารถวนรอบพัฒนาได้ทั้งในเครื่องหรือบน CI, บันทึก fixtures และสาธิตการตัดสินใจด้านสถาปัตยกรรม (เช่น จะวางขั้นตอนตรวจสอบก่อน/หลังการบันทึกข้อมูล) ได้โดยไม่ต้องเสี่ยงรั่วไหล secret จริง

ใช้มันเพื่อจำลองโหมดความล้มเหลวที่พบบ่อย: ส่งมอบล่าช้า, ส่งซ้ำ, อีเวนต์มาไม่เรียงลำดับ และการตอบกลับ 5xx ชั่วคราวที่กระตุ้น exponential backoff นอกจากนี้ mock ยังรองรับ “โหมดลายเซ็น” หลายแบบ (none / HMAC-SHA256 / RSA-verify stub) เพื่อให้ทีมฝึก raw-body hashing, การเปรียบเทียบแบบ constant-time และการกำหนดหน้าต่างเวลา (timestamp window) ได้อย่างปลอดภัย ช่วยให้คุณตรวจสอบ idempotency key และตาราง dedup ได้ก่อนจะเชื่อมต่อกับเกตเวย์จริง

เพื่อให้เอกสารมีคุณภาพ ให้ทำ mock ให้ใกล้เคียง production: รูปแบบ endpoint, headers และรหัสข้อผิดพลาดเหมือนกัน แตกต่างเพียงรากความเชื่อถือ (trust root) ตอบรับ webhook ที่ถูกต้องให้เร็ว (2xx) และย้ายงานหนักไปทำเป็น background jobs ถือว่า payload ของ mock ไม่น่าเชื่อถือจนกว่าจะผ่านการตรวจสอบ—จากนั้นค่อยใช้กฎทางธุรกิจ ผลลัพธ์คือวงจร feedback ที่เร็วและเดโมที่พกพาได้ ซึ่งสะท้อนสถาปัตยกรรมที่คุณจะส่งขึ้น production
TEXT
    ,

    // dari view mock
    'docs' => null,

    'signature_notes' => [
        'โหมดซิมูเลเตอร์: none / HMAC-SHA256 / RSA-verify stub; เลือกผ่าน config เพื่อฝึกเส้นทางการตรวจสอบ.',
        'แฮช raw request body ที่ตรงตามที่ได้รับจริง; เปรียบเทียบด้วยฟังก์ชัน timing-safe; บังคับใช้หน้าต่าง replay ที่สั้น.',
        'บันทึก event ID ที่ประมวลผลแล้วเพื่อ idempotency; ACK webhook ที่ถูกต้องให้เร็ว (2xx) และเลื่อนงานหนักออกไป.',
    ],

    'example_payload' => [
        'id'       => 'evt_' . now()->timestamp,
        'type'     => 'payment.succeeded',
        'provider' => 'mock',
        'data'     => [
            'payment_id' => 'pay_mock_001',
            'amount'     => 25000,
            'currency'   => 'IDR',
            'status'     => 'succeeded',
        ],
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
            'path'   => '/api/webhooks/mock',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
