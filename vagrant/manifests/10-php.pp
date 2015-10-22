augeas { 'php_config':
    context => '/files/etc/php.ini/PHP',
    notify  => Service[php-fpm],
    changes => [
        'set date.timezone UTC',
        'set memory_limit 512M',
        'set max_execution_time 3600',
        'set short_open_tag off',
        'set session.save_handler memcached',
        'set session.save_path localhost:11211'
    ]
}
