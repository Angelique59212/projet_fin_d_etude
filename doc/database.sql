-- MySQL Script generated by MySQL Workbench
-- Fri Apr 22 11:56:25 2022
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema les_troubles_dys
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema les_troubles_dys
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `les_troubles_dys` DEFAULT CHARACTER SET utf8 ;
USE `les_troubles_dys` ;

-- -----------------------------------------------------
-- Table `les_troubles_dys`.`mdf58_user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `les_troubles_dys`.`mdf58_user` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(150) NOT NULL,
  `firstname` VARCHAR(150) NOT NULL,
  `lastname` VARCHAR(150) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `age` SMALLINT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `les_troubles_dys`.`mdf58_role`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `les_troubles_dys`.`mdf58_role` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_name` VARCHAR(45) NOT NULL,
  `mdf58_user_fk` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_mdf58_role_mdf58_user1_idx` (`mdf58_user_fk` ASC),
  CONSTRAINT `fk_mdf58_role_mdf58_user1`
    FOREIGN KEY (`mdf58_user_fk`)
    REFERENCES `les_troubles_dys`.`mdf58_user` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `les_troubles_dys`.`mdf58_article`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `les_troubles_dys`.`mdf58_article` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NULL,
  `date_add` DATETIME NOT NULL,
  `date_update` DATETIME NOT NULL,
  `mdf58_user_fk` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_mdf58_article_mdf58_user_idx` (`mdf58_user_fk` ASC),
  CONSTRAINT `fk_mdf58_article_mdf58_user`
    FOREIGN KEY (`mdf58_user_fk`)
    REFERENCES `les_troubles_dys`.`mdf58_user` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `les_troubles_dys`.`mdf58_comments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `les_troubles_dys`.`mdf58_comments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `content` TEXT NULL,
  `mdf58_user_fk` INT UNSIGNED NOT NULL,
  `mdf58_article_fk` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_mdf58_comments_mdf58_user1_idx` (`mdf58_user_fk` ASC),
  INDEX `fk_mdf58_comments_mdf58_article1_idx` (`mdf58_article_fk` ASC),
  CONSTRAINT `fk_mdf58_comments_mdf58_user1`
    FOREIGN KEY (`mdf58_user_fk`)
    REFERENCES `les_troubles_dys`.`mdf58_user` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_mdf58_comments_mdf58_article1`
    FOREIGN KEY (`mdf58_article_fk`)
    REFERENCES `les_troubles_dys`.`mdf58_article` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
