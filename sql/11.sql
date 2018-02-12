ALTER TABLE `{{PREFIX}}zdroj` ADD `typ` VARCHAR(255) AFTER `id`;
ALTER TABLE `{{PREFIX}}zdroj` ADD `identifikator` VARCHAR(255) AFTER `id`;
ALTER TABLE `{{PREFIX}}zdroj` MODIFY `nazev` VARCHAR(255);
ALTER TABLE `{{PREFIX}}zdroj` ADD `system_zdroj` VARCHAR(255);


UPDATE `{{PREFIX}}zdroj` SET typ = 'KNIHA', identifikator = isbn WHERE length(isbn) > 3;
ALTER TABLE `{{PREFIX}}zdroj` DROP COLUMN isbn;
