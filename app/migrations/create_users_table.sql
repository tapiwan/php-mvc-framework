/**
* create_users_table
*/
DROP TABLE IF EXISTS `users` ;

CREATE TABLE IF NOT EXISTS `users` (
    /* Primary Key (muss mit Model übereinstimmen) */
    `id` INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,

    /* Fillables (müssen mit Model übereinstimmen) */
    `name` VARCHAR(255),
    `age` INTEGER,
    `lastname` VARCHAR(255),

    /* Timestamps */
    `created_at` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `updated_at` TIMESTAMP DEFAULT '0000-00-00 00:00:00'
)

ENGINE = InnoDB;