# Plugins

Plugins allow the User to interact with something that has not been implemented yet or will not be implemented.
This can be anything, e.g. a SendGrid integration.

To keep this pretty short, as other documentation articles are going more into the technical detail, here's what you have to know:

## General

Plugins are being "initialized" meaning, they are being loaded into the superclass `Plugins.arbeit.inc.php`.
TimeTrack handles Plugins as a extra navigation tab being displayed in the Navbar. You'll then get redirected to a menu to select the desired plugin you want to use right now.

Plugins can be restricted to be used by administrators only.

TimeTrack will read your plugin's `plugin.yml` and renders the `nav_links` to the `Plugins Page`.
You could design your plugin this way, that it only has one `nav_links`. This page then contains all other links, this might help keeping the navigation bar clean.

You can also design your plugin the way you want to. Since `PluginBuilder.plugins.arbeit.inc.php` has `Arbeitszeit` as it's parent class, its functions are inherited aswell. So you are able to communicate with the database aswell. In the future, we are planning to resolve the issue with direct access to the instance data to a plugin through the `permissions` attribute within the `plugin.yml`.

A user would go this way to access your plugin: (Select from nav bar) "Plugins" > "[PluginName] View Name" > (your php file)

Plugins are able to work without a navigation bar aswell, like dependencies for other plugins. Just leave the `nav_links` directive empty inside the `plugin.yml`

### plugin.yml

To start with your very first plugin, you should first create a `plugin.yml` into your plugin folder.
You can also run `PluginBuilder::create_skelleton($name)` to create a sample plugin.

The following options **MUST** be in every `plugin.yml`:

- `name`: Can be multiple words, e.g. "My first plugin"
- `main`: The name of your main class, e.g. "MyClass"
- `namespace`: Should be clear, e.g. "MyPlugin"
- `api`: The compatible plugin version (currently "0.1") - remember to write numbers with a decimal or a dot in quotes as they are being treated as floats

These are optional values you can add, which might make things easier:

- `src`: The source directory of your plugin, if empty `/src`
- `author`: The person/group/? who created the plugin. if empty: public domain
- `url`: An URL pointing to your plugins docs, etc.
- `permissions`: Allows you to register permissions, more information down below. - This functionality has not yet been developed
- `enabled`: Specifies if the plugin is enabled by default or not, if empty: depending on your app.ini
- `custom`: Add your own values here
- `build.instructions`: Allows you to add parameters to change the behaviour of the PluginBuilder.
  - `required`: An array containing relative paths to the required files, they will then get included within the archive (PHAR)
- `nav_links`: If your plugin has a front-end, please specify this attribute. It is stored in key-value pairs, e.g. "Send Message": "views/send-message.php" - Views have to be always in the `/views` folder within your plugin folder

### The .tp1 extension (deprecated)

The `.tp1` extension is used to gurantee the peristence of the plugins once TimeTrack has used them.

Every plugin, which is enabled is going to be created within one of the `.tp1` files. It's an acronym for `TimeTrack Plugin v1` - no worries, all plugins are at some point backwards compatible, depending on the `api` keywoard specified.
Everytime the system restarts (which in TimeTrack is triggered once a day) or the `PluginBuilder::initialize_plugins()` function gets used, the plugins are re-read by the system and every `.tp1` file gets replaced.
**Please try to NOT use the same name twice**. Plugins with duplicated names will not get disabled automatically, yet.

When plugins are memorized, you can re-access them with the `PluginBuilder::unmemorize_plugin($name)` function. There is no need to delete the `.tp1` file. After you want to save your class again, run `memorize_plugin($name)` and the file get's overwritten.
Technically, `.tp1` files are just serialized php classes, so you can also just use as a class.

Plugin data can also handled on your own way, e.g. by saving them into the `data` folder in any format you'd like. `.tp1` Files only contain the current configuration of your plugin.

### Permissions

While TimeTrack handles permissions in two different ways, plugins can only be viewed by administrators. However, everyone with a link is able to view the plugin views (not the Plugin selection screen itself)

### Hooks / Callbacks (WIP)

In general you are able to interact with native functions in two ways:

- Hooks
- Callbacks

Hooks and Callbacks are initialized by `Hooks:initialize()` which reads one-time when loading a desired class.

To remove either a hook or a callback, simply run e.g. `Hooks::removeHook('create_user', 'after', [$this, 'internalCallbackFunction'])`

#### Hooks

Hooks allow you to either execute code before or after the function has ran. This allows you to inject code within the native function.

You can register a hook within your plugin this way:

```php
Hooks::addHook('create_user', 'before', function(callable $originalFunction, $username, $name, $email, $password, $isAdmin){
  echo "Something before creating the user";
});
...
Hooks::addHook('create_user', 'after', [$this, 'internalCallbackFunction']);
```

Hooks also allow a special type `around`. This allows the callback functionality while still being able to inject your own code but without running `after` hooks.

This allows you to perform some checks before a user has been created or whatever else.

#### Callbacks (WIP)

Callbacks allow you to also execute a custom function after the native code, but it doesn't allows you to inject anything into it as it's just a callback. Callbacks might be also referred to as "simple Hooks".

You can register a callback like this:

```php
Hooks::addHook('create_user', 'callback', function($username, $name, $email, $password, $isAdmin){
  echo "User created: $username";
});

...
Hooks::addHook('create_user', 'callback', [$this, 'internalCallbackFunction']);
```

You could likely use this to redirect your user to a custom page of your plugin or to proceed your own data. Callbacks can only be registered by one. If a plugin has registered the callback address, no other plugin would be able to overwrite it or to register it on its own.

(`internalCallbackFunction` would be the name of a function you have defined within your class.)
You might have to open a plugin the first time to get the hook or callback registered.
