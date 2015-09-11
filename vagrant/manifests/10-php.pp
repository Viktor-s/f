augeas { 'php_config':
    context => '/files/etc/php.ini/PHP',
    notify  => Service[php-fpm],
    changes => [
        'set date.timezone UTC',
        'set memory_limit 128M',
        'set max_execution_time 3600',
        'set short_open_tag off'
    ]
}
