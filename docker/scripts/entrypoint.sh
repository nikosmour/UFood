#!/bin/bash
# Start Apache in the background
apache2ctl -D FOREGROUND &

# Start Supervisor to manage other processes
/usr/bin/supervisord -c /etc/supervisor/supervisord.conf

# Keep the container running
tail -f /dev/null