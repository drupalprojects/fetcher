<?php

namespace Fetcher\FilePermissions;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

class FilePerms {

  protected $site = NULL;

  public function __construct(Pimple $site) {
    $this->site = $site;
  }

  public function fixPerms() {
    $site = $this->site;
    $commandline_options = array('backend');
    if ($site['verbose']) {
      $commandline_options[] = '--verbose';
    }
    $commandline_args = array(
      // TODO: Support multisite?
      sprintf('@%s.%s:%s', $site['name'], $site['environment'], '%files'),
      sprintf('@%s.local:%s', $site['name'], '%files'),
    );
    $args = array(
      'File directory path',
      'Private file directory path',
      'Drupal root',
    );
    $site['log'](dt('Local file path information:'), 'ok');
    $local_status_result = drush_invoke_process('@' . $site['name'] . '.local', 'status', $args, $commandline_options);
    $local_root_path = $local_status_result['object']['Drupal root'];
    $local_public_files = $local_status_result['object']['File directory path'];
    $local_private_files = !empty($local_status_result['object']['Private file directory path']) ? $local_status_result['object']['Private file directory path'] : '';

    $webroot_files = new Finder();
    $webroot_files->files()->in($local_root_path)->ignoreVCS(TRUE);
    foreach ($webroot_files as $file) {
      $command = "chmod $file u=rw,g=r,o=";
      $process = $site['process']($command);
      $process->setTimeout(null);
      $process->run();
    }
    $webroot_dirs = new Finder();
    $webroot_dirs->directories()->in($local_root_path)->ignoreVCS(TRUE);
    foreach ($webroot_dirs as $dir) {
      $command = "chmod $dir chmod u=rwx,g=rx,o=";
      $process = $site['process']($command);
      $process->setTimeout(null);
      $process->run();
    }
    if (!empty($local_public_files)) {
      $local_public_files = new Finder();
      $public_files->files()->in($local_public_files)->ignoreVCS(TRUE);
      foreach ($public_files as $file) {
        $command = "chmod $file u=rw,g=r,o=";
        $process = $site['process']($command);
        $process->setTimeout(null);
        $process->run();
      }
      $public_dirs = new Finder();
      $public_dirs->directories()->in($local_public_files)->ignoreVCS(TRUE);
      foreach ($public_dirs as $dir) {
        $command = "chmod $dir chmod u=rwx,g=rx,o=";
        $process = $site['process']($command);
        $process->setTimeout(null);
        $process->run();
      }
    }

    // Handle the private files if they exist.
    if (!empty($local_private_files)) {
      $local_private_files = new Finder();
      $private_files->files()->in($local_private_files)->ignoreVCS(TRUE);
      foreach ($public_files as $file) {
        $command = "chmod $file u=rw,g=r,o=";
        $process = $site['process']($command);
        $process->setTimeout(null);
        $process->run();
      }
      $private_dirs = new Finder();
      $private_dirs->directories()->in($local_private_files)->ignoreVCS(TRUE);
      foreach ($private_dirs as $dir) {
        $command = "chmod $dir chmod u=rwx,g=rx,o=";
        $process = $site['process']($command);
        $process->setTimeout(null);
        $process->run();
      }
    }
  }
}
