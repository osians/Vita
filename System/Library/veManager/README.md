# PHP veManager

A simple but powerful PHP Virtual Entity Data Manager `(in progress)`.

**Virtual**, because sometimes we don't like to create model classes, but we still want all the power of OOP when manipulating data that will be persisted in a table.


## How it works

If you decide to create a physical Entity (a php file model), you can create a class just like this:

```php
// Database Table
user
- id_user int
- name varchar
- age int
- email varchar

// model/User.php
class User extends Osians\VeManager\Entity
{
    protected $_name = null;
    protected $_age = null;
    protected $_mail = null;
}

// controller/User.php
$user = new User();
$user->setId(4);
$user->setName('John Doe');
$user->setAge(54);
$user->setEmail('john.doe@acmeinc.com');

$vem = new Osians\VeManager\VeManager($connectionParams);
$vem->save($user);
```

That's right, and that's how most Entity Manager systems work.

But if you want to create a Virtual Entity, as this system suggests, you can do it as follows:

```php
$vem = new Osians\VeManager\VeManager($connectionParams);

$user = $vem->create('user');
$user->setName('John Doe');
$user->setEmail('john.doe@hotmail.com');
$user->setAge(31);
$user->setActive(1);

//  Persiste
$vem->save($user);
```
You don't need to have a file that represents a Model class. You only need to have an `User` table registered in the database. When you use the `creatEntity` Method the system will search the database for the necessary information and then create a Virtual representation of the Data Model.


### **This system is composed by**

Some technologies are necessary for this system to work:

1. A Database Provider (/Database/Mysql.php)
1. A Query Builder (/QueryBuilder.php)
1. An Abstract Model Entity (/Entity.php)
1. A Virtual Entity Class (/VirtualEntity.php)
1. An Entity Manager (VeManager.php)

**Database Provider** - it is responsible for allowing the system to talk to the database.

**Query Builder** - This will allow a PHP class to be converted to an SQL language and SQL Language to be converted in Model Classes.

**Abstract Model Entity** - this Class implements all the methods necessary for an Entity to be manipulated by other objects in the system.

**Virtual Entity** - Represents a Virtual Instance, which is not based on a model file.

**Entity Manager** - This will orchestrate the communication between the class and the database.


## **How the database should be normalized**

The entire system is programmed using the CamelCase syntax scheme, however, the database needs to be used in the SnakeCase syntax.
For example, the user and address tables:
```
user
 - id_user
 - name
 - email
 - id_address
 - active

address
 - id_address
 - address
 - number
 - postal_code
 - active
```

The most important thing is that the primary key of the table is composed of "id_" + "tablename". If not, the system will have some issues to manipulate the table information.

## **Get Start**

The first thing we will always need is a database connection. We can achieve this through the Database Provider (ie.: Database/Mysql.php).

### **Creating a Database Connection**
```php
$provider = new \Osians\VeManager\Database\Provider\Mysql();
$provider
    ->setHostname('localhost')
    ->setPort('3306')
    ->setUsername('my_database_username')
    ->setPassword('my_database_password')
    ->setDatabaseName('database_name');

$connection = $provider->connect();
```

### **Set VeManager Connection**

Once we have a database connection, we need to inform the VeManager of our connection.
```php
$vem = new Osians\VeManager\VeManager($connection);
```

### **Don't forget**

Never forget that you need a properly standardized table in the database. In our example cases, we will use the user table.

```
user
  `id_user` int unsigned NOT NULL AUTO_INCREMENT
  `name` varchar(128) NOT NULL
  `email` varchar(128) NOT NULL
  `age` smallint unsigned NOT NULL
  `date_registration` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
  `active` tinyint unsigned NOT NULL DEFAULT '1'
  PRIMARY KEY (`id_user`)
```

**We are ready to begin**

Once we have everything set up, we can start testing some VeManager class operating scenarios. So, let's go!

### **Scenario 1**

Creating a new entity and saving it to the database.

```php
// create a Virtual Entity
$user = $vem->create('user');

// set data
$user->setName('John Doe');
$user->setEmail('john.doe@aol.com');
$user->setAge(31);
$user->setActive(1);

// persist
$vem->save($user);
```


### **Scenario 2**
Load records from a database, change it and save.

When it comes to the need of query database, a new element needs to be used ... the Query Builder. The `Query Builder` alone can only transform Object Oriented Programming into SQL Language. But when we use `VeManager` we can query the database through the `Database Provider` and transform the return of this query into `VirtualEntity` Classes that are manipulable via OOP.

```php
// create a Database Query
$q = new QueryBuilder();
$q->select()->from('user')->where("id_user = ?", 1);

// use VeManager to exec the Query
$user = $vem->fetchOne($q);

// If you want to know if something was found in the database.
if (empty($user)) {
    throw new Exception('Record does not exist');
}

// change e-mail
$user->setEmail('john.doe@outlook.com');

// persist
$vem->save($user);
```

#### **Alternative**

Alternatively, you can get an entity from the database without using QueryBuilder. Just like this:

```php
// Get User
$user = $vem->get('user', 1);

// change e-mail
$user->setEmail('john.doe@outlook.com');

// persist
$vem->save($user);
```

Just see what is most convenient for you.

### **Scenario 3**
Delete a record from the database

VeManager is the responsible for deleting a record from the database. To do this, you need to inform VeManager the model(entity) that will be deleted.

If you know the ID of an Entity, you can instance the model in 3 ways.

**load Entity**

```php
// first way
// Note that this way will not load data from Database
// this will only create a empty Entity and set an existing ID
$user = $vem->create('user');
$user->setId(10);

// second way
$q = new QueryBuilder();
$q->select()->from('user')->where("id = ?", 10);
$user = $vem->fetchOne($q);

// third way
$user = $vem->get('user', 10);

```

**Delete entity**
```php
$vem->delete($user);
```

### **Scenario 4**

Entities Relationship. Loading data from another Entity.
Let's say that we have the following table scenario:

```
user_address
 - id_user_address
 - id_user
 - id_address
 - active
```

The example above shows a connection table that is used to connect the `user` table to the `address` table. In this case, `id_user` and `id_address` will be converted to Entities, since they represent the IDs of **user** and **address** tables respectively.

**Getting:** You can easily get the data from the linked tables.

```php
$userAddress = $this->vem->get('user_address', 1);
$userName = $ua->getUser()->getName();
$address = $ua->getAddress()->getAddress();
```

**Setting:** If you want to change the property of a model that represents a LazyLoad. You can do this in two ways:

```php
// first way
$ua = $this->vem->get('user_address', 1);
$ua->setIdUser(2);
echo $ua->getUser()->getName();

// second way
$ua = $this->vem->get('user_address', 1);
$user = $this->vem->get('user', 2);
$ua->setUser($user);
echo $ua->getUser()->getName();
```

### **Scenario 5**
Loading data from another Entity that has no Relationship ID.
Let's say that we have the following table scenario:

```
user
 - id_user
 - name
 - email
 - active

 user_address
 - id_user_address
 - id_user
 - id_address
 - active
```

In this scenario you can see that the `user` table has no direct relation to the `user_address` table. In other words, in the user table there's no column called **id_user_address** that links one table to another. But, we know that this relation exists! `user_address` is a conection table, it means that it holds many to many id records from the tables user and address.

So, how do we get a table that has no relationship? In the same way as before!

```php
$user = $vem->get('user', 4);
$ua = $user->getUserAddress();
```

The Entity Manager will try to find a table called **user_address** and check if there is a column in the table called **id_user**. Once this exists, it will try to return all data from `user_address` table where the `id_user` column is the same as the id stored in the user entity.

Because this is a many-to-many relationship, the Entity Manager will return an Array instead of an VirtualEntity instance. So you need to manage this before using it.

```php
$user = $vem->get('user', 4);
$ua = $user->getUserAddress();

$postalCode = $ua[0]->getAddress()->getPostalCode();
```

In other words, you can't use like this:

```php
// Bad
$postalCode = $user->getUserAddress()->getAddress()->getPostalCode();
```

**[!] more content Comming soon**


**Thanks**.
Wanderlei Santana <sans.pds@gmail.com>