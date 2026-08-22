<?php

return ['retention_days' => (int) env('BACKUP_RETENTION_DAYS', 14), 'daily_time' => env('BACKUP_DAILY_TIME', '02:30')];
