# Audit Log Plugin

A logging plugin for [CakePHP](http://cakephp.org). The included `AuditableBehavior`  creates an audit history for each instance of a model to which it's attached.

The behavior tracks changes on two levels. It takes a snapshot of the fully hydrated object _after_ a change is complete and it also records each individual change in the case of an update action.

## Features

* Allows each revision record to be attached to a source -- usually a user -- of responsibility for the change.
* Allows developers to ignore changes to specified properties. Properties named `created`, `updated` and `modified` are ignored by default, but these values can be overwritten.
* Handles changes to HABTM associations.
* Does not require or rely on the existence of explicit models revisions (`AuditLogs`) and deltas (`AuditLogDeltas`).

## Installation

### Composer

**NOTE**: The plugin supports CakePHP 3.6+ since version 1.2.0 (see Git tag 1.1.0), for support for CakePHP 3 versions prior to 3.6, use the version under Git tag 1.1.0.

```
  $ composer install creditdatamw/cakephp-audit-log
```

### Manually

1. Click the big ol' **Downloads** button next to the project description.
2. Extract the archive to `src/plugins/AuditLog`.

### Enable the plugin

In your application's `bootstrap.php` (ie src/config/bootstrap.php) add the following line ```Plugin::load('AuditLog')```

## Migrations

Before you can use the plugin you need to have the tables for storing revisions (`AuditLogs`) and deltas (`AuditLogDeltas`).
To create the tables you can use the CakePHP 3.0 Migrations shell. Here is how:

```
  $ bin/cake migrations migrate -p AuditLog
```

Make sure you have privileges to create tables in your database otherwise this probably won't work ;)

## Usage
    
Applying the `AuditableBehavior` to a model is essentially the same as applying any other CakePHP behavior. 


### Basic Usage

```php
    # Simple syntax accepting default options
    class TasksTable extends Table {
    
      public function initialize(array $config)
      {
        $this->addBehavior('AuditLog.Auditable');
      }
      
    }
```

### Describing events

> NOTE: Since version `1.1.0` 

You may want to add a custom description message for an Audit Event from your application.
You can do this by setting the `Auditable.auditDescription` attribute in the current request's 
Session. The plugin reads the value from that and sets it in the `description` field for the 
Audit record. The value is cleared upon use.

Example:

```php
  public function delete($id=null) {
    $session = $this->request()->getSession();

    $post = $this->Posts->get($id);
    
    $title = $post->get('title');
    
    $session->write('Auditable.auditDescription', __('User deleted a Post with title={0}', $title));
    
    $this->Posts->delete($post);
    
    return $this->redirect('/');
  }
```
 

### Configuration
    
The behavior does offer a few configuration options:

<dl>
	<dt>`id`</dt>
	<dd>Name of the primary key property of the model if your Table class does not follow CakePHP convention</dd>
	<dt>`ignore`</dt>
	<dd>An array of property names to be ignored when records are created in the deltas table.</dd>
	<dt>`habtm`</dt>
	<dd>An array of models that have a HABTM relationship with the acting model and whose changes should be monitored with the model. If the HABTM model is auditable in its own right, don't include it here. This option is for related models whose changes are _only_ tracked relative to the acting model.</dd>
</dl>

```php
    # Syntax with explicit options
    class TasksTable extends Table {
    
      public function initialize(array $config)
      {
        $this->addBehavior('AuditLog.Auditable', [
          'id' => 'task_id',
          'ignore' => ['active', 'name', 'updated'],
          'habtm'  => ['Type', 'Project']
        ]);
      }
      
    }
```

### Setting Audit Sources

The `AuditableBehavior` optionally allows each changeset to be "owned" by a "source" -- typically the user responsible for the change. Since user and authentication models vary widely, the behavior supports a callback method that should return the value to be stored as the source of the change, if any.

The `currentUser()` method must be available to every Table that cares to track a source of changes.

Included in the plug-in is a utility trait that you can use on your table to get the current user from the current request.
You can use it as follows:

```php
    use AuditLog\Model\Table\CurrentUserTrait;
    
    class TasksTable extends Table {
      
      use CurrentUserTrait;
      
      public function initialize(array $config)
      {
        $this->addBehavior('AuditLog.Auditable');
      }
      
    }
```

Your table's will then have a ```currentUser()``` function attached that returns an array that has values filled out like this:

```php
    [
        'id'          => Auth::User('username'),
        'description' => Auth::User('username'),
        'url'         => Request::here, // using the current request
        'ip'          => env('REMOTE_ADDR'),
    ]
```

## CONTRIBUTING

Feel free to submit bug reports or suggest improvements in a ticket or fork this project and improve upon it yourself. Contributions welcome.

## License

This code is licensed under the [MIT license](http://www.opensource.org/licenses/mit-license.php).