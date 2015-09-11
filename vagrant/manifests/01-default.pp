hiera_include('classes')

Class[yum]
-> Package[php56w-fpm]
-> Package[$php_packages]
~> Service[php-fpm]

package { $php_packages:
    ensure => installed,
}

if versioncmp($::puppetversion,'3.6.1') >= 0 {
    $allow_virtual_packages = hiera('allow_virtual_packages',false)
    Package {
        allow_virtual => $allow_virtual_packages,
    }
}

# Development node
node 'furniture.dev' {
#    augeas { 'php_config_xdebug':
#        context => '/files/etc/php.ini/PHP',
#        notify  => Service[php-fpm],
#        changes => [
#            'set xdebug.max_nesting_level 500',
#            'set xdebug.remote_enable On',
#            'set xdebug.remote_autostart Off',
#            'set xdebug.remote_handler dbgp',
#            'set xdebug.remote_connect_back On',
#            'set xdebug.idekey "netbeans-xdebug"'
#        ]
#    }

    augeas { 'php_config_for_symfony':
        context => '/files/etc/php.ini/PHP',
        notify => Service[php-fpm],
        changes => [
            'set realpath_cache_size 4096k',
            'set realpath_cache_ttl 720'
        ]
    }
}

# Default node (Production)
node default {
}