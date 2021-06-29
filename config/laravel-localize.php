<?php

return [
    // Use middleware for set app locale
    'use_set_locale_middleware' => true,
    'omit_prefix_for_default' => false,
    'default_redirect' => true,

    // url prefix key
    'locale_keys' => [
        // 'ru' => 'ru_RU',
        // 'uk' => 'uk_UA',
        // 'en' => 'en_US'
    ],
    'default_locale' => null,

    'domain_pattern' => '{}',
];
