USE mariadb;

--
-- create tables
--
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    api_key CHAR(36) UNIQUE
);

CREATE TABLE licenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    license_key VARCHAR(192) UNIQUE,
    license_data TEXT,
    valid_until DATETIME,
    created DATETIME,
    updated DATETIME
);

CREATE TABLE users_licenses (
    user_id INT,
    license_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (license_id) REFERENCES licenses(id),
    PRIMARY KEY (user_id, license_id)
);

--
-- insert demo data
--
INSERT INTO users (id, api_key) VALUES
(1, '29161696-9087-44b5-8ab3-88cd27cc5d6a'),
(2, '018e008b-a02d-7c95-990f-85778ba9f01e');

INSERT INTO licenses (id, license_key, license_data, valid_until, created) VALUES
(1, '123-456-789', 'inhalt\rder\rlizenzdatei', NULL, NOW()),
(2, '456-789-123', 'am 31.12.2023 abgelaufene Lizenz', '2023-12-31 23:59:59', NOW());

INSERT INTO users_licenses (user_id, license_id) VALUES
(1, 1),
(2, 2);