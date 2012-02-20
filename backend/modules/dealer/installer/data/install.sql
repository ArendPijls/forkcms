CREATE  TABLE IF NOT EXISTS `dealer` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'The unique ID for this dealer.' ,
  `user_id` INT(11) NOT NULL COMMENT 'The ID of the user that added this dealer.' ,
  `language` VARCHAR(10) NOT NULL COMMENT 'The language of this dealer.' ,
  `name` VARCHAR(128) NOT NULL COMMENT 'The original author of this dealer.' ,
  `testimonial` TEXT NOT NULL COMMENT 'The actual dealer.' ,
  `hidden` ENUM('N', 'Y') NOT NULL COMMENT 'Whether this dealer is shown or not.' ,
  `sequence` INT(11) NOT NULL COMMENT 'The sequence of this dealer.' ,
  `created_on` DATETIME NOT NULL COMMENT 'The date and time this dealer was created.' ,
  `edited_on` DATETIME NOT NULL COMMENT 'The date and time this dealer was last edited.' ,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_unicode_ci;
