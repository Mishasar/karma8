use karma;

DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `jobs`;
DROP TABLE IF EXISTS `workers`;

CREATE TABLE IF NOT EXISTS `users`
(
    `id`                 int          NOT NULL AUTO_INCREMENT,
    `username`           varchar(255) NOT NULL,
    `email`              varchar(255) NOT NULL,
    `validts`            date         NOT NULL,
    `last_notified_date` date                  DEFAULT NULL,
    `confirmed`          tinyint(1)   NOT NULL DEFAULT '0',
    `checked`            tinyint(1)   NOT NULL DEFAULT '0',
    `valid`              tinyint(1)   NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `jobs`
(
    `id`         int NOT NULL AUTO_INCREMENT,
    `user_id`    int NOT NULL,
    `process_id` char(36) DEFAULT NULL,
    PRIMARY KEY (`id`, `user_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `workers`
(
    `process_id`     char(36) NOT NULL,
    `lastActiveTime` datetime NOT NULL,
    PRIMARY KEY (`process_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
