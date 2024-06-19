<?php

declare(strict_types=1);

include_once __DIR__ . '/../constants.php';
include_once __DIR__ . '/../libs/db.php';
include_once __DIR__ . '/../libs/logger.php';
include_once __DIR__ . '/../libs/external.php';
include_once __DIR__ . '/../libs/uuid.php';

/*
 * Воркер для отправки email-сообщений
 * Разделять валидацию email и отправку не стал,
 * так как они всег следуют друг за другом и валидация используется только для отправки
 */

function updateWorkerLiveTime(
    PDO $connection,
    string $processId
) {
    $time = date(MYSQL_DATETIME_FORMAT);

    $connection->exec(
        <<<SQL
        INSERT INTO workers (process_id, lastActiveTime)
        VALUES ('$processId', '$time')
        ON DUPLICATE KEY UPDATE lastActiveTime='$time'
        SQL
    );
}

/**
 * Получаем идентификатор jobы и user_id, с которым работаем
 */
function getFreeJob(
    PDO $connection,
    string $processId
): ?array {
    $connection->beginTransaction();
    $queueJob = $connection
        ->query('select id, user_id from jobs where process_id is null limit 1 for update')
        ->fetch();

    if (!$queueJob) {
        $connection->commit();
        // Все сообщения отправлены
        return null;
    }

    $jobId = (int)$queueJob['id'];

    // Считаем, что здесь и далее все данные не могут содержать sql инъекции, так как они int и нет ввода
    $connection->exec("update jobs set process_id = '$processId' where id = $jobId");
    $connection->commit();

    $userId = (int)$queueJob['user_id'];

    logDebug("Process: $processId get job with id: $jobId and user_id: $userId");

    return [
        $jobId,
        $userId,
    ];
}

function getUserInfo(
    PDO $connection,
    int $userId
): ?array {
    return $connection
        ->query("select username, email, confirmed, checked, valid from users where id = $userId")
        ->fetch(PDO::FETCH_ASSOC);
}

function removeJob(
    PDO $connection,
    int $jobId,
): void {
    $connection->exec("delete from jobs where id = $jobId");
}

function validateEmailAndSaveResult(PDO $connection, int $userId, string $email): bool
{
    $result = check_email($email);

    $resultValue = (int)$result;
    $connection->exec("update users set valid = $resultValue, checked = 1 where id = $userId");

    return $result;
}

function handle(string $processId, PDO $connection): void
{
    updateWorkerLiveTime($connection, $processId);

    $jobInfo = getFreeJob($connection, $processId);

    if (!$jobInfo) {
        logDebug("Process: $processId. Job not found");
        // Если задач нет, не стоит часто проверять их наличие
        sleep(10);
        return;
    }

    [$jobId, $user_id] = $jobInfo;

    $userInfo = getUserInfo($connection, $user_id);
    $email = $userInfo['email'];
    $username = $userInfo['username'];
    $valid = $userInfo['valid'];

    if (!$userInfo['confirmed']) {
        logDebug("Process: $processId remove job: $jobId. Reason: is not confirmed");
        removeJob($connection, $jobId);
        return;
    }

    if (!$userInfo['checked']) {
        $valid = validateEmailAndSaveResult($connection, $user_id, $email);
        logDebug("Process: $processId check email $email for user $user_id. Result: " . (int)$valid);
    }

    if (!$valid) {
        logDebug("Process: $processId remove job: $jobId. Reason: is not valid");
        removeJob($connection, $jobId);
        return;
    }

    send_email(MAIL_FROM, $email, sprintf(MAIL_TEXT, $username));
    logDebug("Process: $processId send mail: $username $email");

    removeJob($connection, $jobId);
}

function main(): void
{
    $processId = getUuid();
    $connection = getConnection();

    logDebug("Run worker with process id: " . $processId);

    while (true) {
        try {
            handle($processId, $connection);
        } catch (Throwable $e) {
            logException($e);
        }
    }
}

main();