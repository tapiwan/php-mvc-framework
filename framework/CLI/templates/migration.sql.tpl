/**
 * {MigrationName}
 */

DROP TABLE IF EXISTS `{TableName}` ;

CREATE TABLE IF NOT EXISTS `{TableName}` (
    `id` INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `created_at` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `updated_at` TIMESTAMP DEFAULT now() ON UPDATE now(),
    `placeholder` VARCHAR(255)
ENGINE = InnoDB;