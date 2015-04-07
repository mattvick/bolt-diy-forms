<?php

use Bolt\Extension\Bolt\DiyForms\Extension;

if (isset($app)) {
    $app['extensions']->register(new Extension($app));
}
