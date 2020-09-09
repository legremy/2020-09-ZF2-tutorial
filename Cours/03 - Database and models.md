# Database and models

[link](https://zf2.readthedocs.io/en/latest/user-guide/routing-and-controllers.html)

## The database


```sql
CREATE TABLE album (
   id int(11) NOT NULL auto_increment,
   artist varchar(100) NOT NULL,
   title varchar(100) NOT NULL,
   PRIMARY KEY (id)
 );
 INSERT INTO album (artist, title)
     VALUES  ('The  Military  Wives',  'In  My  Dreams');
 INSERT INTO album (artist, title)
     VALUES  ('Adele',  '21');
 INSERT INTO album (artist, title)
     VALUES  ('Bruce  Springsteen',  'Wrecking Ball (Deluxe)');
 INSERT INTO album (artist, title)
     VALUES  ('Lana  Del  Rey',  'Born  To  Die');
 INSERT INTO album (artist, title)
     VALUES  ('Gotye',  'Making  Mirrors');
```

## The model files

The tutorial uses `Zend\Db\TableGateway\TableGateway`. This is an implementation of the Table Data Gateway design pattern to allow for interfacing with data in a database table. 

Create a `Album.php` under `module/Album/src/Album/Model`:

```php
namespace Album\Model;

 class Album
 {
     public $id;
     public $artist;
     public $title;

     public function exchangeArray($data)
     {
         $this->id     = (!empty($data['id'])) ? $data['id'] : null;
         $this->artist = (!empty($data['artist'])) ? $data['artist'] : null;
         $this->title  = (!empty($data['title'])) ? $data['title'] : null;
     }
 }
```

The `exchangeArray` method is required for our model to work with `Zend\Db\TableGateway`.

Create the `AlbumTable.php` file in `module/Album/src/Album/Model` directory :

```php
<?php	

 namespace Album\Model;

 use Zend\Db\TableGateway\TableGateway;

 class AlbumTable
 {
     protected $tableGateway;

     public function __construct(TableGateway $tableGateway)
     {
         $this->tableGateway = $tableGateway;
     }

     public function fetchAll()
     {
         $resultSet = $this->tableGateway->select();
         return $resultSet;
     }

     public function getAlbum($id)
     {
         $id  = (int) $id;
         $rowset = $this->tableGateway->select(array('id' => $id));
         $row = $rowset->current();
         if (!$row) {
             throw new \Exception("Could not find row $id");
         }
         return $row;
     }

     public function saveAlbum(Album $album)
     {
         $data = array(
             'artist' => $album->artist,
             'title'  => $album->title,
         );

         $id = (int) $album->id;
         if ($id == 0) {
             $this->tableGateway->insert($data);
         } else {
             if ($this->getAlbum($id)) {
                 $this->tableGateway->update($data, array('id' => $id));
             } else {
                 throw new \Exception('Album id does not exist');
             }
         }
     }

     public function deleteAlbum($id)
     {
         $this->tableGateway->delete(array('id' => (int) $id));
     }
 }
```

## Using ServiceManager to configure the table gateway and inject into the AlbumTable

In order to always use the same instance of our `AlbumTable`, we will use the `ServiceManager` to define how to create one. This is most easily done in the Module class where we create a method called `getServiceConfig()` which is automatically called by the `ModuleManager` and applied to the `ServiceManager`. We’ll then be able to retrieve it in our controller when we need it.

To configure the `ServiceManager`, we can either supply the name of the class to be instantiated or a factory (closure or callback) that instantiates the object when the `ServiceManager` needs it. We start by implementing `getServiceConfig()` to provide a factory that creates an `AlbumTable`. Add this method to the bottom of the `module/Album/Module.php` :

```php
	

 namespace Album;

 // Add these import statements:
 use Album\Model\Album;
 use Album\Model\AlbumTable;
 use Zend\Db\ResultSet\ResultSet;
 use Zend\Db\TableGateway\TableGateway;

 class Module
 {
     // getAutoloaderConfig() and getConfig() methods here

     // Add this method:
     public function getServiceConfig()
     {
         return array(
             'factories' => array(
                 'Album\Model\AlbumTable' =>  function($sm) {
                     $tableGateway = $sm->get('AlbumTableGateway');
                     $table = new AlbumTable($tableGateway);
                     return $table;
                 },
                 'AlbumTableGateway' => function ($sm) {
                     $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                     $resultSetPrototype = new ResultSet();
                     $resultSetPrototype->setArrayObjectPrototype(new Album());
                     return new TableGateway('album', $dbAdapter, null, $resultSetPrototype);
                 },
             ),
         );
     }
 }
```

The factory for `Album\Model\AlbumTable` creates an `AlbumTableGateway` to pass to the `AlbumTable`. An AlbumTableGateway is created by getting a `Zend\Db\Adapter\Adapter` and using it to create a `TableGateway` object. The `TableGateway` is told to use an `Album` object whenever it creates a new result row. The `TableGateway` classes use the `prototype` pattern for creation of result sets and entities.

Configure the `ServiceManager` to know how to get a `Zend\Db\Adapter\Adapter`. This is done using a factory called `Zend\Db\Adapter\AdapterServiceFactory` which we can configure within the merged config system. ZF2’s `ModuleManager` merges all the configuration from each module’s `module.config.php` file and then merges in the files in `config/autoload` (`*.global.php` and then `*.local.php` files). 
Add database configuration information to `global.php`. 
Use `local.php` to store the database credentials. 

Modify `config/autoload/global.php` with following code:
	
```php
 return array(
     'db' => array(
         'driver'         => 'Pdo',
         'dsn'            => 'mysql:dbname=zf2tutorial;host=localhost',
         'driver_options' => array(
             PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
         ),
     ),
     'service_manager' => array(
         'factories' => array(
             'Zend\Db\Adapter\Adapter'
                     => 'Zend\Db\Adapter\AdapterServiceFactory',
         ),
     ),
 );
```

Create `local.php` : 

```php
<?php
return [
    'db' => [
        'username' => 'YOURUSERNAME',
        'password' => 'YOURPASSWORD',
    ],
];
```

## Back to the controller

Add `getAlbumTable()` to the `AlbumController` class:
```php 
 // module/Album/src/Album/Controller/AlbumController.php:
     public function getAlbumTable()
     {
         if (!$this->albumTable) {
             $sm = $this->getServiceLocator();
             $this->albumTable = $sm->get('Album\Model\AlbumTable');
         }
         return $this->albumTable;
     }
```

## Listing Albums

```php
 // module/Album/src/Album/Controller/AlbumController.php:
 // ...
     public function indexAction()
     {
         return new ViewModel(array(
             'albums' => $this->getAlbumTable()->fetchAll(),
         ));
     }
 // ...
```

Fill the `index.phtml` view script:

```html
 <?php
 // module/Album/view/album/album/index.phtml:

 $title = 'My albums';
 $this->headTitle($title);
 ?>
 <h1><?php echo $this->escapeHtml($title); ?></h1>
 <p>
     <a href="<?php echo $this->url('album', array('action'=>'add'));?>">Add new album</a>
 </p>

 <table class="table">
 <tr>
     <th>Title</th>
     <th>Artist</th>
     <th>&nbsp;</th>
 </tr>
 <?php foreach ($albums as $album) : ?>
 <tr>
     <td><?php echo $this->escapeHtml($album->title);?></td>
     <td><?php echo $this->escapeHtml($album->artist);?></td>
     <td>
         <a href="<?php echo $this->url('album',
             array('action'=>'edit', 'id' => $album->id));?>">Edit</a>
         <a href="<?php echo $this->url('album',
             array('action'=>'delete', 'id' => $album->id));?>">Delete</a>
     </td>
 </tr>
 <?php endforeach; ?>
 </table>
```
