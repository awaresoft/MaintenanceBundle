<?php

namespace Awaresoft\MaintenanceBundle\Job;

use Awaresoft\SettingBundle\Entity\Setting;
use Awaresoft\SettingBundle\Entity\SettingHasField;
use Awaresoft\SettingBundle\Job\SettingJobInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * ChangeMaintenanceStatusJob
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class ChangeMaintenanceStatusJob implements SettingJobInterface
{
    /**
     * Change maintenance status
     *
     * @param Setting|SettingHasField $object
     * @param ContainerInterface $container
     *
     * @return void
     */
    public static function run($object, ContainerInterface $container)
    {
        $entityManager = $container->get('doctrine')->getManager();
        $driver = $container->get('lexik_maintenance.driver.factory')->getDriver();

        if ($object->isEnabled()) {
            $driver->lock();
        } else {
            $driver->unlock();
        }

        $entityManager->flush($object);
    }
}
