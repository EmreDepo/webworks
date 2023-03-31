#!/bin/bash

# Install Certbot and dependencies
apt update
apt install -y certbot python3-certbot-apache

# Create systemd timer
systemctl enable --now certbot.timer

# Set up automatic renewal and Apache restart
certbot renew --dry-run --preferred-challenges http --deploy-hook "/etc/init.d/apache2 restart" --quiet --no-self-upgrade --force-renewal
echo "15 3 * * 1 /usr/bin/certbot renew --quiet --no-self-upgrade --force-renewal --deploy-hook '/etc/init.d/apache2 restart'" >> /etc/crontab

# Restart cron service
systemctl restart cron
