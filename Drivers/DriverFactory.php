<?php

namespace Awaresoft\MaintenanceBundle\Drivers;

use Lexik\Bundle\MaintenanceBundle\Drivers\DriverFactory as LexikDriverFactory;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

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
            throw new \ErrorException(sprintf("Class %s not found in %s", $class, get_class($this)));
        }

        if ($class === static::DATABASE_DRIVER) {
            $driver = $this->dbDriver;
            $driver->setOptions($this->driverOptions['options']);
        } else {
            $driver = new $class($this->container, $this->driverOptions['options']);
        }

        $driver->setTranslator($this->translator);

        return $driver;
    }
}
