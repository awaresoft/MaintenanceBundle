<?php

namespace Awaresoft\MaintenanceBundle\Drivers;

use Lexik\Bundle\MaintenanceBundle\Drivers\DatabaseDriver;
use Lexik\Bundle\MaintenanceBundle\Drivers\DriverFactory as LexikDriverFactory;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\Translator;

/**
 * Factory driver extension
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class DriverFactory extends LexikDriverFactory
{
    use ContainerAwareTrait;

    /**
     * @inheritdoc
     */
    public function getDriver()
    {
        $class = $this->driverOptions['class'];

        if (!class_exists($class)) {
            throw new \ErrorException("Class '".$class."' not found in ".get_class($this));
        }

        if ($class === static::DATABASE_DRIVER) {
            $driver = $this->dbDriver;
            $driver->setOptions($this->driverOptions['options']);
        } else {
            $driver = new $class($this->driverOptions['options'], $this->container);
        }

        $driver->setTranslator($this->translator);

        return $driver;
    }
}
