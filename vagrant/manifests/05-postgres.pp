# PostgreSQL configuration
class { 'postgresql::server':
}

postgresql::server::db { "furniture":
    user     => "${postgres_username}",
    password => postgresql_password("${postgres_username}", "${postgres_password}"),
}