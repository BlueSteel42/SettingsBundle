# SettingsBundle

A Symfony 2.7+ bundle which allow you to store of multpliple project-configuration settings using different kinds of backend. 

## Installation

1. Add this bundle to your project as a composer dependency: simply add a dependency on `bluesteel42/settings-bundle` to your project's `composer.json` file. Here is a minimal example of a `composer.json` file that just defines a dependency on Diff:

```
    {
        "require": {
            "bluesteel42/settings-bundle": "~1.0"
        }
    }
```

2. Add this bundle in your application kernel:

    ```php
    // app/AppKernel.php
    public function registerBundles()
    {
        // ...
        $bundles[] = new BlueSteel42\SettingsBundle\BlueSteel42SettingsBundle();
    }
    ```
## Backend Configuration

SettingsBundle allow three different kind of backends:
* Doctrine
* Yaml File
* XML File

**Doctrine**

Specify in your `config.yml` file `connection` and `table` parameters. For example:

  ```yml
    bluesteel42_settings:
        backend: doctrinedbal
            connection: default
            table: bluesteel42_settings
  ```
Default values are: `default` and `bluesteel42_settings` respectively.

Use command ```bluesteel42_settings:install``` to automatically create table. Follow the help command for more informations. 

**Yaml File**

Specify in your `config.yml` file `path` parameter. For example:

  ```yml
    bluesteel42_settings:
        backend: yaml
            path: path/to/my/yml
  ```
Default value is: `%kernel.root_dir%/Resources`.

**XML File**

Specify in your `config.yml` file `path` parameter. For example:

  ```yml
    bluesteel42_settings:
        backend: xml
            path: path/to/my/xml
  ```
Default value is: `%kernel.root_dir%/Resources`.

## Cache Configuration
SettingsBundle allows (optionally) three kind of caches:
* NullCache
* Cache on File
* Memcached

**NullCache**

An Empty Adapter for cache is used. The following configuration are equivalent

```yml
    bluesteel42_settings:
        cache: ~
```
```yml
    bluesteel42_settings:
        cache:
            'null': true
```        
**Cache on File**

Specify in your `config.yml` file `path` parameter. For example:

```yml
    bluesteel42_settings:
        cache:
            file:
                path: '%kernel.cache_dir%/bluesteel42_settings'
```

Default value is: '%kernel.cache_dir%/bluesteel42_settings'.

**Memcached**

Specify in your `config.yml` file `servers` parameter as an array of coupled (host,port). For example:

```yml
    bluesteel42_settings:
        cache:
            memcached:
                servers:
                    - { host: localhost, port: 11211 }
```
Default configuration as an array with an element: localhost as 'host' and '11211' port.

## Usage

```php

//  Get the service
$service = $this->get('bluesteel42.settings');
//  Set a key
$service->set('key','val');
//  Set a key as a multilevel array
$service->set('key.sub','subval1');
$service->set('key.sub.sub','subval2');
//  Delete a key
$service->delete('key');
//  Flush Modification
$service->flush();

//  get all values
$ll = $service->getAll();

//  chainability allowed
$service->set('controller.one', 1)
    ->set('controller.two', 2)
    ->set('controller.three', 3)
    ->set('controller.four', 4)
    ->flush();
```