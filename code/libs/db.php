<?php

declare(strict_types=1);

include_once __DIR__ . '/secrets.php';

const MYSQL_DATETIME_FORMAT = 'Y-m-d H:i:s';

const MYSQL_DATE_FORMAT = 'Y-m-d';

/**
 * Возвращает подключение к базе данных
 * Пароль к базе данных находится в файле .env
 */
function getConnection(): PDO
{
    return new PDO(
        'mysql:host=mysql;dbname=karma;port=3306',
        getMysqlUser(),
        getMysqlPassword(),
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
}