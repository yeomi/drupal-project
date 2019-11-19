<?php

/**
 * @file
 * Contains \DrupalProject\composer\GdScriptHandler.
 */

namespace DrupalProject\composer;

use Composer\Script\Event;
use DrupalFinder\DrupalFinder;
use Symfony\Component\Filesystem\Filesystem;

class GdScriptHandler extends ScriptHandler {


  private static function createDirectories($dirs, $parent = '/') {

    $fs = new Filesystem();
    $drupalFinder = new DrupalFinder();
    $drupalFinder->locateRoot(getcwd());
    $drupalRoot = $drupalFinder->getDrupalRoot();
    $composerRoot = $drupalFinder->getComposerRoot();

    foreach ($dirs as $dir => $subDirs) {
      if (!$fs->exists($composerRoot . $parent . $dir)) {
        $fs->mkdir($composerRoot . $parent . $dir);
        $fs->touch($composerRoot . $parent . $dir . '/.gitkeep');
      }
      self::createDirectories($subDirs, $parent . $dir . '/');
    }
  }

  public static function createRequiredFiles(Event $event) {

    parent::createRequiredFiles($event);

    $fs = new Filesystem();
    $drupalFinder = new DrupalFinder();
    $drupalFinder->locateRoot(getcwd());
    $drupalRoot = $drupalFinder->getDrupalRoot();
    $composerRoot = $drupalFinder->getComposerRoot();

    $dirs = [
      'private' => [],
      'temp' => [],
      'doc' => [],
      'config' => [
        'dev' => [],
        'prod' => [],
        'preprod' => [],
        'ignore' => [],
        'sync' => [],
      ],
    ];

    self::createDirectories($dirs);

    if (!$fs->exists($drupalRoot . '/themes/custom/app_theme') && $fs->exists($composerRoot . '/vendor/php-packages/drupal8-theme')) {
      $fs->mirror($composerRoot . '/vendor/php-packages/drupal8-theme', $drupalRoot . '/themes/custom/app_theme');
      $event->getIO()->write("Created a starter app_theme");
    }
  }

}
