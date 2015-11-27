/* Create new table for user retailer profile */
CREATE SEQUENCE furniture_retailer_user_profile_id_seq INCREMENT BY 1 MINVALUE 1 START 1;
CREATE TABLE furniture_retailer_user_profile (
id INT NOT NULL, 
user_id INT NOT NULL, 
retailer_profile_id INT DEFAULT NULL, 
retailerMode SMALLINT DEFAULT NULL, 
PRIMARY KEY(id));
CREATE UNIQUE INDEX UNIQ_B20DBAC2A76ED395 ON furniture_retailer_user_profile (user_id);
CREATE INDEX IDX_B20DBAC2D8295B37 ON furniture_retailer_user_profile (retailer_profile_id);

/* Copy clear User table */

/* Create foreign keys between urniture_retailer_user_profile and furniture_user and fix user table */
ALTER TABLE furniture_retailer_user_profile ADD CONSTRAINT FK_B20DBAC2A76ED395 FOREIGN KEY (user_id) REFERENCES furniture_user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE furniture_retailer_user_profile ADD CONSTRAINT FK_B20DBAC2D8295B37 FOREIGN KEY (retailer_profile_id) REFERENCES furniture_retailer_profile (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE;
/* Copy users from origin User table to new User table with new strucutre */
INSERT INTO furniture_retailer_user_profile (id, user_id, retailer_profile_id, retailerMode) SELECT nextval('furniture_retailer_user_profile_id_seq'), id, retailer_profile_id, retailermode FROM furniture_user;
ALTER TABLE furniture_user DROP retailer_profile_id;
ALTER TABLE furniture_user DROP retailermode;

/*CHANGE BUYER TABLE */
ALTER TABLE specification_buyer RENAME COLUMN creator to user_creator;
ALTER TABLE specification_buyer ADD COLUMN creator INT ;
ALTER TABLE specification_buyer DROP CONSTRAINT FK_3AD3CCD4BC06EA63;
ALTER TABLE specification_buyer ADD CONSTRAINT FK_3AD3CCD4BC06EA63 FOREIGN KEY (creator) REFERENCES furniture_retailer_user_profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
/*MIGRATE BUYER DATA*/
UPDATE specification_buyer sb SET creator = rup.id  FROM (SELECT id, user_id FROM furniture_retailer_user_profile ) rup WHERE sb.user_creator = rup.user_id;
ALTER TABLE specification_buyer DROP COLUMN user_creator;

/*CHANGE SPECIFICATION TABLE*/
DROP INDEX idx_e3f1a9aa76ed395;
ALTER TABLE specification ADD COLUMN creator INT ;
ALTER TABLE specification ADD CONSTRAINT FK_E3F1A9ABC06EA63 FOREIGN KEY (creator) REFERENCES furniture_retailer_user_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE specification DROP CONSTRAINT FK_E3F1A9A6C755722;
ALTER TABLE specification ADD CONSTRAINT FK_E3F1A9A6C755722 FOREIGN KEY (buyer_id) REFERENCES specification_buyer (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE;
CREATE INDEX IDX_E3F1A9ABC06EA63 ON specification (creator);
UPDATE specification s SET creator = rup.id  FROM (SELECT id, user_id FROM furniture_retailer_user_profile ) rup WHERE s.user_id = rup.user_id;

ALTER TABLE specification DROP CONSTRAINT fk_e3f1a9aa76ed395;
DROP INDEX IDX_E3F1A9AA76ED395;
ALTER TABLE specification DROP user_id;
ALTER TABLE specification ALTER creator SET NOT NULL;