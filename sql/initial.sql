SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

DROP SCHEMA IF EXISTS `myezteam_myezteam` ;
CREATE SCHEMA IF NOT EXISTS `myezteam_myezteam` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `myezteam_myezteam` ;

-- -----------------------------------------------------
-- Table `myezteam_myezteam`.`users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `myezteam_myezteam`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `email` VARCHAR(64) NOT NULL ,
  `password` VARCHAR(64) NULL ,
  `legacy_password` VARCHAR(36) NULL ,
  `admin` TINYINT(1)  NULL DEFAULT 0 ,
  `facebook_id` BIGINT(20) NULL ,
  `first_name` VARCHAR(32) NULL ,
  `last_name` VARCHAR(32) NULL ,
  `activation` VARCHAR(64) NULL ,
  `created` DATETIME NULL ,
  `modified` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `admin` (`admin` ASC) ,
  INDEX `email` (`email` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `myezteam_myezteam`.`leagues`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `myezteam_myezteam`.`leagues` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `user_id` INT NULL ,
  `facebook_group` BIGINT(50) NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `created` DATETIME NULL ,
  `modified` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `leagues_user` (`user_id` ASC) ,
  CONSTRAINT `leagues_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `myezteam_myezteam`.`users` (`id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `myezteam_myezteam`.`teams`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `myezteam_myezteam`.`teams` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `league_id` INT NULL ,
  `user_id` INT NULL ,
  `name` VARCHAR(255) NULL ,
  `description` TEXT NULL ,
  `type` VARCHAR(32) NULL ,
  `default_location` TEXT NULL ,
  `google_calendar` VARCHAR(500) NULL ,
  `facebook_group` BIGINT(50) NULL ,
  `created` DATETIME NULL ,
  `modified` DATETIME NULL ,
  INDEX `teams_league` (`league_id` ASC) ,
  INDEX `teams_user` (`user_id` ASC) ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `teams_league`
    FOREIGN KEY (`league_id` )
    REFERENCES `myezteam_myezteam`.`leagues` (`id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `teams_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `myezteam_myezteam`.`users` (`id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `myezteam_myezteam`.`events`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `myezteam_myezteam`.`events` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `team_id` INT NOT NULL ,
  `user_id` INT NULL ,
  `date` DATE NULL ,
  `time` TIME NULL ,
  `name` VARCHAR(255) NULL ,
  `description` TEXT NULL ,
  `location` TEXT NULL ,
  `default_response` INT NULL DEFAULT 1 ,
  `facebook_event` BIGINT(50) NULL ,
  `google_calendar` VARCHAR(500) NULL ,
  `created` DATETIME NULL ,
  `modified` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `events_team` (`team_id` ASC) ,
  INDEX `events_user` (`user_id` ASC) ,
  INDEX `name` (`name` ASC) ,
  INDEX `date_time` (`date` DESC, `time` DESC) ,
  CONSTRAINT `events_team`
    FOREIGN KEY (`team_id` )
    REFERENCES `myezteam_myezteam`.`teams` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `events_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `myezteam_myezteam`.`users` (`id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `myezteam_myezteam`.`response_types`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `myezteam_myezteam`.`response_types` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(32) NOT NULL ,
  `color` CHAR(6) NULL ,
  `created` DATETIME NULL ,
  `modified` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `myezteam_myezteam`.`facebook_sessions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `myezteam_myezteam`.`facebook_sessions` (
  `uid` BIGINT(50) NOT NULL ,
  `session_key` VARCHAR(80) NULL ,
  `secret` VARCHAR(80) NULL ,
  `access_token` VARCHAR(80) NULL ,
  `sig` VARCHAR(80) NULL ,
  `expires` VARCHAR(80) NULL ,
  `base_domain` VARCHAR(80) NULL DEFAULT 'myeasyteam.com' ,
  `created` DATETIME NULL ,
  `modified` DATETIME NULL ,
  PRIMARY KEY (`uid`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `myezteam_myezteam`.`teams_managers`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `myezteam_myezteam`.`teams_managers` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `team_id` INT NOT NULL ,
  `user_id` INT NOT NULL ,
  `created` DATETIME NULL ,
  `modified` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `team_user` (`team_id` ASC, `user_id` ASC) ,
  INDEX `teams_managers_team` (`team_id` ASC) ,
  INDEX `teams_managers_user` (`user_id` ASC) ,
  CONSTRAINT `teams_managers_team`
    FOREIGN KEY (`team_id` )
    REFERENCES `myezteam_myezteam`.`teams` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `teams_managers_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `myezteam_myezteam`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `myezteam_myezteam`.`player_types`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `myezteam_myezteam`.`player_types` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(100) NOT NULL ,
  `description` TEXT NULL ,
  `created` INT NULL ,
  `modified` INT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `myezteam_myezteam`.`players`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `myezteam_myezteam`.`players` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `user_id` INT NOT NULL ,
  `player_type_id` INT NOT NULL ,
  `team_id` INT NOT NULL ,
  `created` DATETIME NULL ,
  `modified` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `players_user` (`user_id` ASC) ,
  INDEX `players_player_type` (`player_type_id` ASC) ,
  INDEX `players_team` (`team_id` ASC) ,
  UNIQUE INDEX `team_user` (`team_id` ASC, `user_id` ASC) ,
  CONSTRAINT `players_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `myezteam_myezteam`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `players_player_type`
    FOREIGN KEY (`player_type_id` )
    REFERENCES `myezteam_myezteam`.`player_types` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `players_team`
    FOREIGN KEY (`team_id` )
    REFERENCES `myezteam_myezteam`.`teams` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


;
CREATE USER `myezteam_user` IDENTIFIED BY '90a6692c71fd6861f58a648a15de817e';

grant ALL on TABLE `myezteam_myezteam`.`users` to myezteam_user;
grant ALL on TABLE `myezteam_myezteam`.`leagues` to myezteam_user;
grant ALL on TABLE `myezteam_myezteam`.`teams` to myezteam_user;
grant ALL on TABLE `myezteam_myezteam`.`events` to myezteam_user;
grant ALL on TABLE `myezteam_myezteam`.`response_types` to myezteam_user;
grant ALL on TABLE `myezteam_myezteam`.`teams_managers` to myezteam_user;
grant ALL on TABLE `myezteam_myezteam`.`facebook_sessions` to myezteam_user;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `myezteam_myezteam`.`player_types`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `myezteam_myezteam`;
INSERT INTO `myezteam_myezteam`.`player_types` (`id`, `name`, `description`, `created`, `modified`) VALUES ('1', 'Regular', NULL, NULL, NULL);
INSERT INTO `myezteam_myezteam`.`player_types` (`id`, `name`, `description`, `created`, `modified`) VALUES ('2', 'Sub', NULL, NULL, NULL);
INSERT INTO `myezteam_myezteam`.`player_types` (`id`, `name`, `description`, `created`, `modified`) VALUES ('3', 'Member', NULL, NULL, NULL);

COMMIT;
