ALTER TABLE `{{PREFIX}}autor` ADD `interni` TEXT;

ALTER TABLE `{{PREFIX}}soubor` MODIFY latitude double NULL;
ALTER TABLE `{{PREFIX}}soubor` MODIFY longitude double NULL;