CREATE TABLE IF NOT EXISTS `__PREFIX__screen_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `code` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT '编码',
  `title` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '标题',
  `desc` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '介绍',
  `type` tinyint(255) unsigned DEFAULT '1' COMMENT '类型:1=模型,2=sql,3=事件(hook)',
  `model_content` text COLLATE utf8mb4_bin COMMENT '模型配置',
  `sql_content` text COLLATE utf8mb4_bin COMMENT 'sql配置',
  `hook_name` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '事件名称',
  `params` text COLLATE utf8mb4_bin COMMENT '查询参数',
  `tags` varchar(800) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '标签',
  `result` text COLLATE utf8mb4_bin COMMENT '返回数据',
  `key_list` text COLLATE utf8mb4_bin COMMENT '数据key',
  `deletetime` int(11) DEFAULT NULL COMMENT '删除时间',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='数据集';

CREATE TABLE IF NOT EXISTS `__PREFIX__screen_excel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `code` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT '编码',
  `title` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '标题',
  `desc` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '介绍',
  `content` longtext COLLATE utf8mb4_bin COMMENT '数据',
  `status` enum('normal','hidden') COLLATE utf8mb4_bin DEFAULT 'normal' COMMENT '状态:normal=显示,hidden=隐藏',
  `author` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '作者',
  `data_code` varchar(3000) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '数据集code',
  `data_param` varchar(3000) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '数据参数',
  `deletetime` int(11) DEFAULT NULL COMMENT '删除时间',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='Excel报表';

CREATE TABLE IF NOT EXISTS `__PREFIX__screen_page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `code` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT '编码',
  `title` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '标题',
  `desc` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '介绍',
  `content` longtext COLLATE utf8mb4_bin COMMENT '大屏数据',
  `status` enum('normal','hidden') COLLATE utf8mb4_bin DEFAULT 'normal' COMMENT '状态:normal=显示,hidden=隐藏',
  `author` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '作者',
  `deletetime` int(11) DEFAULT NULL COMMENT '删除时间',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='大屏管理';

CREATE TABLE IF NOT EXISTS `__PREFIX__screen_share` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `page_id` int(255) NOT NULL COMMENT '大屏id',
  `excel_id` int(255) DEFAULT NULL COMMENT 'excel_ID',
  `code` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '分享标识',
  `share_token` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'token',
  `status` enum('normal','hidden') COLLATE utf8mb4_bin DEFAULT 'normal' COMMENT '状态:normal=开启,hidden=关闭',
  `remark` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '备注',
  `end_time` int(11) DEFAULT NULL COMMENT '截止日期',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(11) DEFAULT NULL COMMENT '更新时间',
  `deletetime` int(11) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `code` (`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='分享记录';

--
-- 1.0.1
-- 分享表添加 excel_id
ALTER TABLE `__PREFIX__screen_share`
ADD COLUMN `excel_id` int(255) NULL DEFAULT NULL COMMENT 'excel_ID' AFTER `page_id`;