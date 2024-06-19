<?php

declare(strict_types=1);

include_once __DIR__ . '/../constants.php';
include_once __DIR__ . '/../libs/logger.php';
include_once __DIR__ . '/../libs/db.php';

/*
 * Демон, который следит за кол-вом воркеров.
 * Если воркер не работает более 2 минут, то запускает новый (время задаётся в конфиге).
 */

function getCurrentWorkerCount(): int
{
    $workerOneJobMaxDurationSeconds = WORKER_ONE_JOB_MAX_DURATION_SECONDS;
    $result = getConnection()
        ->query(
            <<<SQL
                SELECT count(1) as count 
                FROM workers
                WHERE lastActiveTime > date_sub(now(), interval $workerOneJobMaxDurationSeconds second)
            SQL
        )
        ->fetch();

    return (int)$result['count'];
}

function runWorker(): void
{
    $path = __DIR__ . '/../workers/worker_send_email.php';
    $command = 'php ' . $path . ' > /dev/null &';

    exec($command);
}

function handle(): void
{
    $ranWorkersCount = getCurrentWorkerCount();
    $countWorkersToStart = WORKERS_COUNT - $ranWorkersCount;

    if ($countWorkersToStart < 1) {
        logDebug("Running $ranWorkersCount workers.");
        return;
    }

    logDebug("Ran $ranWorkersCount workers. Running $countWorkersToStart workers...");

    for ($i = 0; $i < $countWorkersToStart; $i++) {
        runWorker();
    }
}

while (true) {
    try {
        sleep(WORKER_WATCHER_SLEEP_SECONDS);
        handle();
    } catch (Throwable $e) {
        logException($e);
    }
}