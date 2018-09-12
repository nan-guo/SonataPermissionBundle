<?php

namespace Prodigious\Sonata\PermissionBundle\Admin\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Form\FormMapper;
use Prodigious\Sonata\PermissionBundle\Form\Type\SecurityRolesType;

class ProdigiousSonataPermissionAdminExtension extends AbstractAdminExtension
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Constructor
     *
     * @param RequestStack $requestStack
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $autoReplace = $this->container->getParameter('prodigious_sonata_permission.auto_replace_roles_field');

        if($autoReplace) {
            $keys = $formMapper->keys();
            foreach ($keys as $key) {
                $field = $formMapper->get($key);
                $type = $field->getType()->getBlockPrefix();
                if($type == 'sonata_security_roles') {
                    $formMapper->remove($key);
                    $formMapper->tab('Security')
                        ->with('Permission', ['class' => 'col-md-12'])
                            ->add('roles', SecurityRolesType::class, [
                                'label' => false,
                                'expanded' => true,
                                'multiple' => true,
                                'required' => false,
                            ])
                        ->end()
                    ->end();
                }
            }
        }
    }
}