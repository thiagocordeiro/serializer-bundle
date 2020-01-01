# PHP SerializerBundle

A Symfony bundle for `thiagocordeiro/serializer`

## How to use

```
composer require thiagocordeiro/serializer-bundle
```

PHP Serializer does not use setters, so your class must have a constructor with all properties coming from the json.

#### Configure
It is possible to configure where the cache classes will be created `cache_dir` and if it should check if class was changed and recreate the cache `check_timestamp`

the following yaml contains default configurations, which is opmized for production environment, but the value for `check_timestamp` on dev env should be `true` so cachesare updated whe classes get changed

To customize just create a custom config file at `config/packages/dev/serializer.yaml`:
```yaml
parameters:
  serializer.cache_dir: '%kernel.cache_dir%'
  serializer.check_timestamp: true
```
