<?php

declare(strict_types=1);

include_once __DIR__ . '/../constants.php';
include_once __DIR__ . '/../libs/logger.php';
include_once __DIR__ . '/../libs/db.php';

/*
 * Крон для постановки задач на отправку писем и валидацию emailов
 */

$connection = getConnection();

$date = date(MYSQL_DATE_FORMAT);

$mysqIntervalsArray = array_map(
    static fn($day) => "date(subdate(now(), interval $day day))",
    NOTIFICATION_INTERVALS_DAYS_BEFORE_EXPIRATION
);

$mysqIntervalsString = implode(',', $mysqIntervalsArray);

$sqlSelectUsers = <<<SQL
    SELECT users.id as id
    FROM users
    WHERE confirmed = 1
      AND (last_notified_date != '$date' OR last_notified_date is null)
      AND validts IN ($mysqIntervalsString)
    SQL;

$rows = $connection->query($sqlSelectUsers)->fetchAll();

foreach ($rows as $row) {
    $userId = $row["id"];
    $connection->exec("insert into jobs (user_id) values ($userId)");
    $connection->exec("update users set last_notified_date = '$date' where id = $userId");
}