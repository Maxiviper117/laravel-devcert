<?php

return [
    'default_tld' => '.test',
    'local_https_domain' => (($domain = getenv('LOCAL_HTTPS_DOMAIN')) !== false && $domain !== '') ? $domain : null,
    'certs_path' => null,
    'hosts_path' => null,
    'vite_config_path' => null,
    'include_ipv6' => true,
    'include_wildcard' => false,
];
