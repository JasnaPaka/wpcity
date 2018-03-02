ALTER TABLE `{{PREFIX}}fotografie` ADD soubor int(11);
ALTER TABLE `{{PREFIX}}fotografie` ADD CONSTRAINT fk_soubor FOREIGN KEY (`soubor`) REFERENCES {{PREFIX}}soubor(id);

ALTER TABLE `{{PREFIX}}fotografie` ADD autor_id int(11);
ALTER TABLE `{{PREFIX}}fotografie` ADD CONSTRAINT fk_autor FOREIGN KEY (`autor_id`) REFERENCES {{PREFIX}}autor(id);

ALTER TABLE `{{PREFIX}}fotografie` MODIFY COLUMN objekt int(11);
