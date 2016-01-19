-- Start migrations for task #357
ALTER TABLE furniture_retailer_user_profile RENAME TO retailer_user_profile;
ALTER SEQUENCE  furniture_retailer_user_profile_id_seq RENAME TO retailer_user_profile_id_seq;

ALTER TABLE retailer_user_profile ADD emails TEXT DEFAULT NULL;
UPDATE retailer_user_profile SET emails = '[]';
ALTER TABLE retailer_user_profile ALTER emails SET NOT NULL;
COMMENT ON COLUMN retailer_user_profile.emails IS '(DC2Type:json_array)'; -- Necessary for Doctrine2 DBAL
-- End migrations for task #357