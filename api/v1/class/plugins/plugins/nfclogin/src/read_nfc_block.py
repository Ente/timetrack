from smartcard.System import readers
from smartcard.util import toHexString

BLOCK = 4

r = readers()
if not r:
    print("No NFC reader found.")
    exit(1)

reader = r[0]
connection = reader.createConnection()
connection.connect()

data, sw1, sw2 = connection.transmit([0xFF, 0xB0, 0x00, BLOCK, 0x10])

if sw1 == 0x90 and sw2 == 0x00:
    value = bytearray(data).decode('utf-8', errors='ignore').rstrip('\x00')
    print(f'{{"block": {BLOCK}, "value": "{value}"}}')
else:
    print('{"error": "Read failed", "sw1": %d, "sw2": %d}' % (sw1, sw2))
