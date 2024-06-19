<?php

declare(strict_types=1);

function send_email(
    string $from,
    string $to,
    string $text
): void {
    sleep(random_int(1, 10));

    echo "Run send_email. $from $to $text \n";
}

function check_email(
    string $email
): bool {
    echo "Run checkEmail for $email. Please, give me money \n";
    sleep(random_int(1, 60));

    return random_int(0, 1) === 1;
}