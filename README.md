# PHP SerializerBundle

A Symfony bundle for [`thiagocordeiro/serializer`](https://github.com/thiagocordeiro/serializer)

## How to use

```
composer require thiagocordeiro/serializer-bundle
```

#### Configure
It is possible to configure where the cache classes will be created `cache_dir` and `check_timestamp` for checking if class was changed so the cache gets updated.

The following yaml contains default configurations opmized for production environment, but you might need `check_timestamp` to be `true` on dev environment.

To customize just create a custom config file at `config/packages/dev/serializer.yaml`:
```yaml
# config/packages/dev/serializer.yaml
parameters:
  serializer.cache_dir: '%kernel.cache_dir%'
  serializer.check_timestamp: true # Note that the customization should be only on dev env folder
```

#### Return Value Objects on Controllers
This bundle adds an kernel event listener to symfony so you can return objects on controllers, this object will be converted into a JsonResponse with the serialized data on its body.

```php
<?php

namespace App\Framework\Controller\User;

use App\Domain\User\CreateUserService;
use App\Domain\User\CreateUser;
use App\Domain\User\UserCreated;
use Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CreateUserController
{
    /** @var CreateUserService */
    private $service;

    /** @var Serializer */
    private $serializer;

    public function __construct(CreateUserService $service, Serializer $serializer)
    {
        $this->service = $service;
        $this->serializer = $serializer;
    }

    public function __invoke(Request $request): UserCreated
    {
        $createUser = $this->serializer->deserialize((string) $request->getContent(), CreateUser::class);

        try {
            $userCreated = $this->service->create($createUser);
        } catch (UserAlreadyRegistered $e) {
            throw new HttpException(Response::HTTP_CONFLICT, 'User Already Registered', $e);
        }

        return $userCreated;
    }
}
```

#### Inject Value Objects on Controllers
You can also inject objects on controller, internally the bundle will create the object from request body.
For this to happen you have to tell the bundle which objects should be created,
to do so, create a yaml file at `config/packages/serializer.yaml` with the class names:
```yaml
# config/packages/serializer.yaml - note it should not be on env(dev/prod) folder)
parameters:
    serializer.value_objects:
        - 'App\Domain\User\CreateUser'
        - 'App\Domain\User\UserCreated'
        - ...
```

After setting up you will be able to inject this object, on the same example above the code will be much more simples.

```php
...

class CreateUserController
{
    /** @var CreateUserService */
    private $service;

    public function __construct(CreateUserService $service)
    {
        $this->service = $service;
    }

    public function __invoke(CreateUser $createUser): UserCreated
    {
        try {
            $userCreated = $this->service->create($createUser);
        } catch (UserAlreadyRegistered $e) {
            throw new HttpException(Response::HTTP_CONFLICT, 'User Already Registered', $e);
        }

        return $userCreated;
    }
}
```
