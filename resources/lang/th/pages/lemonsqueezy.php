<?php

return [

    'hint' => 'เฮดเดอร์ลายเซ็น HMAC.',

    'summary' => <<<'TEXT'
Lemon Squeezy เซ็นทุก webhook ด้วย HMAC แบบตรงไปตรงมาบน **raw request body** ผู้ส่งใช้ webhook “signing secret” ของคุณเพื่อสร้าง HMAC SHA-256 แบบ **hex digest** และส่ง digest นั้นมาในเฮดเดอร์ `X-Signature` หน้าที่ของคุณคืออ่านไบต์ของ body ให้ตรงตามที่ได้รับ (ห้าม re-stringify ห้ามเปลี่ยน whitespace), คำนวณ HMAC เดียวกันด้วย secret ของคุณ, แสดงผลเป็นสตริง **hex**, แล้วเปรียบเทียบกับ `X-Signature` ด้วยการเทียบแบบ constant-time หากค่าไม่ตรงกัน — หรือไม่มีเฮดเดอร์ — ให้ปฏิเสธคำขอก่อนแตะต้อง business logic ใด ๆ

เนื่องจากค่าเริ่มต้นของหลาย framework มัก parse body ก่อนที่คุณจะทำ hash ได้ ให้มั่นใจว่า route ของคุณเข้าถึง raw bytes ได้ (เช่น ตั้งค่า “raw body” ใน Node/Express) ให้มองการ verify เป็นด่านกั้น: ผ่านแล้วค่อย parse JSON และอัปเดตสถานะ ทำ handler ให้เป็น idempotent เพื่อไม่ให้ retries/dupes ทำ side effects ซ้ำ และบันทึกเพียง diagnostics ขั้นต่ำ (ความยาวเฮดเดอร์ที่รับมา, ผลการตรวจสอบ, event id) แทนการเก็บ secret สำหรับการทดสอบในเครื่อง ใช้ test events ของ Lemon Squeezy และจำลองความล้มเหลวเพื่อตรวจสอบพฤติกรรม retry/backoff จดเอกสารเส้นทาง end-to-end — การตรวจสอบลายเซ็น, การ dedup, และการประมวลผลแบบ asynchronous — เพื่อให้ทีมทำซ้ำผลลัพธ์ให้สม่ำเสมอได้ในทุกสภาพแวดล้อม
TEXT
    ,

    // dari view Lemon Squeezy
    'docs' => 'https://docs.lemonsqueezy.com/help/webhooks/signing-requests',

    'signature_notes' => [
        'อ่าน `X-Signature` (HMAC-SHA256 แบบ **hex** ของ raw body) และเข้าถึง raw request bytes.',
        'คำนวณ hex HMAC ด้วย signing secret ของคุณ แล้วเปรียบเทียบด้วยฟังก์ชัน timing-safe.',
        'ปฏิเสธเมื่อไม่ตรง/ไม่มีเฮดเดอร์; parse JSON เฉพาะหลัง verify สำเร็จเท่านั้น.',
        'ทำให้ framework ส่ง raw body ได้ (ไม่ re-serialize); ทำ handler ให้ idempotent และ log diagnostics ขั้นต่ำ.',
    ],

    'example_payload' => [
        'meta' => ['event_name' => 'order_created'],
        'data' => ['id' => 'ord_001', 'status' => 'paid'],
        'provider' => 'lemonsqueezy',
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
            'path' => '/api/webhooks/lemonsqueezy',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
