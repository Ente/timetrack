# NFClogin plugin

This pluin allows you to interact with a PC/SC device (e.g. NFC-card reader/writer) to login to the system.

## Requirements

Please make sure your PHP instance does not block `exec()` function.
This plugin requires the `pcscd` service to be running on the system. You can install it using the following command:

```bash
sudo apt update
sudo apt install pcscd pcsc-tools libpcsclite-dev python3-pip
pip3 install pyscard
sudo systemctl enable --now pcscd

```

For Windows:

- Download Python3 <https://www.python.org/downloads/windows/>
- Download Visual C++ Redistributable <https://learn.microsoft.com/en-us/cpp/windows/latest-supported-vc-redist>
- Install official drivers for your NFC reader
- Install `pyscard` using pip:
```bash
pip install pyscard
```
