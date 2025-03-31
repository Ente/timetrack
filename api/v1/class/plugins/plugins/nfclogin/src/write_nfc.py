import sys
from smartcard.System import readers

if len(sys.argv) != 2:
    print("Usage: python3 write_to_card.py <data-to-write>")
    sys.exit(1)

input_data = sys.argv[1]

BLOCK = 4
KEY = [0xFF, 0xFF, 0xFF, 0xFF, 0xFF, 0xFF]  # Default Key A

def auth_block(connection, block):
    return connection.transmit([
        0xFF, 0x86, 0x00, 0x00, 0x05,
        0x01, 0x00, 0x60, block, 0x00
    ] + KEY)

def write_block(connection, block, data_str):
    data = bytearray(data_str.encode("utf-8")[:16])
    data += b"\x00" * (16 - len(data))
    return connection.transmit([0xFF, 0xD6, 0x00, block, 0x10] + list(data))

r = readers()
if not r:
    print("No NFC reader found.")
    sys.exit(1)

reader = r[0]
connection = reader.createConnection()
connection.connect()

_, sw1, sw2 = auth_block(connection, BLOCK)
if sw1 != 0x90 or sw2 != 0x00:
    print(f"Auth failed for block {BLOCK}.")
    sys.exit(1)

_, sw1, sw2 = write_block(connection, BLOCK, input_data)
if sw1 == 0x90 and sw2 == 0x00:
    print(f"Block {BLOCK} successfully written with value: '{input_data}'")
else:
    print('{"error": "write failed"}')
