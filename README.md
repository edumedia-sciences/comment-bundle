# eduMedia Comment Bundle

## How to

### Install bundle

```sh
composer require edumedia/comment-bundle
```

### Create Comment class

```php
<?php
// src/Entity/Comment.php

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use eduMedia\CommentBundle\Entity\CommentInterface;
use eduMedia\CommentBundle\Entity\CommentTrait;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table]
class Comment implements CommentInterface
{
    use CommentTrait;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?UserInterface $author = null;
}
```

### Make entity commentable

Here is a User example:

```php
<?php
// src/Entity/User

namespace App\Entity;

use eduMedia\CommentBundle\Entity\CommentableInterface;
use eduMedia\CommentBundle\Entity\CommentableTrait;

class User implements /* (...) */ CommentableInterface
{

    use CommentableTrait;
    
    // (...)
}
```

### Add admin routes to list/add comments in EasyAdmin

```yaml
# config/routes/edumedia_comment.yaml
edumedia_comment:
  resource: '@eduMediaCommentBundle/Resources/config/routes.yaml'
  prefix: '/admin/comments'
```

#### User CRUD example

```php
<?php
// src/Controller/Admin/UserCrudController.php

namespace App\Controller\Admin;

use App\Entity\User;
// (...)

class UserCrudController extends AbstractCrudController
{
    public function configureCrud(Crud $crud): Crud
    {
        return $crud->overrideTemplate('crud/edit', 'admin/user/edit.html.twig');
    }
}
```

```twig
{# templates/admin/user/edit.html.twig #}
{% extends '@EasyAdmin/crud/edit.html.twig' %}

{% block main %}
	{{ parent() }}
	{% include '@eduMediaComment/admin/comments/crud.html.twig' %}
{% endblock %}
```

### Optional: Use a non-default Comment class FCQN

```yaml
# config/services.yaml
services:
  eduMedia\CommentBundle\Service\CommentService:
    arguments:
      $commentClass: 'MyCustomApp\Entity\Comment'
```

### Migrate, to create tables

```sh
bin/console make:migration
bin/console doctrine:migrations:migrate
```