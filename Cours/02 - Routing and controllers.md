# Roouting and controllers

[Link](https://zf2.readthedocs.io/en/latest/user-guide/routing-and-controllers.html)

Description of our application :

* Home: This will display the list of albums and provide links to edit and delete them. Also, a link to enable adding new albums will be provided.
* Add new album: This page will provide a form for adding a new album.
* Edit album: This page will provide a form for editing an album.
* Delete album: This page will confirm that we want to delete an album and then delete it.

Each page of the application is known as an _action_ and actions are grouped into _controllers_ within _modules_. 

Our 4 actions will be in `AlbumController` : `index`, `add`, `edit` and `delete`.

The mapping of a URL to a particular action is done using routes that are defined in the module’s `module.config.php` file :

```php
	

 return array(
     'controllers' => array(
         'invokables' => array(
             'Album\Controller\Album' => 'Album\Controller\AlbumController',
         ),
     ),

     // The following section is new and should be added to your file
     'router' => array(
         'routes' => array(
             'album' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/album[/:action][/:id]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'id'     => '[0-9]+',
                     ),
                     'defaults' => array(
                         'controller' => 'Album\Controller\Album',
                         'action'     => 'index',
                     ),
                 ),
             ),
         ),
     ),

     'view_manager' => array(
         'template_path_stack' => array(
             'album' => __DIR__ . '/../view',
         ),
     ),
 );
```
The segment route allows us to specify placeholders in the route.

Create the AlbumController, make it extend of `Zend\Mvc\Controller\AbstractActionController`.

Create the four actions in it.

## Initialise the view scripts

Create these four empty files :
* `module/Album/view/album/album/index.phtml`
* `module/Album/view/album/album/add.phtml`
* `module/Album/view/album/album/edit.phtml`
* `module/Album/view/album/album/delete.phtml`
