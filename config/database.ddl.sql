/* -------------------------------------------------------------- */
/* Host     : localhost                                           */
/* Port     : 3306                                                */
/* Database : dynamic-quiz                                        */


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES 'utf8' */;

SET FOREIGN_KEY_CHECKS=0;

CREATE DATABASE `dynamic-quiz`
    CHARACTER SET 'utf8'
    COLLATE 'utf8_general_ci';

USE `dynamic-quiz`;

/* Structure for the `quiz` table : */

CREATE TABLE `quiz` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256) COLLATE utf8_general_ci NOT NULL,
  `category_id` INTEGER(11) NOT NULL,
  `api_url` VARCHAR(1024) COLLATE utf8_general_ci DEFAULT NULL,
  `number_of_questions` TINYINT(4) DEFAULT NULL,
  `difficulty` ENUM('easy','medium','hard') COLLATE utf8_general_ci NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY USING BTREE (`id`),
  UNIQUE KEY `name` USING BTREE (`name`)
) ENGINE=InnoDB
AUTO_INCREMENT=18 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
;

/* Structure for the `category` table : */

CREATE TABLE `category` (
  `id` INTEGER(11) NOT NULL,
  `name` VARCHAR(1024) COLLATE utf8_general_ci DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY USING BTREE (`id`)
) ENGINE=InnoDB
ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
;

/* Structure for the `question` table : */

CREATE TABLE `question` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `quiz_id` INTEGER(11) NOT NULL,
  `text` TEXT COLLATE utf8_general_ci NOT NULL,
  `question_number` TINYINT(4) NOT NULL,
  `correct_answer` VARCHAR(1024) COLLATE utf8_general_ci DEFAULT NULL,
  `category_id` INTEGER(11) NOT NULL COMMENT 'denormalized to make the report easier',
  `difficulty` ENUM('easy','medium','hard') COLLATE utf8_general_ci NOT NULL,
  `type` ENUM('boolean','multiple') COLLATE utf8_general_ci NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY USING BTREE (`id`),
  KEY `question_fk1` USING BTREE (`quiz_id`),
  KEY `question_fk2` USING BTREE (`category_id`),
  KEY `question_idx1` USING BTREE (`question_number`, `quiz_id`),
  CONSTRAINT `question_fk1` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `question_fk2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB
AUTO_INCREMENT=57 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
;

/* Structure for the `answer` table : */

CREATE TABLE `answer` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `question_id` INTEGER(11) NOT NULL,
  `value` VARCHAR(1024) COLLATE utf8_general_ci NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY USING BTREE (`id`),
  KEY `answer_fk1` USING BTREE (`question_id`),
  CONSTRAINT `answer_fk1` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
AUTO_INCREMENT=333 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
;

/* Structure for the `user` table : */

CREATE TABLE `user` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256) COLLATE utf8_general_ci NOT NULL,
  `remote_ip` VARCHAR(128) COLLATE utf8_general_ci DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY USING BTREE (`id`),
  UNIQUE KEY `name` USING BTREE (`name`)
) ENGINE=InnoDB
AUTO_INCREMENT=26 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
;

/* Structure for the `user_answer` table : */

CREATE TABLE `user_answer` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `user_id` INTEGER(11) NOT NULL,
  `answer_id` INTEGER(11) NOT NULL,
  `question_id` INTEGER(11) NOT NULL COMMENT 'denormalized to make queries easyer and faster (first non-answered question of an user)',
  `correct_answer` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` INTEGER(11) DEFAULT NULL,
  PRIMARY KEY USING BTREE (`id`),
  KEY `user_answer_fk2` USING BTREE (`answer_id`),
  KEY `user_answer_idx1` USING BTREE (`correct_answer`),
  KEY `user_answer_idx2` USING BTREE (`user_id`, `correct_answer`),
  CONSTRAINT `user_answer_fk1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `user_answer_fk2` FOREIGN KEY (`answer_id`) REFERENCES `answer` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB
AUTO_INCREMENT=18 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
;

/* Data for the `category` table  (LIMIT 0,500) */

INSERT INTO `category` (`id`, `name`, `created_at`) VALUES
  (9,'General Knowledge','2019-02-12 23:57:41'),
  (10,'Entertainment: Books','2019-02-12 23:57:41'),
  (11,'Entertainment: Film','2019-02-12 23:57:41'),
  (12,'Entertainment: Music','2019-02-12 23:57:41'),
  (13,'Entertainment: Musicals & Theatres','2019-02-12 23:57:41'),
  (14,'Entertainment: Television','2019-02-12 23:57:41'),
  (15,'Entertainment: Video Games','2019-02-12 23:57:41'),
  (16,'Entertainment: Board Games','2019-02-12 23:57:41'),
  (17,'Science & Nature','2019-02-12 23:57:41'),
  (18,'Science: Computers','2019-02-12 23:57:41'),
  (19,'Science: Mathematics','2019-02-12 23:57:41'),
  (20,'Mythology','2019-02-12 23:57:41'),
  (21,'Sports','2019-02-12 23:57:41'),
  (22,'Geography','2019-02-12 23:57:41'),
  (23,'History','2019-02-12 23:57:41'),
  (24,'Politics','2019-02-12 23:57:41'),
  (25,'Art','2019-02-12 23:57:41'),
  (26,'Celebrities','2019-02-12 23:57:41'),
  (27,'Animals','2019-02-12 23:57:41'),
  (28,'Vehicles','2019-02-12 23:57:41'),
  (29,'Entertainment: Comics','2019-02-12 23:57:41'),
  (30,'Science: Gadgets','2019-02-12 23:57:41'),
  (31,'Entertainment: Japanese Anime & Manga','2019-02-12 23:57:41'),
  (32,'Entertainment: Cartoon & Animations','2019-02-12 23:57:41');
COMMIT;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;