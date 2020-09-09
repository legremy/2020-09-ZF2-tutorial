# Modules

[Link](https://zf2.readthedocs.io/en/latest/user-guide/modules.html)

## Setting up the Album module

Create this tree :

```
 (tutorial-name)/
     /module
         /Album
             /config
             /src
                 /Album
                     /Controller
                     /Form
                     /Model
             /view
                 /album
                     /album
```

For the rest of this course, (tutorial-name) will be considered equal to "zf2-tutorial"

Create a file called "Module.php" under `zf2-tutorial/module/Album` :

```php	
<?php
 namespace Album;

 use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
 use Zend\ModuleManager\Feature\ConfigProviderInterface;

 class Module implements AutoloaderProviderInterface, ConfigProviderInterface
 {
     public function getAutoloaderConfig()
     {
         return array(
             'Zend\Loader\ClassMapAutoloader' => array(
                 __DIR__ . '/autoload_classmap.php',
             ),
             'Zend\Loader\StandardAutoloader' => array(
                 'namespaces' => array(
                     __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                 ),
             ),
         );
     }

     public function getConfig()
     {
         return include __DIR__ . '/config/module.config.php';
     }
 }
```

## Autoloading files

As we are in development, we don’t need to load files via the classmap, so we provide an empty array for the classmap autoloader. Create a file called `autoload_classmap.php` under `zf2-tutorial/module/Album`:

```php
<?php
return [];
```

## Configuration

Create a file called `module.config.php` under `zf2-tutorial/module/Album/config`:

```php
<?php
 return array(
     'controllers' => array(
         'invokables' => array(
             'Album\Controller\Album' => 'Album\Controller\AlbumController',
         ),
     ),
     'view_manager' => array(
         'template_path_stack' => array(
             'album' => __DIR__ . '/../view',
         ),
     ),
 );
```

The controllers section provides a list of all the controllers provided by the module. 

Within the `view_manager` section, we add our view directory to the `TemplatePathStack` configuration.

## Informing the application about our new module

In the application’s `config/application.config.php` file, tell the ModuleManager about the new module : 
```php
// [...]     
    'modules' => array(
         'Application',
         'Album',                  // <-- Add this line
     ),
// [...]
```
