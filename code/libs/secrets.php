<?php

declare(strict_types=1);

function getMysqlPassword(): string
{
    return file_get_contents('/run/secrets/karma_mysql_password');
}

function getMysqlUser(): string
{
    return file_get_contents('/run/secrets/karma_mysql_user');
}