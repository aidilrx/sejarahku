-- DATABASE FOR /sejarahku/
-- PLEASE DO NOT CHANGE CONTENT WITHOUT PERMISSION
/**
* @author Aidil
* @package sejarahku
*/
-- Create the database
CREATE DATABASE IF NOT EXISTS sejarahku;

-- Use the database for more functionality
USE sejarahku;

-- TABLE 'murid'
CREATE TABLE `murid` (
    `IDMurid`    INT(20)      UNSIGNED NOT NULL AUTO_INCREMENT,
    `NoKP`       VARCHAR(20)           NOT NULL,
    `NamaMurid`  VARCHAR(255)          NOT NULL,
    `Katalaluan` VARCHAR(255)          NOT NULL,

    PRIMARY KEY(`IDMurid`)
)ENGINE=INNODB;
/**
* DESCRIPTION: any
*/

-- TABLE 'kuiz'
CREATE TABLE `kuiz` (
    `IDKuiz`   INT(20)      UNSIGNED NOT NULL AUTO_INCREMENT,
    `NamaKuiz` VARCHAR(255)          NOT NULL,

    PRIMARY KEY(`IDKuiz`)
)ENGINE=INNODB;
/**
* DESCRIPTION: any
*/

-- TABLE 'skor_murid'
CREATE TABLE `skor_murid` (
    `IDSkor`  INT(20)      UNSIGNED NOT NULL AUTO_INCREMENT,
    `IDMurid` INT(20)      UNSIGNED NOT NULL,
    `IDKuiz`  INT(20)      UNSIGNED NOT NULL,
    `Skor`    DOUBLE(5, 2)          NOT NULL,
    `Gred`    VARCHAR(20)           NOT NULL,

    PRIMARY KEY(`IDSkor`),
    FOREIGN KEY(`IDMurid`) REFERENCES `murid`(`IDMurid`) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY(`IDKuiz`)  REFERENCES `kuiz`(`IDKuiz`)    ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=INNODB;
/**
* DESCRIPTION: any
*/

-- TABLE 'soalan'
CREATE TABLE `soalan` (
    `IDSoalan`   INT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `IDKuiz`     INT(20) UNSIGNED NOT NULL,
    `TeksSoalan` TEXT             NOT NULL,

    PRIMARY KEY(`IDSoalan`),
    FOREIGN KEY(`IDKuiz`) REFERENCES `kuiz`(`IDKuiz`) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=INNODB;
/**
* DESCRIPTION: any
*/

-- TABLE 'jawapan'
CREATE TABLE `jawapan` (
    `IDJawapan`   INT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `IDSoalan`    INT(20) UNSIGNED NOT NULL,
    `TeksJawapan` TEXT             NOT NULL,

    PRIMARY KEY(`IDJawapan`),
    FOREIGN KEY(`IDSoalan`) REFERENCES `soalan`(`IDSoalan`) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=INNODB;
/**
* DESCRIPTION: any
*/

-- TABLE 'jawapan_soalan'
CREATE TABLE `jawapan_soalan` (
    `IDJawapanSoalan` INT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `IDSoalan`        INT(20) UNSIGNED NOT NULL,
    `IDJawapan`       INT(20) UNSIGNED NOT NULL,

    PRIMARY KEY(`IDJawapanSoalan`),
    FOREIGN KEY(`IDSoalan`) REFERENCES `soalan`(`IDSoalan`)     ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY(`IDJawapan`) REFERENCES `jawapan`(`IDJawapan`)  ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=INNODB;

/**
* UPDATE 
* Added some condition for quizs and use 'IF NOT EXISTS' to prevent coflict with existing data
* @version +0.1-development_phase /  @version 0.2-development_phase
*/

/**
* DESCRIPTIO: any
*/

/**
* DESCRIPTION: database for quiz website, though some tables property not in built ERD.
* STATUS: Executable, though few warning encountered during executing process.
* BUG:
    ** Cannot enter `NamaKuiz` more than 20 character
    ** STATUS: fixed. Changed the varchar length to 255 as it limit
    ****
    ** 
*/