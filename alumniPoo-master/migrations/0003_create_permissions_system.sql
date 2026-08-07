CREATE TABLE permission (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  s_key VARCHAR(191) UNIQUE,
  s_label VARCHAR(191),
  s_description VARCHAR(255)
);

CREATE TABLE role_template (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  s_key VARCHAR(191) UNIQUE,
  s_label VARCHAR(191),
  s_description VARCHAR(255)
);

CREATE TABLE role_template_permission (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  i_role_template INT UNSIGNED,
  i_permission INT UNSIGNED
);

ALTER TABLE users ADD COLUMN i_role_template INT UNSIGNED NULL;
