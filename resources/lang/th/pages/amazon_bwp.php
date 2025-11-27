<?php

return [

    'hint' => 'มี x-amzn-signature ในทุกคำขอ.',

    'summary' => <<<'TEXT'
Buy with Prime (BWP) จะลงลายเซ็นให้กับทุก webhook เพื่อให้คุณยืนยันได้ว่า webhook นั้นมาจาก Amazon จริง และไม่ถูกแก้ไขระหว่างทาง แต่ละคำขอจะมีลายเซ็นดิจิทัลอยู่ในเฮดเดอร์ `x-amzn-signature` ตัว handler ของคุณต้องสร้าง “ลายเซ็นที่ควรจะเป็น” ขึ้นมาใหม่ให้ตรงตามที่ BWP ระบุไว้สำหรับชนิดอีเวนต์และสภาพแวดล้อมนั้น ๆ อย่างเคร่งครัด; ถ้าค่าไม่ตรงกันให้ปฏิเสธคำขอทันที ส่วน timestamp/nonce ที่มากับคำขอให้ถือเป็นส่วนหนึ่งของการป้องกันการ replay โดยต้องกำหนดช่วงเวลาความถูกต้องที่แคบ และเก็บตัวระบุที่เคยประมวลผลแล้วเพื่อหลีกเลี่ยงข้อมูลซ้ำ

ในเชิงปฏิบัติการ ควรออกแบบ endpoint ให้เร็วและมีพฤติกรรมที่คาดเดาได้ (deterministic): ตรวจสอบก่อน ตอบรับด้วย `2xx` หลังบันทึกได้อย่างปลอดภัย แล้วค่อยทำงานหนักแบบ asynchronous หากคุณพึ่งพา allowlist ให้จำไว้ว่า IP และเครือข่ายสามารถเปลี่ยนได้—การตรวจสอบเชิงคริปโตคือหลักฐานความเชื่อถือหลัก ควรเก็บ audit trail อย่างปลอดภัย (request ID, มี/ไม่มีลายเซ็น, ผลการตรวจสอบ และแฮชของบอดี—ไม่ใช่ secret) สำหรับการทดสอบในเครื่อง สามารถทำ stub ขั้นตอนตรวจสอบไว้หลังแฟลกของ environment ได้ แต่ต้องทำให้แน่ใจว่าใน production จะตรวจสอบลายเซ็นเสมอ ตอนหมุนเวียนคีย์หรือปรับกฎ canonicalization ให้ค่อย ๆ roll forward อย่างระมัดระวัง เฝ้าดูอัตรา error และบันทึกชุดเฮดเดอร์และกติกา hashing/canonicalization ที่ใช้อย่างชัดเจน เพื่อให้บริการอื่น ๆ ในสแต็กทำงานสอดคล้องกัน

ในมุมมองการใช้งานร่วมกัน ควรแสดง **เหตุผลการล้มเหลวที่ชัดเจน** (ลายเซ็นไม่ถูกต้อง, timestamp เก่า/ไม่ผ่านช่วงเวลา, คำขอผิดรูปแบบ) และส่งรหัสข้อผิดพลาดที่เสถียรเพื่อให้การ retry ทำงานได้คาดเดาได้ ผสานกับ idempotency ระดับแอปและการป้องกัน replay เพื่อให้การเปลี่ยนสถานะการชำระเงินฝั่ง downstream ปลอดภัย แม้เกิด retry, ทราฟฟิกพุ่ง หรือเกิดเหตุขัดข้องบางส่วน
TEXT
    ,

    // Dari view amazon_bwp.blade.php
    'docs' => 'https://documents.buywithprime.amazon.com/bwp-api/docs/subscribe-to-events',

    'signature_notes' => [
        'อ่าน `x-amzn-signature` จากเฮดเดอร์ของคำขอ.',
        'สร้างลายเซ็นที่คาดหวังขึ้นมาใหม่ให้ตรงตามที่ Buy with Prime กำหนด (อัลกอริทึม/การ canonicalization ตามเอกสารทางการ) และปฏิเสธหากไม่ตรงกัน.',
        'หากมี timestamp/nonce ให้บังคับช่วงความสดใหม่ที่แคบเพื่อลดความเสี่ยง replay และเก็บ ID ที่ประมวลผลแล้วเพื่อหลีกเลี่ยงข้อมูลซ้ำ.',
    ],

    'example_payload' => [
        'eventType' => 'ORDER_COMPLETED',
        'data'      => [
            'orderId'  => 'BWP-001',
            'status'   => 'COMPLETED',
            'amount'   => 25000,
            'currency' => 'IDR',
        ],
        'provider' => 'amazon_bwp',
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
            'path'   => '/api/webhooks/amazon_bwp',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
