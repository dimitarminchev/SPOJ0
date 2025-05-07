--
-- User `spoj0_admin`
--
USE mysql;
CREATE USER 'spoj0_admin'@'localhost' IDENTIFIED BY 'stancho3';
GRANT ALL ON *.* TO 'spoj0_admin'@'localhost';
FLUSH PRIVILEGES;

--
-- Database `spoj0`
--
CREATE DATABASE IF NOT EXISTS spoj0;
USE spoj0;

--
-- Table `contests`
--
DROP TABLE IF EXISTS `contests`;
CREATE TABLE `contests` (
  `contest_id` int(11) NOT NULL auto_increment,
  `set_code` char(64) NOT NULL COMMENT 'the contest short name (like fmi-2007-03-04)',
  `name` char(128) NOT NULL COMMENT 'full name (like "Вътрешна тренировка на fmi")',
  `start_time` datetime NOT NULL COMMENT 'from what time the contest will be visible',
  `duration` int(11) NOT NULL COMMENT 'how long will it be in minutes (usually 300)',
  `show_sources` int(11) NOT NULL COMMENT 'whether to show the source after the contest',
  `about` text NOT NULL COMMENT 'information about the contest',
  PRIMARY KEY  (`contest_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User accounts (may be teams also)';

--
-- Table `problems`
--
DROP TABLE IF EXISTS `problems`;
CREATE TABLE `problems` (
  `problem_id` int(11) NOT NULL auto_increment,
  `contest_id` int(11) NOT NULL,
  `letter` char(16) NOT NULL COMMENT 'The problem letter. Must correspond to its directory.',
  `name` char(64) NOT NULL COMMENT 'the full name of the problem',
  `time_limit` int(11) NOT NULL COMMENT 'the time limit in seconds',
  `about` text NOT NULL COMMENT 'notes about the problem',
  PRIMARY KEY  (`problem_id`),
  KEY `new_fk_constraint` (`contest_id`),
  CONSTRAINT `new_fk_constraint` FOREIGN KEY (`contest_id`) REFERENCES `contests` (`contest_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table `users`
--
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL auto_increment,
  `name` char(16) NOT NULL COMMENT 'the username (for login)',
  `pass_md5` char(64) NOT NULL,
  `display_name` char(64) NOT NULL COMMENT 'Full name (ex: coaches - Manev, Sredkov, Bogdanov)',
  `about` text NOT NULL COMMENT 'about the user',
  `hidden` int(11) NOT NULL default 0 COMMENT 'whether the submits of this user does not show come in the board',
  PRIMARY KEY  (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User accounts (may be teams also)';

LOCK TABLES `users` WRITE;
INSERT INTO `users` VALUES (1,'admin',MD5('admin'),'Administrator','Administrator', 0);
INSERT INTO `users` VALUES (2,'user',MD5('user'),'User','User', 0);
INSERT INTO `users` VALUES (3,'milo','83e4a96aed96436c621b9809e258b309','Milo Sredkov','Developer', 0);
INSERT INTO `users` VALUES (4,'mitko','d880e4a4b8a80eb33c1c40604930b79c','Dimitar Minchev','Developer', 0);
UNLOCK TABLES;

--
-- Table `runs`
--
DROP TABLE IF EXISTS `runs`;
CREATE TABLE `runs` (
  `run_id` int(11) NOT NULL auto_increment,
  `problem_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `submit_time` datetime NOT NULL COMMENT 'when the run is submited',
  `language` char(16) NOT NULL COMMENT 'java, cpp ...',
  `source_code` mediumtext NOT NULL COMMENT 'the whole source code',
  `source_name` char(32) NOT NULL COMMENT 'may be needed for java, or may be autodetected',
  `about` text NOT NULL COMMENT 'notes about the code may be present',
  `status` char(16) NOT NULL COMMENT 'waiting, judging, ok, wa... ',
  `log` text NOT NULL COMMENT 'execution details',
  PRIMARY KEY  (`run_id`),
  KEY `fk_problems` (`problem_id`),
  KEY `fk_users` (`user_id`),
  CONSTRAINT `fk_problems` FOREIGN KEY (`problem_id`) REFERENCES `problems` (`problem_id`),
  CONSTRAINT `fk_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table `news`
--
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `new_id` int(11) NOT NULL auto_increment,
  `new_time` datetime NOT NULL COMMENT 'when the new is submited',
  `file` char(64) NOT NULL COMMENT 'the file that the new came from',
  `topic` char(128) NOT NULL COMMENT 'the title of the new',
  `content` text NOT NULL COMMENT 'the new contents',
  PRIMARY KEY  (`new_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `news` WRITE;
INSERT INTO `news` (`new_id`, `new_time`, `file`, `topic`, `content`) VALUES
(1, '2013-10-01 10:00:00', '', 'Information', 'Simple Programming Contests Online Judge System, developed by Milo Sredkov and available for download from <a target="_blank" href="http://code.google.com/p/spoj0/">Google Code</a>.'),
(2, '2016-12-23 19:30:00', '', 'Update', 'Updated version of SPOJ is developed by <a target="_blank" href="http://www.minchev.eu">Dimitar Minchev</a> and available for download from <a target="_blank" href="https://github.com/dimitarminchev/spoj0/">GitHub</a>, more information <a target="_blank" href="http://www.minchev.eu/spoj-update/">here</a>. ');
UNLOCK TABLES;

-- 
-- Table  `questions` 
-- 
DROP TABLE IF EXISTS `questions`;
CREATE TABLE `questions` (
  `question_id` int(11) NOT NULL AUTO_INCREMENT,
  `problem_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `question_time` datetime NOT NULL COMMENT 'when the question is submited',
  `content` text NOT NULL COMMENT 'the questions contents',
  `status` char(32) NOT NULL,
  `answer_time` datetime NOT NULL COMMENT 'when the answer is submited',
  `answer_content` text NOT NULL COMMENT 'the answer contents',
  PRIMARY KEY (`question_id`),
  KEY `fk2_problems` (`problem_id`),
  KEY `fk2_users` (`user_id`),
  CONSTRAINT `fk2_problems` FOREIGN KEY (`problem_id`) REFERENCES `problems`
(`problem_id`),
  CONSTRAINT `fk2_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
