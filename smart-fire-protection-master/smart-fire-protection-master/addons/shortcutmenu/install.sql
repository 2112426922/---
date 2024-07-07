CREATE TABLE IF NOT EXISTS `__PREFIX__shortcutmenu`
(
    `id` int
(
    11
) NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `admin_id` int
(
    11
) DEFAULT NULL COMMENT '账号名称',
    `auth_rule_id` int
(
    11
) DEFAULT NULL COMMENT '菜单名称',
    `url` varchar
(
    255
) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '菜单链接',
    `menu_color` varchar
(
    30
) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '菜单背景颜色',
    `is_shortcut_menu` int
(
    11
) DEFAULT '0' COMMENT '快捷菜单开关',
    PRIMARY KEY
(
    `id`
)
    ) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE =utf8mb4_general_ci COMMENT='快捷菜单';
