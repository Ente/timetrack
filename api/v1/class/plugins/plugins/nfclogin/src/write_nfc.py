import sys
from smartcard.System import readers
from smartcard.util import toBytes

BLOCK = 4 

if len(sys.argv) != 2:
    print("Usage: python3 write_ultralight_block.py <data>")
    sys.exit(1)

data = sys.argv[1][:4].ljust(4, '\x00')  
data_bytes = toBytes(' '.join([f"{ord(c):02X}" for c in data]))

r = readers()
if not r:
    print('{"error": "No reader found"}')
    sys.exit(1)

reader = r[0]
connection = reader.createConnection()
connection.connect()

command = [0xFF, 0xD6, 0x00, BLOCK, 0x04] + data_bytes
response, sw1, sw2 = connection.transmit(command)

if sw1 == 0x90 and sw2 == 0x00:
    print(f'{{"success": true, "block": {BLOCK}, "written": "{data}"}}')
else:
    print(f'{{"error": "Write failed", "sw1": "{sw1:02X}", "sw2": "{sw2:02X}"}}')
