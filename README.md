# Sonata Permission (ACL) Bundle
 
This bundle provides a friendly view to display roles administration.

# Prerequisites
- SonataAdminBundle
- SonataUserBundle

# Screenshots
Check out the [screenshot](https://github.com/nan-guo/SonataPermissionBundle).

# Installation

```
```

# Configuration
```
// app/AppKernel.php
new Prodigious\Sonata\PermissionBundle\ProdigiousSonataPermissionBundle(),
```

### config.yml or sonata_acl.yml

Add permission list

```
prodigious_sonata_permission:
    default_roles: []
    auto_replace_roles_field: true
    groups:
        admin:
            label:              Admin Roles
            translation_domain: ProdigiousSonataPermissionBundle
            items:
                - { type: role, name: ROLE_SUPER_ADMIN, label: ROLE_SUPER_ADMIN }
                - { type: role, name: ROLE_ADMIN, label: ROLE_ADMIN }
        content:
            label:              Content
            translation_domain: ProdigiousSonataPermissionBundle
            items:
                - { type: entity, name: sonata.admin.news }
                - { type: entity, name: sonata.admin.product, permissions: { 'Edit': 'EDIT', 'Delete': 'DELETE' } } // permissions est optionnal
        classification:
            label:              Classification
            translation_domain: ProdigiousSonataPermissionBundle
            items:
                - { type: entity, name: sonata.admin.category }
                - { type: entity, name: sonata.admin.tag }
        media:
            label:              Media
            translation_domain: ProdigiousSonataPermissionBundle
            items:
                - { type: entity, name: sonata.media.admin.media }
                - { type: entity, name: sonata.media.admin.gallery }
        user:
            label:              User
            translation_domain: ProdigiousSonataPermissionBundle
            items:
                  - { type: entity, name: sonata.user.admin.user }
                  - { type: entity, name: sonata.user.admin.group }
```

# Changelog


# Additional info
Author: Nan GUO
 
Company : [Prodigious](http://www.prodigious.com/)
