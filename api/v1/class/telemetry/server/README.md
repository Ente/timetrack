# Self host telemetry server.

Make sure your clients point to a specific client for the telemetry server.
Install TimeTrack on that server and start the telemetry server with `cd path/to/timetrack/api/v1/class/telemetry/server && php -S 0.0.0.0:8888 server.php`

Inside DB check the results within the `telemetry_server` table. Set `telemetryServer` var within app.json `general` section to `true`.
A new entry called Server Telemetry will appear within the Navigation bar.
