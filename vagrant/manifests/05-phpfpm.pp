class { 'phpfpm':
    package_name => php56w-fpm,
    process_max  => 20,
    log_level    => 'warning',
    error_log    => '/var/log/phpfpm.log',
}

phpfpm::pool { 'www':
    user                   => hiera('php_fpm_user'),
    group                  => hiera('php_fpm_group'),
    listen                 => '127.0.0.1:9000',
    listen_allowed_clients => '127.0.0.1',
    pm                     => 'dynamic',
    pm_max_children        => 10,
    pm_start_servers       => 4,
    pm_min_spare_servers   => 2,
    pm_max_spare_servers   => 6,
    pm_max_requests        => 500,
    pm_status_path         => '/status',
    ping_path              => '/ping',
    ping_response          => 'pong',
    env                    => {
        'ODBCINI' => '"/etc/odbc.ini"',
    },

    php_admin_flag         => {
        'expose_php' => 'Off',
    },

    php_admin_value        => {
        'max_execution_time' => '300',
        'log_errors' => 'On',
        'log_errors' => '/var/log/php-fpm/error.log',
    },
}