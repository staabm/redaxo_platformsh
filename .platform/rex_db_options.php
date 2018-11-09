<?php

// Platform.sh DB config
$relationships = getenv('PLATFORM_RELATIONSHIPS');
if ($relationships) {
    $relationships = json_decode(base64_decode($relationships), true);
    foreach ($relationships['database'] as $endpoint) {
        if (empty($endpoint['query']['is_master'])) {
            continue;
        }
		
		`php bin/console db:set-connection-options --host=$endpoint['host'] --login=$endpoint['username'] --password=$endpoint['password'] --database=$endpoint['path']`
    }
}