#!/usr/bin/env php
<?php

// Platform.sh DB config
$relationships = getenv('PLATFORM_RELATIONSHIPS');
if ($relationships) {
    $relationships = json_decode(base64_decode($relationships), true);
    foreach ($relationships['database'] as $endpoint) {
        if (empty($endpoint['query']['is_master'])) {
            continue;
        }
        $cmd = sprintf(
            'php bin/console db:set-connection-options --host=%s:%s --login=%s --password=%s --database=%s',
            $endpoint['host'],
            $endpoint['port'],
            $endpoint['username'],
            $endpoint['password'],
            $endpoint['path']
        );
        throw new Exception($cmd);
        system($cmd);
    }
}

throw new Exception(print_r($relationships, true));
