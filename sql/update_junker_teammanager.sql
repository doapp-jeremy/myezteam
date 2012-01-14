ALTER TABLE junker_teammanager.users ADD facebook_id BIGINT(50) AFTER id;
ALTER TABLE junker_teammanager.teams ADD facebook_group BIGINT(50) AFTER id;
ALTER TABLE junker_teammanager.events ADD facebook_event BIGINT(50);
ALTER TABLE junker_teammanager.events ADD `date` DATE AFTER name;
ALTER TABLE junker_teammanager.events ADD `time` TIME AFTER `date`;
UPDATE junker_teammanager.events SET `date`=(start + INTERVAL 6 HOUR);
UPDATE junker_teammanager.events SET `time`=(start + INTERVAL 6 HOUR);

delimiter |
CREATE TRIGGER new_event BEFORE INSERT ON junker_teammanager.events
  FOR EACH ROW BEGIN
    IF NEW.date IS NULL THEN
      SET NEW.date=(NEW.start + INTERVAL 6 HOUR);
      SET NEW.time=(NEW.start + INTERVAL 6 HOUR);
    END IF;
  END;
|
CREATE TRIGGER update_event BEFORE UPDATE ON junker_teammanager.events
  FOR EACH ROW BEGIN
    IF NEW.start != Old.start THEN
      SET NEW.date=(NEW.start + INTERVAL 6 HOUR);
      SET NEW.time=(NEW.start + INTERVAL 6 HOUR);
    END IF;
  END;
|
delimter ;

-- -----------------------------------------------------
-- Table `myezteam_myezteam`.`facebook_sessions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`facebook_sessions` (
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
CREATE  TABLE IF NOT EXISTS `junker_teammanager`.`teams_managers` (
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


