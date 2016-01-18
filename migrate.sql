ALTER TABLE furniture_user RENAME TO users;
ALTER TABLE sylius_customer RENAME TO customers;

ALTER SEQUENCE furniture_user_id_seq RENAME TO users_id_seq;
ALTER SEQUENCE sylius_customer_id_seq RENAME TO customers_id_seq;