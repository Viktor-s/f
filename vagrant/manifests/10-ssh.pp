#file { "/root/.ssh":
#    ensure => directory
#}
#
#file { "/root/.ssh/id_rsa":
#    ensure => present,
#    owner => root,
#    group => root,
#    mode => 600,
#    source => "/vagrant/keys/${domain}/id_rsa"
#}
#
#file { "/root/.ssh/id_rsa.pub":
#    ensure => present,
#    owner  => root,
#    group  => root,
#    mode   => 600,
#    source => "/vagrant/keys/${domain}/id_rsa.pub"
#}
