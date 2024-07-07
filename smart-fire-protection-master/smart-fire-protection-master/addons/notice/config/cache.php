<?php 
 return array (
  'table_name' => 'fa_notice,fa_notice_admin_mptemplate,fa_notice_event,fa_notice_template',
  'self_path' => '',
  'update_data' => '--
-- 1.0.2
-- 添加url字段url
-- 添加url标题字段urltitle
-- 添加url类型字段url_type
ALTER TABLE `__PREFIX__notice_template`
ADD COLUMN `url` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT \'url\' AFTER `createtime`,
ADD COLUMN `url_title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT \'url标题\' AFTER `url`,
ADD COLUMN `url_type` tinyint(255) UNSIGNED NULL DEFAULT 1 COMMENT \'url类型:1=链接,2=弹窗,3=新窗口\' AFTER `url_title`;


--
-- 1.0.3
-- 添订阅消息
ALTER TABLE `__PREFIX__notice`
MODIFY COLUMN `type` enum(\'msg\',\'email\',\'mptemplate\',\'miniapp\') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT \'消息类型:msg=站内通知,email=邮箱通知\' AFTER `platform`;
ALTER TABLE `__PREFIX__notice_event`
MODIFY COLUMN `type` set(\'msg\',\'email\',\'mptemplate\',\'miniapp\') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT \'支持类型:msg=站内通知,email=邮箱通知\' AFTER `platform`;
ALTER TABLE `__PREFIX__notice_template`
MODIFY COLUMN `type` enum(\'msg\',\'email\',\'mptemplate\',\'miniapp\') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT \'类型:msg=站内通知,email=邮箱通知\' AFTER `platform`;

--
-- 1.0.4
-- 短信通知
ALTER TABLE `__PREFIX__notice_event`
MODIFY COLUMN `type` set(\'msg\',\'email\',\'mptemplate\',\'miniapp\',\'sms\') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT \'支持类型:msg=站内通知,email=邮箱通知\' AFTER `platform`;
ALTER TABLE `__PREFIX__notice`
MODIFY COLUMN `type` enum(\'msg\',\'email\',\'mptemplate\',\'miniapp\',\'sms\') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT \'消息类型:msg=站内通知,email=邮箱通知\' AFTER `platform`;
ALTER TABLE `__PREFIX__notice_template`
MODIFY COLUMN `type` enum(\'msg\',\'email\',\'mptemplate\',\'miniapp\',\'sms\') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT \'类型:msg=站内通知,email=邮箱通知\' AFTER `platform`;',
);