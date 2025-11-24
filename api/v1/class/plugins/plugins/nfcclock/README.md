# NFC Clock plugin

This plugin allows you to use NFC tags to clock in and out of your time tracking system. By scanning an NFC tag, users can quickly log their work hours without needing to manually enter their information.
**This plugin requires the nfclogin plugin to function properly. Please ensure that the nfclogin plugin is enabled before using this plugin.**

## Features

- Clock in and out using NFC tags
- Automatic user identification based on NFC tag data

## Installation

1. Ensure that the nfclogin plugin is installed and enabled.
2. Download the NFC Clock plugin and place the `nfcclock` folder in the `api/v1/class/plugins/plugins/` directory.
3. Enable the plugin either within the `plugin.yml` file or through the `PluginManager` plugin.

To allow the plugin to register its custom routes, make sure you open the "[nfcclock] Open About Page" once after enabling the plugin.

## Using the Plugin

1. Assign NFC tags to users using the nfclogin plugin.
2. Users have to open the NFC clocking URL on their device, which is `http://<your-timetrack-server>/api/v1/nfcclock`
3. When a user scans their assigned NFC tag, the plugin will log their clock-in or clock-out time automatically. A simple web interface will confirm either action.
