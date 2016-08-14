<?php

namespace Awaresoft\MaintenanceBundle\DataFixtures\ORM;

use Awaresoft\Doctrine\Common\DataFixtures\AbstractFixture as AwaresoftAbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Awaresoft\SettingBundle\Entity\Setting;
use Awaresoft\SettingBundle\Entity\SettingHasField;

/**
 * Class LoadMaintenanceData
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class LoadMaintenanceData extends AwaresoftAbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }

    /**
     * {@inheritDoc}
     */
    public function getEnvironments()
    {
        return array('dev', 'prod');
    }

    /**
     * {@inheritDoc}
     */
    public function doLoad(ObjectManager $manager)
    {
        // maintenance setting
        $setting = new Setting();
        $setting
            ->setName('MAINTENANCE')
            ->setEnabled(0)
            ->setHidden(1)
            ->setInfo('Maintenance parameters. If enabled maintenance mode is running.');
        $manager->persist($setting);

        $settingField = new SettingHasField();
        $settingField->setSetting($setting);
        $settingField->setName('MAINTENANCE_PATH');
        $settingField->setInfo('Optional. Authorized path, accepts regexs, e.g. /path');
        $settingField->setValue('(admin|_profiler|_wdt|js)/(.*)');
        $settingField->setEnabled(true);
        $manager->persist($settingField);

        $settingField = new SettingHasField();
        $settingField->setSetting($setting);
        $settingField->setName('MAINTENANCE_HOST');
        $settingField->setInfo('Optional. Authorized domain, accepts regexs, e.g. your-domain.com');
        $manager->persist($settingField);

        $settingField = new SettingHasField();
        $settingField->setSetting($setting);
        $settingField->setName('MAINTENANCE_IPS');
        $settingField->setInfo('Optional. Authorized ip addresses, e.g. [\'127.0.0.1\', \'172.123.10.14\']');
        $manager->persist($settingField);

        $settingField = new SettingHasField();
        $settingField->setSetting($setting);
        $settingField->setName('MAINTENANCE_QUERY');
        $settingField->setInfo('Optional. Authorized request query parameter (GET/POST), e.g. { foo: bar }');
        $manager->persist($settingField);

        $settingField = new SettingHasField();
        $settingField->setSetting($setting);
        $settingField->setName('MAINTENANCE_ROUTE');
        $settingField->setInfo('Optional. Authorized route name, e.g. /admin');
        $manager->persist($settingField);

        $settingField = new SettingHasField();
        $settingField->setSetting($setting);
        $settingField->setName('MAINTENANCE_ATTRIBUTES');
        $settingField->setInfo('Optional. Authorized route attributes, e.g. { id: 2 }');
        $manager->persist($settingField);

        $manager->flush();
    }
}
