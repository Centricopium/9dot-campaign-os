#!/bin/sh

# Hostinger runs this script once per minute through a custom cron job.
# flock prevents a second worker starting while the previous import chunk runs.
cd /home/u848929565/domains/9dotmedia.com/campaign_app || exit 1
exec /usr/bin/flock -n /tmp/9dot-campaign-queue.lock \
    /usr/bin/php artisan queue:work --once --timeout=900 --tries=2 --no-interaction
