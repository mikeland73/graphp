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
* PHP 5.5

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

Create instances:

```php
$user = (new User(['name' => 'Jane Doe']))->save();
$bank_account = new BankAccount(['accountNumber' => 123, 'balance' => 125.05]);
$bank_account->save();
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
    GP::view('admin/explore_view', ['title' => 'Hello World']);
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
    <a href="<?= OtherController::getURI('some method') ?>">Go to other controller</a>
  </body>
<html>
```

Set up instructions
======

TODO (mikeland86) :P

FAQ
======
#### Why PHP?
Great question. Inertia, I guess. The good news is we are using php 5.5 which has some cool features like traits and short array syntax.
