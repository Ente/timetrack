from smartcard.System import readers
from smartcard.util import toHexString

BLOCK = 4
KEY = [0xFF, 0xFF, 0xFF, 0xFF, 0xFF, 0xFF]  # Standard-Key A

def auth_block(connection, block):
    return connection.transmit([
        0xFF, 0x86, 0x00, 0x00, 0x05,
        0x01, 0x00, 0x60, block, 0x00
    ] + KEY)

def read_block(connection, block):
    return connection.transmit([0xFF, 0xB0, 0x00, block, 0x10])

r = readers()
if not r:
    print("No NFC reader found.")
    exit(1)

reader = r[0]
connection = reader.createConnection()
connection.connect()

_, sw1, sw2 = auth_block(connection, BLOCK)
if sw1 != 0x90 or sw2 != 0x00:
    print(f"Auth failed for block {BLOCK}")
    exit(1)

data, sw1, sw2 = read_block(connection, BLOCK)
if sw1 == 0x90 and sw2 == 0x00:
    value = bytearray(data).decode('utf-8', errors='ignore').rstrip('\x00')
    print(f'{{"block": {BLOCK}, "value": "{value}"}}')
else:
    print("Read failed.")
