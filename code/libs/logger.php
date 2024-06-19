<?php

declare(strict_types=1);

include_once __DIR__ . '/../constants.php';

function logDebug(string $message): void
{
    $logMessage = sprintf(
        "[%s] %s \n",
        date(LOG_DATETIME_FORMAT),
        $message
    );

    file_put_contents(LOG_FILE_PATH, $logMessage, FILE_APPEND);
}

function logException(Throwable $e): void
{
    $logMessage = sprintf(
        "[%s] %s \n",
        date(LOG_DATETIME_FORMAT),
        (string)$e
    );

    file_put_contents(LOG_FILE_PATH, $logMessage, FILE_APPEND);
}
