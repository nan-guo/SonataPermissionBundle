<?php

namespace Prodigious\Sonata\PermissionBundle\Security;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Sonata\AdminBundle\Admin\Pool;

class EditableRolesBuilder
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var Pool
     */
    protected $pool;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param TokenStorageInterface             $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param Pool                     $pool
     */
    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker, Pool $pool, TranslatorInterface $translator)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->pool = $pool;
        $this->container = $pool->getContainer();
        $this->translator = $translator;
    }
    /**
     * @return array
     */
    public function getRoles()
    {
        $roles = [];
        $rolesList = [];
        $defaultRoles = [];

        if (!$this->tokenStorage->getToken()) {
            return [$roles, $rolesList, $defaultRoles];
        }

        if($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $groups = $this->container->getParameter('prodigious_sonata_permission.groups');
            $defaultRoles = $this->container->getParameter('prodigious_sonata_permission.default_roles');

            $i = 0;

            foreach ($groups as $key => $group) {
                $roles[$i]['label'] = $this->translator->trans($group['label'], [], $group['translation_domain']);

                foreach ($group['items'] as $item) {
                                        
                    switch ($item['type']) {
                        case 'role':
                            $label = $item['label'] ?? $item['name'];
                            $label = $this->translator->trans($label, [], $item['translation_domain']);
                            $roles[$i]['items'][$label]['permissions'][ucfirst($label)] = $item['name'];
                            $roles[$i]['items'][$label]['type'] = $item['type'];
                            $rolesList[] = $item['name'];
                            break;
                        case 'entity':
                            $admin = $this->pool->getInstance($item['name']);
                            $label = $item['label'] ?? $this->translator->trans($admin->getLabel(), [], $admin->getTranslationDomain());
                            $nameFromServiceId = $this->getNameFromServiceId($item['name']);
                            foreach ($item['permissions'] as $permissionLabel => $permission) {
                                $permissionLabel = is_integer($permissionLabel) ? $permission : $permissionLabel;
                                $roles[$i]['items'][$label]['permissions'][ucfirst($permissionLabel)] = 'ROLE_'.$nameFromServiceId.'_'.$permission;
                                $roles[$i]['items'][$label]['type'] = $item['type'];
                                $rolesList[] = 'ROLE_'.$nameFromServiceId.'_'.$permission;
                            }

                            break;
                        default:
                            break;
                    }

                }

                $i ++;
            }

        }

        return [$roles, $rolesList, $defaultRoles];
    }

    /**
     * Get pool
     *
     * @return Pool $pool
     */
    public function getPool()
    {
        return $this->pool;
    }

    /**
     * Get translator
     *
     * @return TranslatorInterface $translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Generate permission prefix
     */
    public function getNameFromServiceId($service)
    {
        return strtoupper(str_replace('.', '_', $service));
    }
}