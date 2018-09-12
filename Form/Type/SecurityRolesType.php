<?php

namespace Prodigious\Sonata\PermissionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Prodigious\Sonata\PermissionBundle\Form\Transformer\RestoreRolesTransformer;
use Prodigious\Sonata\PermissionBundle\Security\EditableRolesBuilder;

class SecurityRolesType extends AbstractType
{
    /**
     * @var EditableRolesBuilder
     */
    protected $rolesBuilder;

    /**
     * @var Sonata\AdminBundle\Admin\Pool
     */
    protected $pool;

    /**
     * @param EditableRolesBuilder $rolesBuilder
     */
    public function __construct(EditableRolesBuilder $rolesBuilder)
    {
        $this->rolesBuilder = $rolesBuilder;
        $this->pool = $rolesBuilder->getPool();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $transformer = new RestoreRolesTransformer($this->rolesBuilder);

        // GET METHOD
        $formBuilder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($transformer) {
            $transformer->setOriginalRoles($event->getData());
        });

        // POST METHOD
        $formBuilder->addEventListener(FormEvents::PRE_SUBMIT, function ( FormEvent $event ) use ( $transformer ) {
            $transformer->setOriginalRoles($event->getForm()->getData());
        });

        $formBuilder->addModelTransformer($transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $attr = $view->vars['attr'];

        if (isset($attr['class']) && empty($attr['class'])) {
            $attr['class'] = 'sonata-medium';
        }
        
        $view->vars['roles'] = $options['roles'];
        $view->vars['default_roles'] = $options['default_roles'];
        $view->vars['allowed_permissions'] = $options['allowed_permissions'];
        $view->vars['attr'] = $attr;
        $selectedRoles = $form->getData();

        if (!empty($selectedRoles)) {
            $view->vars['selected_choices'] = $selectedRoles;
        } else {
            $view->vars['selected_choices'] = '';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        list($roles, $availableRoles, $defaultRoles) = $this->rolesBuilder->getRoles();
       
        $allowedPermissions = $this->getAllowedPermissions($roles);

        $resolver->setDefaults([
            'choices' => function(Options $options, $parentChoices) use ($availableRoles){
                return empty( $parentChoices ) ? $availableRoles : [];
            },
            'roles' => $roles,
            'default_roles' => $defaultRoles,
            'allowed_permissions' => $allowedPermissions,
            'data_class' => null
        ]);
    }

    /**
     * Get all possible permissions ['Create', 'Edit', 'List', 'View', 'Delete', 'Export']
     *
     * @var array $roles
     *
     * @return array
     */
    public function getAllowedPermissions($roles)
    {
        $allowedPermissions = [];
        
        foreach ($roles as $role) {
            foreach ($role['items'] as $key => $item) {
                if($item['type'] == 'entity') {
                    if(count($item['permissions']) > 0) {
                        foreach ($item['permissions'] as $label => $permission) {
                            if(!array_key_exists($label, $allowedPermissions)) {
                                $allowedPermissions[$label] = $label;
                            }
                                
                        }
                    }
                }
            }
        }

        return $allowedPermissions;
    } 

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'prodigious_sonata_security_roles';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
