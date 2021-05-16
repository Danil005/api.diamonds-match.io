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
    'accepted'             => 'Mae\'r maes hwn yn rhaid i gael eu derbyn.',
    'active_url'           => 'Nid yw hyn yn ddilys URL.',
    'after'                => 'Rhaid i hwn fod yn ddyddiad ar ôl :date.',
    'after_or_equal'       => 'Rhaid i hwn fod yn ddyddiad ar ôl neu yn hafal i :date.',
    'alpha'                => 'Y maes hwn efallai y dim ond cynnwys llythyrau.',
    'alpha_dash'           => 'Y maes hwn mai dim ond yn cynnwys llythrennau, rhifau, llinellau toriad ac yn tanlinellu.',
    'alpha_num'            => 'Y maes hwn mai dim ond yn cynnwys llythrennau a rhifau.',
    'array'                => 'Mae hyn yn maes rhaid i fod yn arae.',
    'attached'             => 'Mae\'r maes hwn eisoes yn ynghlwm.',
    'before'               => 'Rhaid i hwn fod yn ddyddiad cyn :date.',
    'before_or_equal'      => 'Rhaid i hwn fod yn ddyddiad cyn neu yn hafal i :date.',
    'between'              => [
        'array'   => 'This content must have between :min and :max items.',
        'file'    => 'This file must be between :min and :max kilobytes.',
        'numeric' => 'This value must be between :min and :max.',
        'string'  => 'This string must be between :min and :max characters.',
    ],
    'boolean'              => 'Mae hyn yn maes rhaid i fod yn wir neu anwir.',
    'confirmed'            => 'Mae\'r cadarnhad nad yw\'n cyd-fynd.',
    'date'                 => 'Nid yw hyn yn ddilys ar y dyddiad.',
    'date_equals'          => 'Rhaid i hwn fod yn ddyddiad yn hafal i :date.',
    'date_format'          => 'Nid yw hyn yn cyfateb i\'r fformat :format.',
    'different'            => 'Y gwerth hwn mae\'n rhaid i fod yn wahanol :other.',
    'digits'               => 'Rhaid i hyn fod yn :digits digid.',
    'digits_between'       => 'Rhaid i hyn fod rhwng :min a :max digid.',
    'dimensions'           => 'Mae\'r ddelwedd hon wedi annilys dimensiynau.',
    'distinct'             => 'Y maes hwn wedi dyblyg gwerth.',
    'email'                => 'Rhaid i hyn fod yn gyfeiriad e-bost dilys.',
    'ends_with'            => 'Mae\'n rhaid i hyn i ben gydag un o\'r canlynol: :values.',
    'exists'               => 'Mae\'r gwerth a ddewiswyd yn annilys.',
    'file'                 => 'Mae\'n rhaid i\'r cynnwys fod yn ffeil.',
    'filled'               => 'Mae\'r maes hwn yn rhaid ei gael ar werth.',
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
    'image'                => 'Rhaid i hyn fod yn lun.',
    'in'                   => 'Mae\'r gwerth a ddewiswyd yn annilys.',
    'in_array'             => 'Mae hyn yn werth nid yw\'n bodoli yn :other.',
    'integer'              => 'Rhaid i hyn fod yn gyfanrif.',
    'ip'                   => 'Rhaid i hwn fod yn ddilys cyfeiriad IP.',
    'ipv4'                 => 'Rhaid i hwn fod yn ddilys ar IPv4 cyfeiriad.',
    'ipv6'                 => 'Rhaid i hwn fod yn ddilys cyfeiriad IPv6.',
    'json'                 => 'Rhaid i hwn fod yn ddilys JSON llinyn.',
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
    'mimes'                => 'Rhaid i hyn fod y ffeil o\'r math: :values.',
    'mimetypes'            => 'Rhaid i hyn fod y ffeil o\'r math: :values.',
    'min'                  => [
        'array'   => 'The value must have at least :min items.',
        'file'    => 'The file size must be at least :min kilobytes.',
        'numeric' => 'The value must be at least :min.',
        'string'  => 'The string must be at least :min characters.',
    ],
    'multiple_of'          => 'Mae\'r gwerth rhaid iddo fod yn lluosrif o :value',
    'not_in'               => 'Mae\'r gwerth a ddewiswyd yn annilys.',
    'not_regex'            => 'Mae\'r fformat hwn yn annilys.',
    'numeric'              => 'Rhaid i hyn fod yn rhif.',
    'password'             => 'Mae\'r cyfrinair yn anghywir.',
    'present'              => 'Mae hyn yn maes rhaid i fod yn bresennol.',
    'prohibited'           => 'Y maes hwn yn cael ei wahardd.',
    'prohibited_if'        => 'Y maes hwn yn cael ei wahardd pan :other yn :value.',
    'prohibited_unless'    => 'Y maes hwn yn cael ei wahardd oni bai :other yn :values.',
    'regex'                => 'Mae\'r fformat hwn yn annilys.',
    'relatable'            => 'Y maes hwn gall nid yw fod yn gysylltiedig â\'r adnodd hwn.',
    'required'             => 'Mae\'r maes hwn yn ofynnol.',
    'required_if'          => 'Mae\'r maes hwn yn ofynnol pan fydd :other yn :value.',
    'required_unless'      => 'Mae\'r maes hwn yn ofynnol oni bai bod :other yn :values.',
    'required_with'        => 'Mae\'r maes hwn yn ofynnol pan fydd :values yn bresennol.',
    'required_with_all'    => 'Mae\'r maes hwn yn ofynnol pan fydd :values yn bresennol.',
    'required_without'     => 'Mae\'r maes hwn yn ofynnol pan fydd :values yn bresennol.',
    'required_without_all' => 'Mae\'r maes hwn yn ofynnol pan gaiff unrhyw un o\'r :values yn bresennol.',
    'same'                 => 'Gwerth y maes hwn mae\'n rhaid i gyfateb i\'r un o :other.',
    'size'                 => [
        'array'   => 'The content must contain :size items.',
        'file'    => 'The file size must be :size kilobytes.',
        'numeric' => 'The value must be :size.',
        'string'  => 'The string must be :size characters.',
    ],
    'starts_with'          => 'Rhaid i hyn ddechrau gydag un o\'r canlynol: :values.',
    'string'               => 'Rhaid i hyn fod yn llinyn.',
    'timezone'             => 'Rhaid i hwn fod yn ddilys parth.',
    'unique'               => 'Mae hyn eisoes wedi eu cymryd.',
    'uploaded'             => 'Mae hyn yn methu â llwytho i fyny.',
    'url'                  => 'Mae\'r fformat hwn yn annilys.',
    'uuid'                 => 'Rhaid i hwn fod yn ddilys UUID.',
    'custom'               => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],
    'attributes'           => [],
];