<?php

/** @var \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher */

use AKlump\Knowledge\Events\GetVariables;
use AKlump\Knowledge\User\InstallWithComposerVariable;

$dispatcher->addListener(GetVariables::NAME, function (GetVariables $event) {
  (new InstallWithComposerVariable())($event);
});
