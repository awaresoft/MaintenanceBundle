<?php

namespace Awaresoft\MaintenanceBundle\Drivers;

use Lexik\Bundle\MaintenanceBundle\Drivers\FileDriver as BaseFileDriver;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FileDriver extends BaseFileDriver
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager|object
     */
    protected $em;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Constructor
     *
     * @param Translator $translator Translator service
     * @param array $options Options driver
     * @param ContainerInterface $container
     */
    public function __construct($translator, array $options = [], ContainerInterface $container)
    {
        parent::__construct($translator, $options);

        $this->em = $container->get('doctrine')->getManager();
        $this->container = $container;
    }

    /**
     * (non-PHPdoc)
     * @see Lexik\Bundle\MaintenanceBundle\Drivers.AbstractDriver::isExists()
     */
    public function isExists()
    {
        $token = $this->container->get('security.token_storage')->getToken();
        $authChecker = $this->container->get('security.authorization_checker');

        if ($token && $authChecker && $authChecker->isGranted('ROLE_ADMIN')) {
            return false;
        }

        if (file_exists($this->filePath)) {
            if (isset($this->options['ttl']) && is_numeric($this->options['ttl'])) {
                $this->isEndTime($this->options['ttl']);
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * (non-PHPdoc)
     * @see Lexik\Bundle\MaintenanceBundle\Drivers.AbstractDriver::createLock()
     */
    protected function createLock()
    {
        $parent = parent::createLock();

        if (!$parent) {
            return false;
        }

        $maintenance = $this->getSetting('MAINTENANCE');
        $maintenance->setEnabled(true);
        $this->em->persist($maintenance);
        $this->em->flush($maintenance);

        return true;
    }

    /**
     * (non-PHPdoc)
     * @see Lexik\Bundle\MaintenanceBundle\Drivers.AbstractDriver::createUnlock()
     */
    protected function createUnlock()
    {
        $parent = parent::createUnlock();

        if (!$parent) {
            return false;
        }

        $maintenance = $this->getSetting('MAINTENANCE');
        $maintenance->setEnabled(false);
        $this->em->persist($maintenance);
        $this->em->flush($maintenance);

        return true;
    }

    /**
     * @param string $name
     *
     * @return \Awaresoft\SettingBundle\Entity\Setting
     */
    protected function getSetting($name)
    {
        return $this->container->get('awaresoft.setting')->get($name);
    }
}
