#!/bin/sh

puppet module install puppetlabs-concat --version 1.2.2
puppet module install puppetlabs-apt --version 1.8
puppet module install saz-ssh
puppet module install jfryman-nginx
puppet module install puppetlabs-postgresql
puppet module install saz-ssh
puppet module install saz-memcached
puppet module install puppetlabs-git
puppet module install example42-yum
puppet module install Slashbunny-phpfpm
puppet module install cornfeedhobo-nano
puppet module install thomasvandoren-redis
puppet module install puppetlabs-rabbitmq
puppet module install garethr-erlang
puppet module install ajcrowe-supervisord
puppet module install cornfeedhobo-nano
puppet module install camptocamp-postfix

# ssh root@%host% "puppet apply --pluginsync --verbose --environment prod --hiera_config=/vagrant/hiera.yaml /vagrant/manifests/"