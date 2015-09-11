# PostgreSQL variables
$postgres_username = hiera('postgres_username')
$postgres_password = hiera('postgres_password')

# PHP variables
$php_packages = hiera_array('php::packages')