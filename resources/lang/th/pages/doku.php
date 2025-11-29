<?php

return [

    'hint' => 'ลายเซ็นด้วยเฮดเดอร์ Client-Id/Request-*.',

    'summary' => <<<'TEXT'
DOKU ปกป้อง HTTP Notification ด้วยลายเซ็นแบบ canonical ที่ขับเคลื่อนด้วยเฮดเดอร์ ซึ่งคุณต้องตรวจสอบก่อนจะทำอะไรกับ payload ใด ๆ ทุก callback จะมาพร้อมเฮดเดอร์ `Signature` ที่มีรูปแบบ `HMACSHA256=<base64>`. เพื่อสร้างค่าที่คาดหวังขึ้นใหม่ ให้คำนวณ `Digest` ของ request body ก่อน: ทำ SHA-256 บนไบต์ JSON ดิบ (raw) แล้วเข้ารหัสเป็น base64. จากนั้นสร้างสตริงที่คั่นด้วยขึ้นบรรทัดใหม่ (newline) จำนวน 5 องค์ประกอบ โดยต้องเป็นลำดับและการสะกด “ตามนี้เป๊ะ”:
  • `Client-Id:<value>`
  • `Request-Id:<value>`
  • `Request-Timestamp:<value>`
  • `Request-Target:<path-of-your-notification-url>` (เช่น `/payments/notifications`)
  • `Digest:<base64-of-SHA256(body)>`
ต่อไปให้คำนวณ HMAC ด้วย SHA-256 โดยใช้ DOKU Secret Key เป็นคีย์บนสตริง canonical นี้ แล้วนำผลไปเข้ารหัส base64 และเติมคำนำหน้า `HMACSHA256=`. สุดท้ายให้เปรียบเทียบกับเฮดเดอร์ `Signature` ด้วยการเทียบแบบ constant-time. หากค่าไม่ตรง ขาดองค์ประกอบ หรือรูปแบบผิด ต้องถือว่าเป็นความล้มเหลวด้านการยืนยันตัวตน และต้องปฏิเสธคำขอทันที

เพื่อความทนทานและความปลอดภัย ให้ตอบรับ notification ที่ถูกต้องอย่างรวดเร็ว (2xx) และย้ายงานหนักไปทำใน background job เพื่อไม่ให้เกิด retries ทำให้ consumer เป็นแบบ idempotent โดยบันทึกตัวระบุที่เคยประมวลผลแล้ว (เช่น `Request-Id` หรือ event ID ใน body) ตรวจสอบ freshness: `Request-Timestamp` ควรอยู่ในช่วงเวลาที่แคบเพื่อป้องกัน replay และตรวจให้แน่ใจว่า `Request-Target` ตรงกับเส้นทางจริงของคุณเพื่อหลีกเลี่ยงบั๊กจาก canonicalization ตอน parse ให้ทำตามแนวทางของ DOKU แบบ non-strict: ไม่รู้จักฟิลด์ก็ให้ข้าม และให้รองรับการเปลี่ยนแปลงสคีมามากกว่าพาร์สเซอร์ที่เปราะบาง ระหว่าง incident response ให้บันทึกการมีอยู่ของเฮดเดอร์ที่จำเป็น ค่า digest/signature ที่คำนวณได้ (ห้าม log secret) และแฮชของ body เพื่อช่วยการตรวจสอบย้อนหลังโดยไม่รั่วไหลข้อมูลอ่อนไหว
TEXT
    ,

    // dari view DOKU
    'docs' => 'https://developers.doku.com/get-started-with-doku-api/signature-component/non-snap/signature-component-from-request-header',

    'signature_notes' => [
        'อ่านเฮดเดอร์: `Client-Id`, `Request-Id`, `Request-Timestamp`, `Signature` และอนุมาน `Request-Target` (path ของ route คุณ).',
        'คำนวณ `Digest = base64( SHA256(raw JSON body) )`.',
        'สร้าง canonical string เป็นบรรทัด: Client-Id, Request-Id, Request-Timestamp, Request-Target, Digest (ตามลำดับนี้ แต่ละอันคนละบรรทัด และไม่มี newline ต่อท้าย).',
        'คำนวณ expected = `HMACSHA256=base64( HMAC-SHA256(canonical, SecretKey) )`; เปรียบเทียบกับ `Signature` แบบ constant-time.',
        'บังคับ freshness ของ timestamp; ทำให้การประมวลผลเป็น idempotent; ACK เร็ว (2xx) และ offload งานหนัก.',
    ],

    'example_payload' => [
        'order' => ['invoice_number' => 'INV-001'],
        'transaction' => ['status' => 'SUCCESS'],
        'provider' => 'doku',
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
            'path' => '/api/webhooks/doku',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
