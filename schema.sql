CREATE DATABASE YetiCave;
USE YetiCave;
CREATE TABLE category (
id INT AUTO_INCREMENT PRIMARY KEY,
name CHAR(64)
);

CREATE TABLE lots (
id INT AUTO_INCREMENT PRIMARY KEY,
creation_date DATETIME,
name CHAR(64),
description TEXT,
image CHAR(64),
price_initial MEDIUMINT NOT NULL,
data_end TINYINT,
step SMALLIN NOT NULL,
author CHAR(64),
author_winner  CHAR(64),
lots_category CHAR(64)
);

CREATE TABLE rate (
id INT AUTO_INCREMENT PRIMARY KEY,
date_accommodation DATETIME,
price MEDIUMINT,
rate_user CHAR(64),
rate_lots CHAR(128)
);

CREATE TABLE user (
id INT AUTO_INCREMENT PRIMARY KEY,
date_registration DATETIME,
emmail CHAR(128) NOT NULL UNIQUE,
nome CHAR(64),
password CHAR(64) NOT NULL UNIQUE,
avatar CHAR(128),
conteks CHAR(64),
creation_lots CHAR(128),
user_rate MEDIUMINT
);
