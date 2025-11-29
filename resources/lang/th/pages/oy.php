<?php

return [

    'hint' => 'ลายเซ็นคอลแบ็กเฉพาะผู้ให้บริการ.',

    'summary' => <<<'TEXT'
คอลแบ็กของ OY! เป็นส่วนหนึ่งของแนวทางความปลอดภัยที่กว้างขึ้น ซึ่งประกอบด้วย API key ที่ลงทะเบียนและการทำ allowlisting ของ IP ต้นทางสำหรับคำขอจากพาร์ทเนอร์ นอกจากนี้ OY! ยังมีฟีเจอร์ Authorization Callback ที่ช่วยให้คุณควบคุมและอนุมัติคอลแบ็กก่อนที่จะเข้าถึงระบบของคุณ เพิ่มเกตที่ชัดเจนเพื่อป้องกันการเปลี่ยนแปลงสถานะที่ไม่ตั้งใจ อย่างไรก็ตาม ในทางปฏิบัติคุณควรถือว่าคอลแบ็กที่เข้ามาทั้งหมดไม่น่าเชื่อถือจนกว่าจะยืนยันได้ บังคับใช้ความสดใหม่ (หน้าต่าง timestamp/nonce) และทำให้ตัว consumer เป็นแบบ idempotent เพื่อให้ retries และการส่งมอบแบบไม่เรียงลำดับยังคงปลอดภัย

เนื่องจากผู้ให้บริการภายนอกแต่ละรายมีวิธีลงนามคอลแบ็กต่างกัน ซิมูเลเตอร์นี้จึงสาธิต baseline ที่แข็งแรงด้วยเฮดเดอร์ HMAC (เช่น `X-Callback-Signature`) ที่คำนวณบน raw request body แบบตรงตามที่ได้รับ โดยใช้ shared secret แนวคิดนี้สะท้อนหลักการเดียวกับในโปรดักชัน: แฮชจาก raw bytes (ไม่ re-serialize), เปรียบเทียบแบบ constant-time และใช้หน้าต่าง replay ที่สั้น จับคู่กับ store สำหรับ dedup ขนาดเล็กและการตอบรับ 2xx ที่รวดเร็ว เพื่อให้ retry logic ของผู้ให้บริการทำงานได้ดีพร้อมหลีกเลี่ยง side effects ซ้ำ

ในเชิงปฏิบัติการ ให้เก็บ audit trail (เวลารับ, ผลการตรวจสอบ, hash ของ body—ไม่ใช่ secret), หมุนเวียน secret อย่างปลอดภัย และติดตามอัตราการล้มเหลวของการตรวจสอบ หากคุณพึ่งพา allowlist อย่าลืมว่าสามารถเปลี่ยนแปลงได้; การตรวจสอบเชิงคริปโต (หรือเกตการอนุมัติที่ชัดเจนของ OY) ควรเป็น trust anchor หลักเสมอ ทำให้ endpoint แคบ คาดเดาได้ และมีเอกสารชัดเจน เพื่อให้บริการอื่นและทีมงานนำไปใช้ซ้ำได้อย่างมั่นใจ
TEXT
    ,

    // dari view OY
    'docs' => 'https://docs.oyindonesia.com/',

    'signature_notes' => [
        'ใช้แนวทางความปลอดภัยของ OY!: API key ที่ลงทะเบียน + allowlisting IP ต้นทางสำหรับคำขอจากพาร์ทเนอร์.',
        'ใช้ Authorization Callback (บนแดชบอร์ด) เพื่ออนุมัติคอลแบ็กก่อนเข้าถึงระบบของคุณ.',
        'ในซิมูเลเตอร์นี้ ให้ตรวจสอบ `X-Callback-Signature = HMAC-SHA256(raw_body, secret)` เป็นโมเดล best-practice และใช้การเปรียบเทียบแบบ constant-time พร้อมตรวจ freshness.',
        'ทำให้การประมวลผลเป็นแบบ idempotent และตอบ 2xx อย่างรวดเร็วเพื่อให้ retries ของผู้ให้บริการทำงานได้ดี.',
    ],

    'example_payload' => [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'payment.completed',
        'provider' => 'oy',
        'data' => [
            'partner_trx_id' => 'PRT-001',
            'amount' => 25000,
            'currency' => 'IDR',
            'status' => 'COMPLETED',
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
            'path' => '/api/webhooks/oy',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
