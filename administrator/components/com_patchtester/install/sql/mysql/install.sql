CREATE TABLE IF NOT EXISTS `#__patchtester_pulls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pull_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(5000) NOT NULL DEFAULT '',
  `pull_url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__patchtester_tests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pull_id` int(11) NOT NULL,
  `data` longtext NOT NULL,
  `patched_by` int(11) NOT NULL,
  `applied` int(11) NOT NULL,
  `applied_version` varchar(25) NOT NULL,
  `comments` varchar(3000) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;
