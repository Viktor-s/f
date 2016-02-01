exec { 'install_composer':
    command     => 'curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer',
    require     => Package[php56w-cli],
    cwd         => '/tmp',
    path        => ["/usr/bin", "/bin"],
    environment => 'HOME=/tmp'
}
