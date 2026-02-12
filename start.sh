#!/bin/sh

# Generate msmtp config from environment variables.
cat > /etc/msmtprc <<EOF
account default
host ${SMTP_HOST:-smtp-relay.brevo.com}
port ${SMTP_PORT:-587}
auth on
user ${SMTP_USER}
password ${SMTP_PASS}
from ${SMTP_FROM:-noreply@cleansweep-cleaning.com}
tls on
tls_starttls on
logfile /var/log/msmtp.log
EOF
chmod 600 /etc/msmtprc

php-fpm84 &
nginx -g "daemon off;"
