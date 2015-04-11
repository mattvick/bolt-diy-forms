<?php

use Bolt\Extension\Mattvick\DiyForms\Extension;

if (isset($app)) {
    $app['extensions']->register(new Extension($app));
}
