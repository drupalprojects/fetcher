<?php

namespace Fetcher\Configurator;

use \Fetcher\SiteInterface,
    \Fetcher\Task\TaskStack;

class CentOS implements ConfiguratorInterface {

  static public function configure(SiteInterface $site) {
    // TODO: This is really an Apache setting.
    $site['server.webroot'] = '/var/www/html';
    $site['server.user'] = 'apache';
    $site['server.vhost_enabled_folder'] = '/etc/httpd/conf.d';
    $site['server.vhost_available_folder'] = '/etc/httpd/conf.d';
    $site['server.restart_command'] = 'sudo service httpd reload';
    $site['server.disable_site_command'] = function($c) {
      return sprintf('rm %s', $c['server.host_conf_path']);
    };
    // TODO: Make this lie less
    $site['server.enable_site_command'] = 'echo "not functional on centos"';

  }
}
 
