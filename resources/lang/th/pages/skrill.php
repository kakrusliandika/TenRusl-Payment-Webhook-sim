<?php

return [

    'hint' => 'ลายเซ็น callback สไตล์ MD5/HMAC.',

    'summary' => <<<'TEXT'
Skrill จะ POST สถานะ callback ไปยัง `status_url` ของคุณ และคาดหวังให้คุณตรวจสอบข้อความด้วย `md5sig` ซึ่งเป็น **MD5 ตัวพิมพ์ใหญ่ (uppercase)** ของการนำฟิลด์ที่กำหนดมาต่อกันตามลำดับ (ตัวอย่าง: `merchant_id + transaction_id + UPPERCASE(MD5(secret_word)) + mb_amount + mb_currency + status`) คุณควรเชื่อถือ payload ก็ต่อเมื่อค่าที่คุณคำนวณได้ตรงกับ `md5sig` ที่ส่งมาเท่านั้น Skrill ยังรองรับตัวเลือก `sha2sig` (SHA-2 ตัวพิมพ์ใหญ่) แบบทางเลือกเมื่อร้องขอ ซึ่งสร้างขึ้นในลักษณะเดียวกับ `md5sig`

ในทางปฏิบัติ ให้ทำการตรวจสอบลายเซ็นที่ฝั่งแบ็กเอนด์เท่านั้น (ห้ามเปิดเผย secret word) และแฮช **ค่าพารามิเตอร์แบบเดิมเป๊ะ** ตามที่ระบบส่งกลับมา ทำให้เอ็นด์พอยต์เป็นแบบ idempotent (dedup ด้วย transaction หรือ event ID) ตอบ 2xx ให้เร็วหลังบันทึกข้อมูล และเลื่อนงานที่ไม่สำคัญออกไป ระหว่างดีบัก ให้บันทึกผลการตรวจสอบและแฮชของบอดี โดยไม่ใส่ความลับลงในล็อก ระวังเรื่องฟอร์แมต—ฟิลด์จำนวนเงินและสกุลเงินต้องใช้แบบ verbatim ขณะประกอบสตริงลายเซ็น—เพื่อให้การเปรียบเทียบคงที่แม้มีการ retry และต่างสภาพแวดล้อม
TEXT
    ,

    // dari view Skrill
    'docs' => 'https://www.skrill.com/fileadmin/content/pdf/Skrill_Quick_Checkout_Guide_v10.3.pdf',

    'signature_notes' => [
        'สร้าง `md5sig` ใหม่ให้ตรงตามเอกสาร: ต่อฟิลด์ที่กำหนด (เช่น merchant_id, transaction_id, UPPERCASE(MD5(secret_word)), mb_amount, mb_currency, status) แล้วคำนวณ **MD5 ตัวพิมพ์ใหญ่**.',
        'เปรียบเทียบกับ `md5sig` ที่ได้รับ; หาก Skrill เปิดใช้ สามารถใช้ `sha2sig` (SHA-2 ตัวพิมพ์ใหญ่) เป็นทางเลือกได้.',
        'ตรวจสอบฝั่งเซิร์ฟเวอร์เท่านั้น โดยใช้ค่าที่โพสต์มาตามเดิม; ทำให้ handler เป็น idempotent และตอบ 2xx ให้เร็ว.',
    ],

    'example_payload' => [
        'transaction_id' => 'SKR-001',
        'mb_amount'      => '10.00',
        'mb_currency'    => 'EUR',
        'status'         => '2',
        'md5sig'         => '<UPPERCASE_MD5>',
        'provider'       => 'skrill',
        'sent_at'        => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/skrill',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
