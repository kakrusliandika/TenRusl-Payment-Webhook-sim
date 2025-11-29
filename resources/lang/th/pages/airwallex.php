<?php

return [

    'hint' => 'HMAC-SHA256 บน x-timestamp + body.',

    'summary' => <<<'TEXT'
Webhook ของ Airwallex มีการลงลายเซ็นไว้เพื่อให้คุณตรวจสอบได้ทั้งความถูกต้องของแหล่งที่มา (authenticity) และความสมบูรณ์ของข้อมูล (integrity) ก่อนแตะฐานข้อมูล ทุกคำขอจะมีส่วนหัวสำคัญสองตัวคือ `x-timestamp` และ `x-signature` ในการตรวจสอบข้อความ ให้คุณอ่าน HTTP body แบบดิบ (raw) ตามที่ได้รับมาเป๊ะ ๆ จากนั้นนำค่าของ `x-timestamp` (ในรูป string) มาต่อกับ body ดิบดังกล่าวเพื่อใช้เป็นอินพุตสำหรับ digest แล้วคำนวณ HMAC ด้วย SHA-256 โดยใช้ shared secret ของ URL การแจ้งเตือนเป็นกุญแจ Airwallex คาดหวังผลลัพธ์เป็น **hex digest**; ให้นำค่าที่ได้มาเปรียบเทียบกับส่วนหัว `x-signature` ด้วยการเปรียบเทียบแบบเวลาเท่ากัน (constant-time) เพื่อหลีกเลี่ยงการรั่วไหลด้าน timing หากลายเซ็นไม่ตรงกัน หรือ timestamp หายไป/ไม่ถูกต้อง ให้ถือว่าล้มเหลวและส่งคืน response ที่ไม่ใช่ 2xx

เพราะการ replay เป็นความเสี่ยงที่เกิดขึ้นจริงกับระบบ webhook ใด ๆ คุณจึงควรกำหนดช่วงเวลาความสดใหม่ (freshness window) ให้กับ `x-timestamp` ปฏิเสธข้อความที่เก่าเกินไปหรืออยู่ล่วงหน้าในอนาคตมากเกินไป และเก็บ ID ของ event ที่ประมวลผลแล้วเพื่อใช้ลบการทำงานซ้ำของ side-effect ในลำดับต่อไป (idempotency ในเลเยอร์แอปพลิเคชัน) ให้ถือว่า payload ไม่ปลอดภัยจนกว่าการตรวจสอบจะผ่าน อย่า stringify JSON ใหม่ก่อนทำแฮช—ให้ใช้ bytes ดิบตามที่ได้รับมาเพื่อหลีกเลี่ยงความต่างเล็ก ๆ น้อย ๆ เรื่องช่องว่างหรือเรียงลำดับ เมื่อการตรวจสอบสำเร็จ ให้ตอบกลับด้วย `2xx` โดยเร็ว และย้ายงานหนักไปทำแบบ asynchronous เพื่อให้ logic การ retry ทำงานได้ดีและลดความเสี่ยงในการเกิดข้อมูลซ้ำโดยไม่ตั้งใจ

สำหรับการทำงานในสภาพแวดล้อม local และ CI นั้น Airwallex มีเครื่องมือระดับสูงให้ใช้งาน: ตั้งค่า URL การแจ้งเตือนของคุณในแดชบอร์ด ดูตัวอย่าง payload และ **ส่ง event ทดสอบ** ไปยัง endpoint ของคุณได้ เมื่อ debug ให้บันทึก `x-timestamp` ที่ได้รับ พรีวิวของลายเซ็นที่คุณคำนวณได้ (อย่าบันทึก secret ลง log เด็ดขาด) และ ID ของ event ถ้ามี หากคุณทำการหมุนกุญแจลับ (secret key rotation) ให้ทำอย่างระมัดระวังและเฝ้าดูอัตรา error ของลายเซ็น สุดท้ายนี้ควรบันทึกขั้นตอนทั้งชุด—การตรวจสอบ การลบซ้ำ การ retry และ error response—เพื่อให้สมาชิกในทีมสามารถทำซ้ำผลลัพธ์ได้ด้วยกติกา hashing body ดิบ และช่วงเวลาเดียวกัน
TEXT
    ,

    'docs' => 'https://www.airwallex.com/docs/developer-tools/webhooks/listen-for-webhook-events',

    'signature_notes' => [
        'ดึง `x-timestamp` และ `x-signature` จากส่วนหัว (headers).',
        'สร้าง value_to_digest = <x-timestamp> + <HTTP body แบบดิบ> (byte เดิมเป๊ะ).',
        'คำนวณ expected = HMAC-SHA256(value_to_digest, <webhook secret>) ให้อยู่ในรูป HEX; แล้วเปรียบเทียบกับ `x-signature` ด้วยการเปรียบเทียบแบบเวลาเท่ากัน (constant-time).',
        'ปฏิเสธหากลายเซ็นไม่ตรงกันหรือ timestamp หมดอายุ; และลบการซ้ำของ event ID ที่ประมวลผลไปแล้วเพื่อให้เกิด idempotency.',
    ],

    'example_payload' => [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'payment_intent.succeeded',
        'data' => [
            'payment_intent_id' => 'pi_awx_001',
            'amount' => 25000,
            'currency' => 'IDR',
            'status' => 'succeeded',
        ],
        'provider' => 'airwallex',
        'created_at' => now()->toIso8601String(),
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
            'path' => '/api/webhooks/airwallex',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
