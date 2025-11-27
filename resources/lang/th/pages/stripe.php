<?php

return [

    'hint' => 'เฮดเดอร์ลายเซ็นพร้อม timestamp.',

    'summary' => <<<'TEXT'
Stripe เซ็นลายเซ็นให้กับทุกคำขอ webhook และใส่ลายเซ็นที่คำนวณไว้ในเฮดเดอร์ `Stripe-Signature` เอ็นด์พอยต์ของคุณต้องตรวจสอบคำขอก่อนทำงานใด ๆ เมื่อใช้ไลบรารีทางการของ Stripe ให้ส่งอินพุต 3 อย่างเข้าไปในขั้นตอนตรวจสอบ: raw request body ที่ตรงตามที่รับมา, เฮดเดอร์ `Stripe-Signature`, และ endpoint secret ของคุณ ทำต่อเฉพาะเมื่อการตรวจสอบสำเร็จเท่านั้น หากไม่สำเร็จให้ตอบ non-2xx และหยุดประมวลผล หากไม่สามารถใช้ไลบรารีทางการได้ ให้ทำการตรวจสอบแบบ manual ตามเอกสาร รวมถึงการตรวจสอบ tolerance ของ timestamp เพื่อลดความเสี่ยงจาก replay

ให้ถือว่าการตรวจสอบลายเซ็นเป็น gate ที่เข้มงวด ทำให้ handler เป็นแบบ idempotent (เก็บ event ID), ตอบ 2xx ให้เร็วหลังบันทึกข้อมูล และส่งงานหนักไปทำใน background jobs ตรวจสอบให้แน่ใจว่าเฟรมเวิร์กของคุณให้ **raw bytes** ได้—หลีกเลี่ยงการ re-serialize JSON ก่อนทำแฮช เพราะการเปลี่ยนช่องว่างหรือการเรียงลำดับฟิลด์จะทำให้ตรวจสอบลายเซ็นล้มเหลว สุดท้ายให้ log เฉพาะข้อมูลวินิจฉัยขั้นต่ำ (ผลการตรวจสอบ, ประเภทอีเวนต์, แฮชของบอดี—ไม่รวมความลับ) และเฝ้าดูความล้มเหลวระหว่างการหมุน secret หรือการเปลี่ยนเอ็นด์พอยต์
TEXT
    ,

    // dari view Stripe
    'docs' => 'https://docs.stripe.com/webhooks',

    'signature_notes' => [
        'อ่านเฮดเดอร์ `Stripe-Signature`; รับ endpoint secret จาก Stripe dashboard.',
        'ตรวจสอบด้วยไลบรารีทางการโดยส่ง: raw request body, `Stripe-Signature`, และ endpoint secret.',
        'หากตรวจสอบแบบ manual ให้บังคับใช้ timestamp tolerance เพื่อลด replay และเปรียบเทียบลายเซ็นด้วยฟังก์ชัน timing-safe.',
        'ยอมรับเฉพาะเมื่อสำเร็จ; เก็บ event ID เพื่อความเป็น idempotent และตอบ 2xx ให้เร็วหลังบันทึกข้อมูล.',
    ],

    'example_payload' => [
        'id'   => 'evt_' . now()->timestamp,
        'type' => 'payment_intent.succeeded',
        'data' => [
            'object' => [
                'id'     => 'pi_01H',
                'status' => 'succeeded',
            ],
        ],
        'provider'   => 'stripe',
        'created_at' => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/stripe',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
