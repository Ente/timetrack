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

### Permissions

While TimeTrack handles permissions in two different ways, plugins can only be viewed by administrators. However, everyone with a link is able to view the plugin views (not the Plugin selection screen itself)
