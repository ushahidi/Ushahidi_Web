CREATE INDEX incident_active ON incident (incident_active);
CREATE INDEX incident_date ON incident (incident_date);
CREATE UNIQUE INDEX incident_category_ids ON incident_category (incident_id,category_id);
CREATE INDEX category_visible ON category (category_visible);

UPDATE `settings` SET `db_version` = '14' WHERE `id` = 1 LIMIT 1;
