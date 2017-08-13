/**
* {FileName}
*/

DROP TABLE IF EXISTS `{TableName}` ;

CREATE TABLE IF NOT EXISTS `{TableName}` (
    /* Primary Key (muss mit Model übereinstimmen) */
    `id` INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,

    /* Fillables (müssen mit Model übereinstimmen) */
    `placeholder` VARCHAR(255),

    /* Timestamps */
    `created_at` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `updated_at` TIMESTAMP DEFAULT '0000-00-00 00:00:00'
)

ENGINE = InnoDB;