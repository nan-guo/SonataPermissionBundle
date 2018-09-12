<?php

namespace Prodigious\Sonata\PermissionBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Prodigious\Sonata\PermissionBundle\Security\EditableRolesBuilder;

class RestoreRolesTransformer implements DataTransformerInterface
{
    /**
     * @var array
     */
    protected $originalRoles = null;
    
    /*
     * @var EditableRolesBuilder
     */
    protected $rolesBuilder  = null;
    
    /**
     * @param EditableRolesBuilder $rolesBuilder
     */
    public function __construct(EditableRolesBuilder $rolesBuilder)
    {
        $this->rolesBuilder = $rolesBuilder;
    }
    /**
     * @param array|null $originalRoles
     */
    public function setOriginalRoles(array $originalRoles = null)
    {
        $this->originalRoles = $originalRoles ?: [];
    }
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if ($value === null) {
            return $value;
        }
        if ($this->originalRoles === null) {
            throw new \RuntimeException('Invalid state, originalRoles array is not set');
        }
        
        return $value;
    }
    /**
     * {@inheritdoc}
     */
    public function reverseTransform($selectedRoles)
    {
        if ($this->originalRoles === null) {
            throw new \RuntimeException('Invalid state, originalRoles array is not set');
        }
        list($roles, $availableRoles, $defaultRoles) = $this->rolesBuilder->getRoles();

        $hiddenRoles = array_diff($this->originalRoles, $availableRoles);

        return array_merge($selectedRoles, $hiddenRoles, $defaultRoles);
    }
}