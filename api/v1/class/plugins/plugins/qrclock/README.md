# QRClock Plugin

This plugin allows you and your users to generate QR codes to either clock in or out.
You can also print them out or save them to your device for later use.

To use this feature, simply scan the QR code with your device's camera or a QR code scanner app and you should then be redirected to your browser.

## Installation

Before you can use this feature, you have to download the `phpqrcode` library from [here](https://sourceforge.net/projects/phpqrcode/).

After you've downloaded the library, extract it and copy the `phpqrcode` folder to the plugins `src` folder. (so there should be a `src/phpqrcode` folder).

## Configuration

You are currently not able to configure this plugin.

## Usage

Simply scan the QR code generated when opening "Plugins" > `[qrclock] Generate QR code`" and save or scan the QR code with your device.

You should be redirected to your browser and the clock in/out process should be completed. This currently requires you to be logged in before you can use the qr code. This will be reworked.

## Development

The class `QRClock` is the main class of this plugin and is responsible for generating the QR code and validating the dynamicToken embedded within the payload from the QR code.

### views/index.php

This is the general view for the plugin. It displays you your current status (if you either are going to clock in or out ("Status")) and the QR code.

If you scan the qr code the file will try to validate if an action has been specified within the URI. If not, nothing happens. Otherwise it tries to decode the payload and validate the token, if OK you will be clocked in or out.

You can modify this file if you want any changes to the UI.

### Dynamic Token

The dynamic token is a hash generated from the master token. The dynamic token and the master token are then validated when the QR code is scanned. If the verification is successful, the user is then clocked in or out.

To modify this or other behavior, you can modify the `QRClock::generateQRCodeContents(string $userId)` function.

### Payload

The payload is a base64 encoded JSON object containing:

```json
{
    "id": "1",
    "token": "token",
    "action": "clockin",
    "username": "username"
}
