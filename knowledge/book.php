<?php

/** @var \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher */

$dispatcher->addListener(\AKlump\Knowledge\Events\GetVariables::NAME, function (\AKlump\Knowledge\Events\GetVariables $event) {
  $root = $event->getPathToSource() . '/../';
  $version = exec("cd $root && web_package v");
  if ($version) {
    $event->addVariable('version', $version);
  }
});
