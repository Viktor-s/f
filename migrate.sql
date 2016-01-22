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