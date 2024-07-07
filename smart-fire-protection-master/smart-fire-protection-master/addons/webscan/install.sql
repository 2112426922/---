CREATE TABLE `__PREFIX__webscan_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(10) NOT NULL,
  `page` varchar(100) DEFAULT NULL,
  `method` varchar(20) DEFAULT NULL,
  `rkey` varchar(50) DEFAULT NULL,
  `rdata` varchar(100) DEFAULT NULL,
  `user_agent` varchar(200) DEFAULT NULL,
  `request_url` varchar(200) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `ip` varchar(50) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=229 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `__PREFIX__webscan_verifies` (
  `nameid` int(32) NOT NULL AUTO_INCREMENT,
  `md5` varchar(32) NOT NULL DEFAULT '',
  `method` enum('local','official') NOT NULL DEFAULT 'official',
  `filename` varchar(254) NOT NULL DEFAULT '',
  `mktime` int(11) NOT NULL DEFAULT '0' COMMENT '最后修改时间',
  PRIMARY KEY (`nameid`)
) ENGINE=MyISAM AUTO_INCREMENT=36329 DEFAULT CHARSET=utf8;

