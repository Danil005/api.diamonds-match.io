<?php

/*
|--------------------------------------------------------------------------
| Validation Language Lines
|--------------------------------------------------------------------------
|
| The following language lines contain the default error messages used by
| the validator class. Some of these rules have multiple versions such
| as the size rules. Feel free to tweak each of these messages here.
|
*/

return [
    'accepted'             => 'त्या फिल्डला स्वीकारली पाहिजे.',
    'active_url'           => 'हे वैध युआरएल नाही.',
    'after'                => 'या :date नंतर तारीख असणे आवश्यक आहे.',
    'after_or_equal'       => 'या नंतर किंवा समान एक तारीख असणे आवश्यक आहे :date.',
    'alpha'                => 'हे फील्ड केवळ अक्षरे असू शकतात.',
    'alpha_dash'           => 'हे फील्ड केवळ अक्षरे असू शकतात, अंक, डॅश आणि अंडरस्कोरसोर.',
    'alpha_num'            => 'हे फील्ड केवळ अक्षरे आणि क्रमांक असू शकतात.',
    'array'                => 'अरे असणे आवश्यक आहे.',
    'attached'             => 'हे फील्ड आधीच संलग्न आहे.',
    'before'               => 'या :date आधी एक तारीख असणे आवश्यक आहे.',
    'before_or_equal'      => 'या आधी एक तारीख किंवा :date समान असणे आवश्यक आहे.',
    'between'              => [
        'array'   => 'This content must have between :min and :max items.',
        'file'    => 'This file must be between :min and :max kilobytes.',
        'numeric' => 'This value must be between :min and :max.',
        'string'  => 'This string must be between :min and :max characters.',
    ],
    'boolean'              => 'या क्षेत्रात खरे किंवा खोटे असणे आवश्यक आहे.',
    'confirmed'            => 'पुष्टीकरण जुळत नाही.',
    'date'                 => 'ही एक वैध तारीख नाही.',
    'date_equals'          => 'या :date समान तारीख असणे आवश्यक आहे.',
    'date_format'          => 'या स्वरूपात जुळत नाही :format.',
    'different'            => 'हे मूल्य विविध असणे आवश्यक आहे :other.',
    'digits'               => 'या असणे आवश्यक आहे :digits अंक.',
    'digits_between'       => 'या दरम्यान असणे आवश्यक आहे :min आणि :max अंक.',
    'dimensions'           => 'ही प्रतिमा अवैध परिमाणे आहेत.',
    'distinct'             => 'या क्षेत्रात हुबेहुब मूल्य आहे.',
    'email'                => 'हा ग्राह्य ई-मेल पत्ता असेल.',
    'ends_with'            => 'खालील एक समाप्त करणे आवश्यक आहे: :values.',
    'exists'               => 'निवडलेली मूल्य अवैध आहे.',
    'file'                 => 'सामग्री फाइल असणे आवश्यक आहे.',
    'filled'               => 'ह्या फिल्डमध्ये व्हॅल्यू असायला हवी.',
    'gt'                   => [
        'array'   => 'The content must have more than :value items.',
        'file'    => 'The file size must be greater than :value kilobytes.',
        'numeric' => 'The value must be greater than :value.',
        'string'  => 'The string must be greater than :value characters.',
    ],
    'gte'                  => [
        'array'   => 'The content must have :value items or more.',
        'file'    => 'The file size must be greater than or equal :value kilobytes.',
        'numeric' => 'The value must be greater than or equal :value.',
        'string'  => 'The string must be greater than or equal :value characters.',
    ],
    'image'                => 'प्रतिमा असणे आवश्यक आहे.',
    'in'                   => 'निवडलेली मूल्य अवैध आहे.',
    'in_array'             => 'हे मूल्य :other अस्तित्वात नाही.',
    'integer'              => 'हा पूर्णांक असणे आवश्यक आहे.',
    'ip'                   => 'हा एक वैध आंतरजाल पत्ता असणे आवश्यक आहे.',
    'ipv4'                 => 'हा एक वैध आयपीव्ही4 पत्ता असणे आवश्यक आहे.',
    'ipv6'                 => 'या वैध आंतरजाल6 पत्ता असणे आवश्यक आहे.',
    'json'                 => 'या असणे आवश्यक आहे एक वैध JSON स्ट्रिंग.',
    'lt'                   => [
        'array'   => 'The content must have less than :value items.',
        'file'    => 'The file size must be less than :value kilobytes.',
        'numeric' => 'The value must be less than :value.',
        'string'  => 'The string must be less than :value characters.',
    ],
    'lte'                  => [
        'array'   => 'The content must not have more than :value items.',
        'file'    => 'The file size must be less than or equal :value kilobytes.',
        'numeric' => 'The value must be less than or equal :value.',
        'string'  => 'The string must be less than or equal :value characters.',
    ],
    'max'                  => [
        'array'   => 'The content may not have more than :max items.',
        'file'    => 'The file size may not be greater than :max kilobytes.',
        'numeric' => 'The value may not be greater than :max.',
        'string'  => 'The string may not be greater than :max characters.',
    ],
    'mimes'                => 'हा प्रकार एक फाइल असणे आवश्यक आहे: :values.',
    'mimetypes'            => 'हा प्रकार एक फाइल असणे आवश्यक आहे: :values.',
    'min'                  => [
        'array'   => 'The value must have at least :min items.',
        'file'    => 'The file size must be at least :min kilobytes.',
        'numeric' => 'The value must be at least :min.',
        'string'  => 'The string must be at least :min characters.',
    ],
    'multiple_of'          => 'मूल्य :value अनेक असणे आवश्यक आहे',
    'not_in'               => 'निवडलेली मूल्य अवैध आहे.',
    'not_regex'            => 'या स्वरूपात अवैध आहे.',
    'numeric'              => 'हा एक नंबर असणे आवश्यक आहे.',
    'password'             => 'गुप्तशब्द अयोग्य आहे.',
    'present'              => 'या क्षेत्रात उपस्थित असणे आवश्यक आहे.',
    'prohibited'           => 'हे फील्ड प्रतिबंधित आहे.',
    'prohibited_if'        => ':other :value आहे तेव्हा हे फील्ड प्रतिबंधित आहे.',
    'prohibited_unless'    => 'या क्षेत्रात प्रतिबंधित आहे तोपर्यंत :other आहे :values.',
    'regex'                => 'या स्वरूपात अवैध आहे.',
    'relatable'            => 'हे फील्ड या संसाधन संबद्ध केले जाऊ शकत नाही.',
    'required'             => 'या क्षेत्रात आवश्यक आहे.',
    'required_if'          => 'या क्षेत्रात :other :value असताना आवश्यक आहे.',
    'required_unless'      => ':other मध्ये आहे तोपर्यंत हा क्षेत्रात आवश्यक आहे :values.',
    'required_with'        => 'या क्षेत्रात :values उपस्थित आहे, तेव्हा आवश्यक आहे.',
    'required_with_all'    => 'हे फील्ड :values उपस्थित असतात तेव्हा आवश्यक आहे.',
    'required_without'     => 'या क्षेत्रात :values उपस्थित नाही तेव्हा आवश्यक आहे.',
    'required_without_all' => 'या क्षेत्रात आवश्यक आहे, तेव्हा काहीही :values उपस्थित आहेत.',
    'same'                 => 'या क्षेत्रात मूल्य एक जुळणे आवश्यक आहे :other.',
    'size'                 => [
        'array'   => 'The content must contain :size items.',
        'file'    => 'The file size must be :size kilobytes.',
        'numeric' => 'The value must be :size.',
        'string'  => 'The string must be :size characters.',
    ],
    'starts_with'          => 'या खालीलपैकी एक सुरू करणे आवश्यक आहे: :values.',
    'string'               => 'ही स्ट्रिंग असायला हवी.',
    'timezone'             => 'या वैध क्षेत्र असणे आवश्यक आहे.',
    'unique'               => 'हे आधीच घेतले गेले आहे.',
    'uploaded'             => 'या अपलोड करण्यासाठी अयशस्वी.',
    'url'                  => 'या स्वरूपात अवैध आहे.',
    'uuid'                 => 'या असणे आवश्यक आहे एक वैध फ्लुइडNAME.',
    'custom'               => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],
    'attributes'           => [],
];
