-- Add Fulltext Index for Partial Search 
ALTER TABLE incident ADD Fulltext(incident_title, incident_description);