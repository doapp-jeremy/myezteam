SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `junker_teammanager` DEFAULT CHARACTER SET latin1 ;
USE `junker_teammanager` ;

-- -----------------------------------------------------
-- Table `junker_teammanager`.`pictures`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`pictures` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  `path` VARCHAR(32) NOT NULL ,
  `user_id` INT(11) NOT NULL DEFAULT '3' ,
  PRIMARY KEY (`id`) ,
  INDEX `pictures_user` (`user_id` ASC) ,
  CONSTRAINT `pictures_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `junker_teammanager`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 4;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`time_zones`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`time_zones` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(32) NOT NULL ,
  `value` VARCHAR(32) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `name` (`name` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 5;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `facebook_id` BIGINT(50) NULL DEFAULT NULL ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  `email` VARCHAR(32) NOT NULL ,
  `password` VARCHAR(32) NULL DEFAULT NULL ,
  `picture_id` INT(11) NULL DEFAULT '1' ,
  `first_name` VARCHAR(32) NULL DEFAULT NULL ,
  `last_name` VARCHAR(32) NULL DEFAULT NULL ,
  `last_login` DATETIME NULL DEFAULT NULL ,
  `password_change_key` VARCHAR(32) NULL DEFAULT NULL ,
  `password_forgotten` INT(11) NOT NULL DEFAULT '0' ,
  `contact_id` INT(11) NULL DEFAULT NULL ,
  `feed_id` VARCHAR(32) NULL DEFAULT NULL ,
  `ip` VARCHAR(16) NULL DEFAULT NULL ,
  `time_zone_id` INT(11) NOT NULL DEFAULT '1' ,
  `value` INT(11) NOT NULL DEFAULT '1' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `email` (`email` ASC) ,
  UNIQUE INDEX `feed_id` (`feed_id` ASC) ,
  INDEX `facebook` (`facebook_id` ASC) ,
  INDEX `users_picture` (`picture_id` ASC) ,
  INDEX `first_name` (`first_name` ASC) ,
  INDEX `last_name` (`last_name` ASC) ,
  INDEX `last_login` (`last_login` ASC) ,
  INDEX `password_change_key` (`password_change_key` ASC) ,
  INDEX `users_time_zone` (`time_zone_id` ASC) ,
  CONSTRAINT `users_picture`
    FOREIGN KEY (`picture_id` )
    REFERENCES `junker_teammanager`.`pictures` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `users_time_zone`
    FOREIGN KEY (`time_zone_id` )
    REFERENCES `junker_teammanager`.`time_zones` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 542;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`leagues`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`leagues` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) NULL ,
  `facebook_group` BIGINT(50) NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `created` DATETIME NULL ,
  `modified` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `leagues_user` (`user_id` ASC) ,
  INDEX `name` (`name` ASC) ,
  CONSTRAINT `leagues_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `junker_teammanager`.`users` (`id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`teams`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`teams` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `facebook_group` BIGINT(50) NULL DEFAULT NULL ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  `name` VARCHAR(32) NOT NULL ,
  `description` VARCHAR(500) NULL DEFAULT NULL ,
  `visibility` SET('open','closed','private') NULL DEFAULT 'open' ,
  `type` VARCHAR(32) NULL DEFAULT NULL ,
  `user_id` INT(11) NOT NULL ,
  `picture_id` INT(11) NOT NULL DEFAULT '2' ,
  `default_location` VARCHAR(1000) NULL DEFAULT NULL ,
  `calendar_id` VARCHAR(500) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `teams_user` (`user_id` ASC) ,
  INDEX `teams_picture` (`picture_id` ASC) ,
  INDEX `facebook_group` (`facebook_group` ASC) ,
  INDEX `name` (`name` ASC) ,
  INDEX `type` (`type` ASC) ,
  INDEX `calendar` (`calendar_id` ASC) ,
  CONSTRAINT `teams_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `junker_teammanager`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `teams_picture`
    FOREIGN KEY (`picture_id` )
    REFERENCES `junker_teammanager`.`pictures` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 147;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`teams_managers`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`teams_managers` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `team_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  `created` DATETIME NULL ,
  `modified` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `team_user` (`team_id` ASC, `user_id` ASC) ,
  INDEX `teams_managers_team` (`team_id` ASC) ,
  INDEX `teams_managers_user` (`user_id` ASC) ,
  CONSTRAINT `teams_managers_team`
    FOREIGN KEY (`team_id` )
    REFERENCES `junker_teammanager`.`teams` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `teams_managers_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `junker_teammanager`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`response_types`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`response_types` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  `name` VARCHAR(32) NOT NULL ,
  `color` VARCHAR(6) NOT NULL DEFAULT '000000' ,
  PRIMARY KEY (`id`) ,
  INDEX `name` (`name` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 6;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`events`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`events` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  `name` VARCHAR(32) NOT NULL ,
  `date` DATE NULL DEFAULT NULL ,
  `time` TIME NULL DEFAULT NULL ,
  `start` DATETIME NOT NULL ,
  `end` DATETIME NOT NULL ,
  `description` VARCHAR(500) NULL DEFAULT NULL ,
  `visibility` SET('open','closed','private') NOT NULL DEFAULT 'open' ,
  `team_id` INT(11) NOT NULL ,
  `picture_id` INT(11) NOT NULL DEFAULT '3' ,
  `default_response` SET('yes','no','maybe','no_response') NULL DEFAULT 'no_response' ,
  `hours_behind` INT(11) NOT NULL DEFAULT '1' ,
  `location` VARCHAR(1000) NULL DEFAULT NULL ,
  `response_type_id` INT(11) NULL DEFAULT '1' ,
  `cal_event_id` VARCHAR(500) NULL DEFAULT NULL ,
  `facebook_event` BIGINT(50) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `events_team` (`team_id` ASC) ,
  INDEX `events_response_type` (`response_type_id` ASC) ,
  INDEX `start` (`start` ASC) ,
  INDEX `end` (`end` ASC) ,
  INDEX `name` (`name` ASC) ,
  INDEX `calendar` (`cal_event_id` ASC) ,
  INDEX `facebook_event` (`facebook_event` ASC) ,
  INDEX `events_picture` (`picture_id` ASC) ,
  CONSTRAINT `events_team`
    FOREIGN KEY (`team_id` )
    REFERENCES `junker_teammanager`.`teams` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `events_response_type`
    FOREIGN KEY (`response_type_id` )
    REFERENCES `junker_teammanager`.`response_types` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `events_picture`
    FOREIGN KEY (`picture_id` )
    REFERENCES `junker_teammanager`.`pictures` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 861;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`emails`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`emails` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  `title` VARCHAR(32) NULL DEFAULT NULL ,
  `days_before` INT(11) NULL DEFAULT NULL ,
  `content` VARCHAR(500) NULL DEFAULT NULL ,
  `sent` DATETIME NULL DEFAULT NULL ,
  `event_id` INT(11) NULL DEFAULT NULL ,
  `rsvp` TINYINT(4) NULL DEFAULT '1' ,
  `send` SET('now','days_before','send_on') NULL DEFAULT NULL ,
  `send_on` DATE NULL DEFAULT NULL ,
  `default` TINYINT(4) NULL DEFAULT '0' ,
  `team_id` INT(11) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `emails_event` (`event_id` ASC) ,
  INDEX `emails_team` (`team_id` ASC) ,
  INDEX `sent` (`sent` ASC) ,
  INDEX `rsvp` (`rsvp` ASC) ,
  INDEX `send_on` (`send_on` ASC) ,
  INDEX `default` (`default` ASC) ,
  CONSTRAINT `emails_event`
    FOREIGN KEY (`event_id` )
    REFERENCES `junker_teammanager`.`events` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `emails_team`
    FOREIGN KEY (`team_id` )
    REFERENCES `junker_teammanager`.`teams` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 2286;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`condition_types`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`condition_types` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(32) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `name` (`name` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 4;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`conditions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`conditions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `email_id` INT(11) NOT NULL ,
  `condition_type_id` INT(11) NOT NULL ,
  `number_of_players` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `conditions_email` (`email_id` ASC) ,
  INDEX `conditions_condition_type` (`condition_type_id` ASC) ,
  CONSTRAINT `conditions_email`
    FOREIGN KEY (`email_id` )
    REFERENCES `junker_teammanager`.`emails` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `conditions_condition_type`
    FOREIGN KEY (`condition_type_id` )
    REFERENCES `junker_teammanager`.`condition_types` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`player_types`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`player_types` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  `name` VARCHAR(32) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `name` (`name` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 4;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`players`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`players` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  `type` SET('regular','sub','member') NOT NULL DEFAULT 'regular' ,
  `team_id` INT(11) NOT NULL ,
  `user_id` INT(11) NULL DEFAULT NULL ,
  `player_type_id` INT(11) NOT NULL DEFAULT '1' ,
  PRIMARY KEY (`id`) ,
  INDEX `team_id` (`team_id` ASC) ,
  INDEX `players_user` (`user_id` ASC) ,
  INDEX `players_player_type` (`player_type_id` ASC) ,
  INDEX `players_team` (`team_id` ASC) ,
  CONSTRAINT `players_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `junker_teammanager`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `players_player_type`
    FOREIGN KEY (`player_type_id` )
    REFERENCES `junker_teammanager`.`player_types` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `players_team`
    FOREIGN KEY (`team_id` )
    REFERENCES `junker_teammanager`.`teams` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 908;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`condition_player_types`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`condition_player_types` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `condition_id` INT(11) NOT NULL ,
  `player_type_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `condition_player_types_condition` (`condition_id` ASC) ,
  INDEX `condition_player_types_player` (`player_type_id` ASC) ,
  UNIQUE INDEX `condition_player_types` (`player_type_id` ASC, `condition_id` ASC) ,
  CONSTRAINT `condition_player_types_condition`
    FOREIGN KEY (`condition_id` )
    REFERENCES `junker_teammanager`.`conditions` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `condition_player_types_player`
    FOREIGN KEY (`player_type_id` )
    REFERENCES `junker_teammanager`.`players` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`responses`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`responses` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  `response_type_id` INT(11) NOT NULL ,
  `player_id` INT(11) NOT NULL ,
  `event_id` INT(11) NOT NULL ,
  `comment` VARCHAR(500) NULL DEFAULT NULL ,
  `ip` VARCHAR(16) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `responses_response_type` (`response_type_id` ASC) ,
  INDEX `responses_player` (`player_id` ASC) ,
  INDEX `responses_event` (`event_id` ASC) ,
  INDEX `ip` (`ip` ASC) ,
  CONSTRAINT `responses_response_type`
    FOREIGN KEY (`response_type_id` )
    REFERENCES `junker_teammanager`.`response_types` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `responses_player`
    FOREIGN KEY (`player_id` )
    REFERENCES `junker_teammanager`.`players` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `responses_event`
    FOREIGN KEY (`event_id` )
    REFERENCES `junker_teammanager`.`events` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 6590;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`condition_response_types`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`condition_response_types` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `condition_id` INT(11) NOT NULL ,
  `response_type_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `condition_response_type_condition` (`condition_id` ASC) ,
  INDEX `condition_response_type_response` (`response_type_id` ASC) ,
  UNIQUE INDEX `condition_response_type` (`condition_id` ASC, `response_type_id` ASC) ,
  CONSTRAINT `condition_response_type_condition`
    FOREIGN KEY (`condition_id` )
    REFERENCES `junker_teammanager`.`conditions` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `condition_response_type_response`
    FOREIGN KEY (`response_type_id` )
    REFERENCES `junker_teammanager`.`responses` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`email_player_types`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`email_player_types` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `email_id` INT(11) NOT NULL ,
  `player_type_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `email_player_types_email` (`email_id` ASC) ,
  INDEX `email_player_types_player` (`player_type_id` ASC) ,
  UNIQUE INDEX `email_player_type` (`email_id` ASC, `player_type_id` ASC) ,
  CONSTRAINT `email_player_types_email`
    FOREIGN KEY (`email_id` )
    REFERENCES `junker_teammanager`.`emails` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `email_player_types_player`
    FOREIGN KEY (`player_type_id` )
    REFERENCES `junker_teammanager`.`players` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 2465;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`email_response_types`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`email_response_types` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `email_id` INT(11) NOT NULL ,
  `response_type_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `email_response_types_email` (`email_id` ASC) ,
  INDEX `email_response_types_reponse_type` (`response_type_id` ASC) ,
  UNIQUE INDEX `email_response_type` (`email_id` ASC, `response_type_id` ASC) ,
  CONSTRAINT `email_response_types_email`
    FOREIGN KEY (`email_id` )
    REFERENCES `junker_teammanager`.`emails` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `email_response_types_reponse_type`
    FOREIGN KEY (`response_type_id` )
    REFERENCES `junker_teammanager`.`response_types` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 4043;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`errors`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`errors` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  `function` VARCHAR(100) NULL DEFAULT NULL ,
  `message` VARCHAR(500) NULL DEFAULT NULL ,
  `user_Id` INT(11) NULL DEFAULT NULL ,
  `team_id` INT(11) NULL DEFAULT NULL ,
  `ip` VARCHAR(32) NULL DEFAULT NULL ,
  `email_id` INT(11) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 863;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`facebook_sessions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`facebook_sessions` (
  `uid` BIGINT(50) NOT NULL ,
  `session_key` VARCHAR(80) NULL DEFAULT NULL ,
  `secret` VARCHAR(80) NULL DEFAULT NULL ,
  `access_token` VARCHAR(80) NULL DEFAULT NULL ,
  `sig` VARCHAR(80) NULL DEFAULT NULL ,
  `expires` VARCHAR(80) NULL DEFAULT NULL ,
  `base_domain` VARCHAR(80) NULL DEFAULT 'www.myeasyteam.com' ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`uid`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`topics`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`topics` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  `team_id` INT(11) NULL DEFAULT NULL ,
  `event_id` INT(11) NULL DEFAULT NULL ,
  `user_id` INT(11) NOT NULL ,
  `title` VARCHAR(32) NOT NULL ,
  `ip` VARCHAR(16) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `title` (`title` ASC) ,
  INDEX `topics_team` (`team_id` ASC) ,
  INDEX `topics_event` (`event_id` ASC) ,
  INDEX `topics_user` (`user_id` ASC) ,
  CONSTRAINT `topics_team`
    FOREIGN KEY (`team_id` )
    REFERENCES `junker_teammanager`.`topics` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `topics_event`
    FOREIGN KEY (`event_id` )
    REFERENCES `junker_teammanager`.`events` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `topics_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `junker_teammanager`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 59;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`posts`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`posts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  `topic_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  `text` VARCHAR(5000) NULL DEFAULT NULL ,
  `ip` VARCHAR(16) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `posts_user` (`user_id` ASC) ,
  INDEX `posts_topic` (`topic_id` ASC) ,
  CONSTRAINT `posts_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `junker_teammanager`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `posts_topic`
    FOREIGN KEY (`topic_id` )
    REFERENCES `junker_teammanager`.`topics` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 214;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`messages`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`messages` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  `msg` VARCHAR(500) NULL DEFAULT NULL ,
  `title` VARCHAR(100) NULL DEFAULT NULL ,
  `link` VARCHAR(100) NULL DEFAULT NULL ,
  `response_id` INT(11) NULL DEFAULT NULL ,
  `event_id` INT(11) NULL DEFAULT NULL ,
  `user_id` INT(11) NULL DEFAULT NULL ,
  `del` TINYINT(4) NOT NULL DEFAULT '0' ,
  `save` TINYINT(4) NOT NULL DEFAULT '0' ,
  `topic_id` INT(11) NULL DEFAULT NULL ,
  `post_id` INT(11) NULL DEFAULT NULL ,
  `error_id` INT(11) NULL DEFAULT NULL ,
  `new_user_id` INT(11) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `messages_response` (`response_id` ASC) ,
  INDEX `messages_event` (`event_id` ASC) ,
  INDEX `messages_user` (`user_id` ASC) ,
  INDEX `messages_topic` (`topic_id` ASC) ,
  INDEX `messages_post` (`post_id` ASC) ,
  INDEX `messages_error` (`error_id` ASC) ,
  INDEX `messages_new_user` (`new_user_id` ASC) ,
  CONSTRAINT `messages_response`
    FOREIGN KEY (`response_id` )
    REFERENCES `junker_teammanager`.`responses` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `messages_event`
    FOREIGN KEY (`event_id` )
    REFERENCES `junker_teammanager`.`events` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `messages_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `junker_teammanager`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `messages_topic`
    FOREIGN KEY (`topic_id` )
    REFERENCES `junker_teammanager`.`topics` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `messages_post`
    FOREIGN KEY (`post_id` )
    REFERENCES `junker_teammanager`.`posts` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `messages_error`
    FOREIGN KEY (`error_id` )
    REFERENCES `junker_teammanager`.`errors` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `messages_new_user`
    FOREIGN KEY (`new_user_id` )
    REFERENCES `junker_teammanager`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 30354;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`setting_types`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`setting_types` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  `name` VARCHAR(32) NOT NULL ,
  `description` VARCHAR(500) NULL DEFAULT NULL ,
  `default` TINYINT(4) NULL DEFAULT '1' ,
  PRIMARY KEY (`id`) ,
  INDEX `name` (`name` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 2;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`teams_users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`teams_users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  `user_id` INT(11) NOT NULL ,
  `team_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `team_user` (`user_id` ASC, `team_id` ASC) ,
  INDEX `teams_users_team` (`team_id` ASC) ,
  INDEX `teams_users_user` (`user_id` ASC) ,
  CONSTRAINT `teams_users_team`
    FOREIGN KEY (`team_id` )
    REFERENCES `junker_teammanager`.`teams` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `teams_users_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `junker_teammanager`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 195;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`user_emails`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`user_emails` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  `user_id` INT(11) NOT NULL ,
  `email` VARCHAR(100) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `email` (`email` ASC) ,
  INDEX `user_emails_user` (`user_id` ASC) ,
  CONSTRAINT `user_emails_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `junker_teammanager`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 53;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`user_ips`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`user_ips` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  `user_id` INT(11) NULL DEFAULT NULL ,
  `ip` VARCHAR(16) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `ip` (`ip` ASC) ,
  INDEX `user_ips_user` (`user_id` ASC) ,
  CONSTRAINT `user_ips_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `junker_teammanager`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 45;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`user_settings`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`user_settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) NOT NULL ,
  `setting_type_id` INT(11) NOT NULL ,
  `value` TINYINT(4) NOT NULL DEFAULT '1' ,
  PRIMARY KEY (`id`) ,
  INDEX `user_settings_user` (`user_id` ASC) ,
  INDEX `user_settings_setting_type` (`setting_type_id` ASC) ,
  CONSTRAINT `user_settings_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `junker_teammanager`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `user_settings_setting_type`
    FOREIGN KEY (`setting_type_id` )
    REFERENCES `junker_teammanager`.`setting_types` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 78;


-- -----------------------------------------------------
-- Table `junker_teammanager`.`users_users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`users_users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  `user_id` INT(11) NOT NULL ,
  `contact_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `contact` (`contact_id` ASC) ,
  INDEX `users_users_user` (`user_id` ASC) ,
  CONSTRAINT `users_users_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `junker_teammanager`.`users_users` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 3748;

USE `junker_teammanager`;

DELIMITER $$
USE `junker_teammanager`$$














CREATE
DEFINER=`junker_webuser`@`localhost`
TRIGGER `junker_teammanager`.`new_event`
BEFORE INSERT ON `junker_teammanager`.`events`
FOR EACH ROW
BEGIN
    IF NEW.date IS NULL THEN
      SET NEW.date=(NEW.start + INTERVAL 6 HOUR);
      SET NEW.time=(NEW.start + INTERVAL 6 HOUR);
    END IF;
  END$$

USE `junker_teammanager`$$














CREATE
DEFINER=`junker_webuser`@`localhost`
TRIGGER `junker_teammanager`.`update_event`
BEFORE UPDATE ON `junker_teammanager`.`events`
FOR EACH ROW
BEGIN
    IF NEW.start != Old.start THEN
      SET NEW.date=(NEW.start + INTERVAL 6 HOUR);
      SET NEW.time=(NEW.start + INTERVAL 6 HOUR);
    END IF;
  END$$


DELIMITER ;

;
CREATE USER `myezteam_user` IDENTIFIED BY '90a6692c71fd6861f58a648a15de817e';

grant ALL on   to myezteam_user;
grant ALL on   to myezteam_user;
grant ALL on   to myezteam_user;
grant ALL on   to myezteam_user;
grant ALL on   to myezteam_user;
grant ALL on   to myezteam_user;
grant ALL on   to myezteam_user;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
