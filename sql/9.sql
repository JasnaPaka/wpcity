ALTER TABLE `{{PREFIX}}objekt` ADD `rok_zaniku` VARCHAR(255) NOT NULL;
ALTER TABLE `{{PREFIX}}objekt` ADD `potreba_foto` tinyint(4) NOT NULL DEFAULT 0;
ALTER TABLE `{{PREFIX}}autor` ADD `misto_narozeni` VARCHAR(255);
ALTER TABLE `{{PREFIX}}autor` ADD `misto_umrti` VARCHAR(255);
