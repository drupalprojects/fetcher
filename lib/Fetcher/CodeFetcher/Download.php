<?php

namespace Fetcher\CodeFetcher;
use Symfony\Component\Process\Process;

class Download implements \Fetcher\CodeFetcher\SetupInterface {

  private $site = FALSE;

  public function __construct(\Fetcher\Site $site) {
    $this->site = $site;
  }

  /**
   * Implements \Fetcher\CodeFetcher\SetupInterface::Setup().
   */
  public function setup() {

    $site = $this->site;

    $profile = 'drupal-' . $site['version'];
    if ($site['profile'] != 'standard' && $site['profile'] != 'minimal') {
      $profile = $site['profile'];
    }

    $commandline_args = array(
      $profile,
    );
    $commandline_options = array(
      // Default our package hander to git_drupalorg.
      //'--package-handler=' . drush_get_option('package-handler', 'git_drupalorg'),
      '--destination=' . $site['site.working_directory'],
      '--drupal-project-rename=code',
      '--default-major=' . $site['version'],
    );
    foreach ($commandline_options as $name => $value) {
      drush_set_option($name, $value);
    }

    if ($this->container['verbose']) {
      $commandline_options[] = '--verbose';
      $command = 'drush dl ' . implode(' ', $commandline_args) . ' ' . implode(' ', $commandline_options);
      drush_log(dt('Executing: `!command`. ', array('!command' => $command)), 'ok');
    }
    if (!drush_invoke_process('@none', 'dl', $commandline_args, $commandline_options)) {
      throw new \Fetcher\Exception\FetcherException('Database syncronization FAILED!');
    }
  }

}

