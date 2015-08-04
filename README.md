graphp
======

The GraPHP web framework.

The goal of this project is to build a lightweight web framework with a graph DB abstraction. It should be very easy to create the graph schema with no knowledge of of how the data is stored. Also, the schema should be incredibly flexible so you should never need migrations when adding new models (nodes), connections (edges), or data that lives in nodes.

A couple of things that describe graphp:

* Full MVC. Zero boilerplate controllers, models, and views.
* Models are your schema. Defining data is up to you (but not required).
* No migrations. Team members can add new models independently without conflicts
* No DB queries, unless you want to. Transparent model makes it easy to see what happens under the hood.
* DB API is designed for fast performance. No implicit joins or other magic, but expressive enough for nice readable code.
* No CLI needed (but supported for cron and tests).
* All classes are loaded on demand when used for the first time.
* PHP 5.5+

A simple example:

Define nodes (your model) with minimum boilerplate
=

```php
class User extends GPNode {
  protected static function getDataTypesImpl() {
    return [
      new GPDataType('name', GPDataType::GP_STRING, true),
    ];
  }
  protected static function getEdgeTypesImpl() {
    return [
      new GPEdgeType(BankAccount::class),
    ];
  }
}
```

Define a model for bank account

```php
// No need to declare data if you don't want to index it.
class BankAccount extends GPNode {}
```

Create instances and edges between them:

```php
$user = (new User())->setName('Jane Doe')->save();
$bank_account = (new BankAccount())
  ->setData('accountNumber', 123)
  ->setData('balance', 125.05)
  ->save();
$user->addBankAccount($bank_account)->save();
```

and load them later:

```php
$user = User::getOneByName('Jane Doe');
$account = $user->loadBankAccount()->getOneBankAccount();
echo $account->getData('balance'); // 125.05
```

Controllers
=
```php
class MyController extends GPController {

  public function helloWorld() {
    GP::view('hello_world_view', ['title' => 'Hello World']);
  }

  public function doStuff() {
    // Do stuff and redirect
    OtherController::redirect()->method();
  }
}
```

Views
=
```html
<html>
  <title>
    <?= $title ?>
  </title?
  <body>
    <a href="<?= OtherController::getURI('someMethod') ?>">Go to other controller</a>
  </body>
<html>
```

Set up instructions
======
* Install php-5.5+ mysql php-mysqli
* Run `mysql -u db_user < graphp/db/mysql_schema.sql` to create the database.
* Create maps folder and open permissions: `mkdir maps; chmod 777 maps`
* Point your webserver to public directory.
* Modify config files to suit your environment.
* To check out sample app, change the general config 'app_folder' to "sample_app".


FAQ
======

**What is a graph database and why should I use it?**
A graph db is a database that uses graph structures for semantic queries with nodes, edges and properties to represent and store data (wikipedia). By giving our nodes, edges, and data nice human readable names we can write pretty, easy to understand code while storing the data in a way that is much more intuitive than relational dbs or key value stores. The flexible schema makes it easy to make structural changes to objects without having to write migrations or make any db changes.


**What is a human readable graph? How does this lead to nicer code?**
The following code loads friends and city for a user and all her friends:
```php
$friends = $user->loadCity()->loadFriends()->getFriends();
batch($friends)->loadFriends()->loadCity();
```


**What are magic methods, and what do you mean no boilerplate?**
In graphp, node methods are defined by the graph structure. So if you create a user node with a friend edge and a city edge, you automatically can do things like:
```php
$user->addFriend($friend)->save();
$city = $user->loadCity()->getCity();
$user->removeAllFriends()->save();
```
There are no cli commands to create the node, there is no autogen code, and there is no copy paste boilerplace. All of these methods will work using the minimal node and edge information you provide.
