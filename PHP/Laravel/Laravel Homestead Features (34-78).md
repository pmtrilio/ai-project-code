log-level=1
server-id=${server_id}
server-token=${server_token}
socket=unix:///var/run/blackfire/agent.sock
spec=
"

client="[blackfire]
ca-cert=
client-id=${client_id}
client-token=${client_token}
endpoint=https://blackfire.io
timeout=15s
"

echo "$agent" > "/etc/blackfire/agent"
echo "$client" > "/home/vagrant/.blackfire.ini"

service php5.6-fpm restart
service php7.0-fpm restart
service php7.1-fpm restart
service php7.2-fpm restart
service php7.3-fpm restart
service php7.4-fpm restart
service php8.0-fpm restart
service blackfire-agent restart
