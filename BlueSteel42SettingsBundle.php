<?php

namespace BlueSteel42\SettingsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class BlueSteel42SettingsBundle extends Bundle
{
    public function getContainerExtension()
    {
        $class = $this->getContainerExtensionClass();
        return new $class();
    }
}
