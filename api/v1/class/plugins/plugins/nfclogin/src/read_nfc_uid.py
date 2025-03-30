from smartcard.System import readers
from smartcard.util import toHexString

r = readers()
if not r:
    print("No reader found")
    exit(1)

reader = r[0]
connection = reader.createConnection()
connection.connect()

GET_UID = [0xFF, 0xCA, 0x00, 0x00, 0x00]  # Command to get UID
data, sw1, sw2 = connection.transmit(GET_UID)

if sw1 == 0x90 and sw2 == 0x00:
    print('{"uid": "' + ''.join(format(x, '02X') for x in data) + '"}')
else:
    print('{"error": "Failed to get UID"}')
