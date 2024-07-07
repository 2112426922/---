CREATE TABLE IF NOT EXISTS `__PREFIX__notice` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) DEFAULT NULL COMMENT '消息名称',
  `event` varchar(255) DEFAULT NULL COMMENT '消息事件',
  `platform` enum('user','admin','mptemplate') DEFAULT 'user' COMMENT '平台:user=用户,admin=后台',
  `type` enum('msg','email','mptemplate','miniapp','sms') NOT NULL COMMENT '消息类型:msg=站内通知,email=邮箱通知',
  `to_id` int(255) NOT NULL COMMENT '接收人id',
  `content` text COMMENT '内容',
  `ext` text COMMENT '扩展',
  `notice_template_id` int(11) unsigned DEFAULT '0' COMMENT '消息模板',
  `readtime` bigint(16) DEFAULT NULL COMMENT '是否已读',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `notifications_notifiable_id_notifiable_type_index` (`to_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COMMENT='消息通知';

CREATE TABLE IF NOT EXISTS `__PREFIX__notice_admin_mptemplate` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `admin_id` int(11) DEFAULT NULL COMMENT '管理员',
  `nickname` varchar(255) DEFAULT NULL COMMENT '微信昵称',
  `avatar` varchar(255) DEFAULT NULL COMMENT '微信头像',
  `openid` varchar(100) DEFAULT NULL,
  `unionid` varchar(100) DEFAULT NULL,
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员绑定微信(模版消息)';

CREATE TABLE IF NOT EXISTS `__PREFIX__notice_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `platform` set('user','admin') NOT NULL DEFAULT 'user' COMMENT '支持平台:user=用户,admin=后台',
  `type` set('msg','email','mptemplate','miniapp','sms') NOT NULL COMMENT '支持类型:msg=站内通知,email=邮箱通知',
  `name` varchar(50) NOT NULL COMMENT '消息名称',
  `event` varchar(50) NOT NULL COMMENT '消息事件',
  `send_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发送次数',
  `send_fail_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发送失败次数',
  `content` text COMMENT '参数',
  `visible_switch` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态:0=关闭,1=启用',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `deletetime` bigint(16) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `event` (`event`) USING HASH
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COMMENT='消息事件';

CREATE TABLE IF NOT EXISTS `__PREFIX__notice_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `notice_event_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '事件',
  `platform` enum('user','admin') NOT NULL DEFAULT 'user' COMMENT '平台:user=用户,admin=后台',
  `type` enum('msg','email','mptemplate','miniapp','sms') NOT NULL COMMENT '类型:msg=站内通知,email=邮箱通知',
  `mptemplate_id` varchar(100) DEFAULT '' COMMENT '微信公众号模板id',
  `mptemplate_json` varchar(3000) DEFAULT NULL COMMENT '公众号模版数据',
  `content` text COMMENT '消息内容',
  `visible_switch` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态:0=关闭,1=启用',
  `send_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发送次数',
  `send_fail_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发送失败次数',
  `ext` varchar(255) DEFAULT NULL COMMENT '扩展数据',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `url` varchar(300) DEFAULT NULL COMMENT 'url',
  `url_title` varchar(100) DEFAULT NULL COMMENT 'url标题',
  `url_type` tinyint(255) unsigned DEFAULT '1' COMMENT 'url类型:1=链接,2=弹窗,3=新窗口',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COMMENT='消息模版';

--
-- 1.0.2
-- 添加url字段url
-- 添加url标题字段urltitle
-- 添加url类型字段url_type
ALTER TABLE `__PREFIX__notice_template`
ADD COLUMN `url` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'url' AFTER `createtime`,
ADD COLUMN `url_title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'url标题' AFTER `url`,
ADD COLUMN `url_type` tinyint(255) UNSIGNED NULL DEFAULT 1 COMMENT 'url类型:1=链接,2=弹窗,3=新窗口' AFTER `url_title`;


--
-- 1.0.3
-- 添订阅消息
ALTER TABLE `__PREFIX__notice`
MODIFY COLUMN `type` enum('msg','email','mptemplate','miniapp') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '消息类型:msg=站内通知,email=邮箱通知' AFTER `platform`;
ALTER TABLE `__PREFIX__notice_event`
MODIFY COLUMN `type` set('msg','email','mptemplate','miniapp') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '支持类型:msg=站内通知,email=邮箱通知' AFTER `platform`;
ALTER TABLE `__PREFIX__notice_template`
MODIFY COLUMN `type` enum('msg','email','mptemplate','miniapp') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '类型:msg=站内通知,email=邮箱通知' AFTER `platform`;

--
-- 1.0.4
-- 短信通知
ALTER TABLE `__PREFIX__notice_event`
MODIFY COLUMN `type` set('msg','email','mptemplate','miniapp','sms') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '支持类型:msg=站内通知,email=邮箱通知' AFTER `platform`;
ALTER TABLE `__PREFIX__notice`
MODIFY COLUMN `type` enum('msg','email','mptemplate','miniapp','sms') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '消息类型:msg=站内通知,email=邮箱通知' AFTER `platform`;
ALTER TABLE `__PREFIX__notice_template`
MODIFY COLUMN `type` enum('msg','email','mptemplate','miniapp','sms') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '类型:msg=站内通知,email=邮箱通知' AFTER `platform`;