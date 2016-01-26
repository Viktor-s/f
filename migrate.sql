-- Start migrations for task #357
ALTER TABLE furniture_retailer_user_profile RENAME TO retailer_user_profile;
ALTER SEQUENCE  furniture_retailer_user_profile_id_seq RENAME TO retailer_user_profile_id_seq;

ALTER TABLE retailer_user_profile ADD emails TEXT DEFAULT NULL;
UPDATE retailer_user_profile SET emails = '[]';
ALTER TABLE retailer_user_profile ALTER emails SET NOT NULL;
COMMENT ON COLUMN retailer_user_profile.emails IS '(DC2Type:json_array)'; -- Necessary for Doctrine2 DBAL
-- End migrations for task #357


-- Start migrations for task #446
ALTER TABLE furniture_product ADD product_type SMALLINT DEFAULT NULL;
UPDATE furniture_product SET product_type = 1;
ALTER TABLE furniture_product ALTER product_type SET NOT NULL;

ALTER TABLE furniture_product RENAME COLUMN availableForSale TO available_for_sale;
ALTER TABLE furniture_product RENAME COLUMN factoryCode TO factory_code;

ALTER TABLE furniture_product RENAME TO product;
ALTER SEQUENCE furniture_product_id_seq RENAME TO product_id_seq;
-- End migrations for task #446
CREATE TABLE retailer_profile_demo_factories (profile_id INT NOT NULL, factory_id INT NOT NULL, PRIMARY KEY(profile_id, factory_id));
CREATE INDEX IDX_5ECF7558CCFA12B8 ON retailer_profile_demo_factories (profile_id);
CREATE INDEX IDX_5ECF7558C7AF27D2 ON retailer_profile_demo_factories (factory_id);
ALTER TABLE retailer_profile_demo_factories ADD CONSTRAINT FK_5ECF7558CCFA12B8 FOREIGN KEY (profile_id) REFERENCES furniture_retailer_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE retailer_profile_demo_factories ADD CONSTRAINT FK_5ECF7558C7AF27D2 FOREIGN KEY (factory_id) REFERENCES factory (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE product_pdp_input ADD forSchemes BOOLEAN DEFAULT 'false' NOT NULL;
