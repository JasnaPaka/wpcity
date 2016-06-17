ALTER TABLE `{{PREFIX}}objekt` ADD `mestska_cast` VARCHAR(255) NOT NULL AFTER `rok_vzniku`;
ALTER TABLE `{{PREFIX}}objekt` ADD `oblast` VARCHAR(255) NOT NULL AFTER `rok_vzniku`;
ALTER TABLE `{{PREFIX}}objekt` ADD `adresa` VARCHAR(255) NOT NULL AFTER `rok_vzniku`;
