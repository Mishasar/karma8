<?php

declare(strict_types=1);

const NOTIFICATION_INTERVALS_DAYS_BEFORE_EXPIRATION = [1, 3];

const MAIL_FROM = 'from@example.com';

const MAIL_TEXT = '%s, your subscription is expiring soon';

// 1 минута на проверку валидности, 10 секунд на тестирование, округлил до 2 минут
const WORKER_ONE_JOB_MAX_DURATION_SECONDS = 120;

// Общее кол-во воркеров. Методика расчёта в readme
const WORKERS_COUNT = 148;

const WORKER_WATCHER_SLEEP_SECONDS = 10;

const LOG_DATETIME_FORMAT = 'Y-m-d H:i:s';

const LOG_FILE_PATH = '/var/log/backend/log.txt';