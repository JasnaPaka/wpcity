START TRANSACTION;

CREATE INDEX idx_deleted ON {{PREFIX}}objekt` (deleted);
CREATE INDEX idx_schvaleno ON {{PREFIX}}objekt` (schvaleno);
CREATE INDEX idx_zruseno ON {{PREFIX}}objekt` (zruseno);

CREATE INDEX idx_systemova ON {{PREFIX}}kategorie` (systemova);

CREATE INDEX idx_deleted ON {{PREFIX}}fotografie` (deleted);
CREATE INDEX idx_skryta ON {{PREFIX}}fotografie` (skryta);
CREATE INDEX idx_primarni ON {{PREFIX}}fotografie` (primarni);

CREATE INDEX idx_deleted ON {{PREFIX}}bod` (deleted);
CREATE INDEX idx_deleted ON {{PREFIX}}soubor` (deleted);
CREATE INDEX idx_deleted ON {{PREFIX}}stitek` (deleted);
CREATE INDEX idx_deleted ON {{PREFIX}}zdroj` (deleted);

COMMIT;
