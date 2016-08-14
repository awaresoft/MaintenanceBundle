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
    public function __construct(array $options = [], ContainerInterface $container)
    {
        parent::__construct($options);

        $this->em = $container->get('doctrine')->getManager();
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function isExists()
    {
        $token = $this->container->get('security.token_storage')->getToken();
        $authChecker = $this->container->get('security.authorization_checker');

        if ($token && $authChecker && $authChecker->isGranted('ROLE_ADMIN')) {
            return false;
        }

        return parent::isExists();
    }

    /**
     * @inheritdoc
     */
    protected function createLock()
    {
        $parent = parent::createLock();

        $maintenance = $this->getSetting('MAINTENANCE');
        $maintenance->setEnabled(true);
        $this->em->persist($maintenance);
        $this->em->flush($maintenance);

        return $parent;
    }

    /**
     * @inheritdoc
     */
    protected function createUnlock()
    {
        $parent = parent::createUnlock();

        $maintenance = $this->getSetting('MAINTENANCE');
        $maintenance->setEnabled(false);
        $this->em->persist($maintenance);
        $this->em->flush($maintenance);

        return $parent;
    }

    /**
     * @inheritdoc
     */
    public function unlock()
    {
        return $this->createUnlock();
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
