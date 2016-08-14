<?php

namespace Awaresoft\MaintenanceBundle\Drivers;

use Lexik\Bundle\MaintenanceBundle\Drivers\DatabaseDriver;
use Lexik\Bundle\MaintenanceBundle\Drivers\DriverFactory as LexikDriverFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\Translator;

/**
 * Factory driver extension
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class DriverFactory extends LexikDriverFactory
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(DatabaseDriver $dbDriver, Translator $trans, array $driverOptions, ContainerInterface $container)
    {
        parent::__construct($dbDriver, $trans, $driverOptions);

        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function getDriver()
    {
        $class = $this->driver['class'];
        if (class_exists($class)) {
            if ($class === self::DATABASE_DRIVER) {
                return $class($this->dbDriver, $this->trans, $this->driver['options'], $this->container);
            }

            return new $class($this->trans, $this->driver['options'], $this->container);
        } else {
            throw new \ErrorException("Class '" . $class . "' not found in " . get_class($this));
        }
    }
}
