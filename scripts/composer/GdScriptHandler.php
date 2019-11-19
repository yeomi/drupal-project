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


  private static function createDirectories($dirs) {

    $fs = new Filesystem();
    $drupalFinder = new DrupalFinder();
    $drupalFinder->locateRoot(getcwd());
    $drupalRoot = $drupalFinder->getDrupalRoot();

    foreach ($dirs as $dir => $subDirs) {
      if (!$fs->exists($drupalRoot . '/'. $dir)) {
        $fs->mkdir($drupalRoot . '/'. $dir);
        $fs->touch($drupalRoot . '/'. $dir . '/.gitkeep');
      }
      self::createDirectories($subDirs);
    }
  }

  public static function createRequiredFiles(Event $event) {

    parent::createRequiredFiles($event);

    $fs = new Filesystem();
    $drupalFinder = new DrupalFinder();
    $drupalFinder->locateRoot(getcwd());
    $drupalRoot = $drupalFinder->getDrupalRoot();

    $dirs = [
      'private' => [],
      'temp' => [],
      'config' => [
        'dev' => [],
        'prod' => [],
        'preprod' => [],
        'ignore' => [],
        'sync' => [],
      ],
    ];

    self::createDirectories($dirs);

    if (!$fs->exists($drupalRoot . '/themes/custom/app_theme') && $fs->exists($drupalFinder->getComposerRoot() . '/vendor/php-packages/drupal8-theme')) {
      $fs->mirror($drupalFinder->getComposerRoot() . '/vendor/php-packages/drupal8-theme', $drupalRoot . '/themes/custom/app_theme');
      $event->getIO()->write("Created a starter app_theme");
    }
  }



}
