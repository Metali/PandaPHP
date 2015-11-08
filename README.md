# PandaPHP
Tired and lazy as a panda to write all your SQL request? PandaPHP is a php database framework to simplify your sql request, by allow you to build them step by step:

```php
$Panda->select(...)->where(...)->execute();
```
For example, instead of :
```php
$request = "SELECT * FROM user";
$query = $pdo->query($request);
$result = $query->fetchAll();
```

You will write :
```php
$Panda->setTable('user');
$Panda->select()->execute();
```
___

The following examples are based on the **user** table :

| id  |  name | last name | age 
| ------------- | ------------- | ------------- | ------------- |
| 1  | John | Doe | 50 |
| 2 | Chuck | Norris | 999 |
| 3 | Nina | Lopez | 32 |
| 4 | Joseph | Smith | 23 |

### Initialization

To use the framework, you must init the **Panda** class and set the table used for SQL request. Parameters with default arguments are optionnal if their values fit with your local configuration.

```php
$Panda = new \PandaPHP\Panda([
    'db' => 'db_name',
    'host' => 'localhost',      // default: 127.0.0.1
    'charset' => 'UTF8',        // default: UTF8
    'user' => 'root',           // default: root
    'password' => ''            // default: root
]);
```

```php
$Panda->setTable('user');
```
*note : your can re-use the **setTable()** method to set whenever you want the current table.*

### Executing SQL request
Panda framework is construct with simple method to get result. You can stack method to construct step by step your sql request.

```php
$Panda->select(...)->where(...);
```

After stacking all method required, you mut use the  **execute()** method to execute the built SQL request .

```php
$Panda->select(...)->where(...); // Return the SQL request (string)
$Panda->select(...)->where(...)->execute(); // Execute the SQL request
```

_note : the execute() method will not be write again in the following examples as long as it's implicit_

### Set the current table targeted

To set a `FROM table_name` in your request, you have to use the *setTable('table_name')* method. You can re-use the method as much as you want in your code, just before executing the sql request.

```php
$Panda->setTable('a_table'); // Select from a table?
$Panda->select(...)->execute();

$Panda->setTable('another_table'); // Select from an other table?
$Panda->select(...)->execute();
```

### Select *

```php
/**
* @return array
*/
$Panda->select();
```

### Select column_1, column_2
```php
/**
* @param array(column_1, column_2, column_3)
* @return array
*/
$Panda->select(['name','last_name']);
```

### Insert into
```php
/**
* @param array('column_name' => 'value')
* @return true|false 
*/
$Panda->insert([
    'name' => 'Steeve',
    'last_name' => 'Vine'
]);
```
You can use a numeric array to create a new entry (all column values must be setted!)
```php
/**
* @param array(value_1, value_2, value_3)
* @return true|false 
*/
$Panda->insert([
    null,      // id
    'Austin',  // name
    'Power',   // last_name
    '29'       // age
]);
```

### Update

```php
/**
* @param array('column' => 'new value')
* @return true|false 
*/
$Panda->update([
    'name'=>'new_name'
]);
```

### Delete

```php
/**
* @return true|false 
*/
$Panda->delete();
```

### Where 

You can add a where clause for your request
```php
/**
* @param array('column' => 'value')
* add a WHERE clause at the end of the current sql request
*/
$Panda->select()->where(['id' => 3]);
```
Examples : 
```php
$Panda->setTable('user');

// DELETE FROM user WHERE name = 'John' AND age = 23
$Panda->delete()->where([
	'name' => 'John',
	'age' => 23
]);

// UPDATE FROM user SET name = 'new_name' WHERE id = 3
$Panda->update(['name'=>'new_name'])->where(['id' => 3]);

// SELECT name, age FROM user WHERE id = 3
$Panda->select(['name','age'])->where(['id' => 3]);
```

### Limit
The limit method  add a `LIMIT` in your request. This method must always be used right before the `execute()` method. 

```php
/**
* @param int : $max, $offset
* add a LIMIT $offset, $max at the end of the sql request
*/
$Panda->...->limit($max, $offset)->execute();
```

Example :

```php
// will return 5 users with 3 for the offset
// The offset default value is 0
$Panda->select()->limit(5, 3)->execute();

// will return 5 users without offset
$Panda->select()->limit(2)
```
___

### TODO

There is a lot to do and I have a todo list :

* Deal with JOIN
* Deal with SQL injection
* Deal with WHERE value >= value
* And more...

*Feel free to contact me to add your contribution!*
