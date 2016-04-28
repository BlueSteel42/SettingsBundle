# SettingsBundle

A Symfony 2.7+ bundle which allows you to store multiple project configuration settings
using different backends for data storage. 

## Installation

Add this bundle to your project as a composer dependency: simply add a dependency on
`bluesteel42/settings-bundle` to your project's `composer.json` file. Here is a
minimal example of a `composer.json` file that just defines a dependency on SettingsBundle:

```
    {
        "require": {
            "bluesteel42/settings-bundle": "~1.0"
        }
    }
```

Add the bundle in your application kernel:

```php
// app/AppKernel.php
public function registerBundles()
{
    // ...
    $bundles[] = new BlueSteel42\SettingsBundle\BlueSteel42SettingsBundle();
}
```
    
## Backend Configuration

The SettingsBundle works out of the box without the needing of specific configuration, using the Yaml backend by
default and storing data under ```%kernel.root_dir%/Resources directory```. If you need to use a different backend just put
it in your `config.yml`:

 ```yml
 bluesteel42_settings: xml
 ```

Valid backends are: `yml`, `xml`, `doctrinedbal`.

### Yaml and XML backends

`yml` and `xml` backends store data in a file. In order to change the default directory under which
the file is stored you must use the extended configuration:

 ```yml
 bluesteel42_settings:
     backend: xml
         path: path/to/my/dir
 ```

### Doctrine DBAL backend

This backend stores data in a database table. By default it uses the
`bluesteel42_settings` table and the Doctrine connection named `default`.  
You can modify one or both these parameters with the extended configuration:
 
 ```yml
 bluesteel42_settings:
     backend: doctrinedbal
         connection: my_connection
         table: my_settings_table
 ```

The table can be created using the console command

 ```bash
 bluesteel42_settings:install
 ```
 
Please see the command help for more information.

## Cache Configuration
The SettingsBundle uses a cache to speed-up data loading and provides adapters for
**Memcached** and **file-based cache**. A special **Null** adapter is also provided to let you
disable the cache for debugging purposes. The **Null** adapter is enabled by default.

Each cache adapter has specific configuration options.

### NullCache

Since this adapter is enabled by default the following configurations are equivalent:
```yml
    bluesteel42_settings:
        cache: ~
```
```yml
    bluesteel42_settings:
        cache: 'null'
```
```yml
    bluesteel42_settings:
        cache:
            'null': true
```
This adapter does note have further configuration parameters.

### File-based cache
This adapter stores the cached data in a cache file placed by default under
the ```%kernel.cache_dir%/bluesteel42_settings``` directory.
This adapter can be enabled with its default configuration with the following keys in your
```config.yml```:
```yml
    bluesteel42_settings:
        cache: file
```
If you need to modify the default path under which the cache file is stored just the
```path``` key:
```yml
    bluesteel42_settings:
        cache:
            file:
                path: 'path/to/my/cache/dir'
```
It is **strongly recommended** to keep the cache file somewhere under ```%kernel.cache_dir%```
in order to have it cleaned up every time you invoke a ```cache:clear``` console command.

### Memcached
This adapter uses the ```php-memcached``` extension to store cached data under **one or more**
memcached servers. Each server must be specified as in the following example:

```yml
    bluesteel42_settings:
        cache:
            memcached:
                servers:
                    - { host: localhost, port: 11211 }
                    - { host: my_remote_cache_server, port: 11222 }
```

## Usage
The SettingsBundle exposes a `bluesteel42.settings` that acts as a _settings repository_.  
Each setting _value_ is identified by a _key_. You can use a dot (```.```) to create a key hierarchy.

### Get values
Keys can be retrieved using the method ```get($key, $default = null)```
```php
//  Get the service
$service = $this->get('bluesteel42.settings');
//  Get a key
$value = $service->get('my_key');
// Get a subkey
$subValue = $service->get('second_key.sub_key.third_level_key');
// Get a key with a default value if not found
$items_per_page = $service->get('items_per_page', 10);
```

### Set values
A key value can be set using the method ```set($key, $value)```. 
```php
//  Set a key
$service->set('items_per_page', 20);
//  Set a subkey
$service->set('my_key.sub_key','subval1');
```

If the value is an array, a subkey is created for each key of the array.
```php
$myArray = array('first' => 'red', 'second' => 'blue', 'third' => 'yellow');
$service->set('colors', $myArray);
echo $service->get('colors.first'); // outputs 'red'
```

Note that when a key is set the old value will be overridden regardless the existance or not of subkeys.
For instance, following the example above:
```php
$service->set('colors', 'black and white');
echo $service->get('colors.first'); // outputs null
echo $service->get('colors'); // outputs 'black and white'
```

### Delete values
A key can be deleted using the method ```delete($key)```. The given key and all its subkeys
(if any) will be deleted.

```php
//  Delete a key
$service->delete('my_key');
```

### Persist changes
Every set of changes must be explicitly persisted. If not, changes will be lost at the end
of the execution.  
Changes will be persisted by invoking the method ```flush()```.
```php
//  Flush Modification
$service->flush();
```

## Tips
### Get or set all keys in a single call
You can retrieve all keys with one single call with the method ```getAll()``` and set all key
values with ```setAll(array $values```.  
Note that since by using subkeys you define a hierarchy the method ```getAll()``` returns a
multidimensional array and - obviously - the method ```setAll()``` needs a multidimensional
array in order to store the data set correctly.

```php
$allValues = array('colors.first' => 'red', 'colors.second' => 'blue'); // wrong

//correct
$allValues = 
    array(
        'colors' => 
            array(
                'first' => 'red', 
                'second' => 'blue'
            )
    );
    
$service->setAll($allValues);
```

### Chainability
The method ```set($key, $value)``` allows you to chain calls:
```php
$service->set('controller.one', 1)
    ->set('controller.two', 2)
    ->set('controller.three', 3)
    ->set('controller.four', 4)
    ->flush();
```