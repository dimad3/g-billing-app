<?php

// $legal_form = $chanceOfGettingTrue ? 'SIA' : fake()->randomElement(['AS', 'ZS', 'IK', 'Biedrība', 'Kooperatīvā sabiedrība', 'Eiropas komercsabiedrība', 'Zvērinātu advokātu birojs', 'Pilnsabiedrība', 'Komandītsabiedrība', 'Komersanta filiāle', 'Fonds', 'Individuālais uzņēmums', 'Valsts iestāde', 'Pašvaldības iestāde',]);
// $legal_form = fake()->randomElement(['Fiziskā persona', 'Saimnieciskās darbības veicējs']);
return [
    'entity_types' => [
        'legal_entity' => 'Legal Entity',
        'individual' => 'Individual',
    ],

    'legal_forms' => [
        'legal_entity' => [
            'llc' => 'LLC',
            'jsc' => 'JSC',
            'farm' => 'Farm',
            'association' => 'Association',
            'cooperative_society' => 'Cooperative Society',
            'european_company' => 'European Company',
            'general_partnership' => 'General Partnership',
            'limited_partnership' => 'Limited Partnership',
            'merchant_branch' => 'Merchant Branch',
            'foundation' => 'Foundation',
            'individual_enterprise' => 'Individual Enterprise',
            'state_institution' => 'State Institution',
            'municipal_institution' => 'Municipal Institution',
        ],
        'individual' => [
            'natural_person' => 'Natural Person',
            'self_employed' => 'Self-employed',
            'sole_proprietorship' => 'Sole Proprietorship',
        ],
    ],

    'document_types' => [
        'invoice' => 'Invoice', // Rēķins
        'adv_inv' => 'Advance Invoice', // Avansa rēķins
        'cred_inv' => 'Credit Invoice', // Kredītrēķins
        'goods_del_doc' => 'Goods Delivery Document' // Preču piegādes dokuments
    ],

    'document_statuses' => [
        'draft' => 'Draft',
        'sent' => 'Sent',
        'paid' => 'Paid',
        'overdue' => 'Overdue',
        'cancelled' => 'Cancelled',
        'disputed' => 'Disputed',
    ],

    'roles' => [
        'admin' => 'Admin',
        'user' => 'User',
    ],

    'banks' => [
        ['name' => 'Swedbank, AS', 'bank_code' => 'HABALV22'],
        ['name' => 'Citadele banka, AS', 'bank_code' => 'PARXLV22'],
        ['name' => 'Magnetiq Bank, AS', 'bank_code' => 'LAPBLV2X'],
        ['name' => 'Reģionālā investīciju banka, AS', 'bank_code' => 'RIBRLV22'],
        ['name' => 'Rietumu Banka, AS', 'bank_code' => 'RTMBLV2X'],
        ['name' => 'Bigbank Latvijas filiāle', 'bank_code' => 'BIGKLV21'],
        ['name' => 'SEB banka, AS', 'bank_code' => 'UNLALV2X'],
        ['name' => 'Industra Bank, AS', 'bank_code' => 'MULTLV2X'],
        ['name' => 'Luminor Bank Latvijas filiāle', 'bank_code' => 'RIKOLV2X'],
        ['name' => 'Signet Bank, AS', 'bank_code' => 'LLBBLV2X'],
        ['name' => 'BluOr Bank, AS', 'bank_code' => 'CBBRLV22'],
        ['name' => 'INDEXO Banka, AS', 'bank_code' => 'IDXOLV22'],
    ],
];
