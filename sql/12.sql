ALTER TABLE `{{PREFIX}}zdroj` ADD soubor int(11);
ALTER TABLE `{{PREFIX}}zdroj` ADD CONSTRAINT fk_soubor FOREIGN KEY (`soubor`) REFERENCES {{PREFIX}}soubor(id);
