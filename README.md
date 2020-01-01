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
parameters:
  serializer.cache_dir: '%kernel.cache_dir%'
  serializer.check_timestamp: true
```
