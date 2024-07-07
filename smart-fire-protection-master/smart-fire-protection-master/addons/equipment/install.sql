CREATE TABLE IF NOT EXISTS `__PREFIX__equipment_archive`  (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `model` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '设备型号',
    `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '设备名称',
    `parameter` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '设备参数',
    `amount` smallint(6) NOT NULL DEFAULT 0 COMMENT '设备数量',
    `supplier_id` bigint(20) NULL DEFAULT 0 COMMENT '供应商',
    `purchasetime` bigint(16) NULL DEFAULT 0 COMMENT '购置日期',
    `region` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '所在区域',
    `responsible_uid` bigint(20) NULL DEFAULT 0 COMMENT '负责人',
    `document` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '设备文档',
    `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '备注',
    `status` enum('normal','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal' COMMENT '状态',
    `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
    `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
    `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '设备档案表';

CREATE TABLE IF NOT EXISTS `__PREFIX__equipment_equipment`  (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `archive_id` bigint(20) NULL DEFAULT 0 COMMENT '设备档案ID',
    `coding` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '唯一编码',
    `equipment_code` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '设备编号',
    `work_status` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal' COMMENT '设备状态',
    `status` enum('normal','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal' COMMENT '状态',
    `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
    `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
    `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '设备明细表';

CREATE TABLE IF NOT EXISTS `__PREFIX__equipment_supplier`  (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `supplier_code` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '供应商编号',
    `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '供应商名称',
    `relationship` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '合作关系',
    `bank` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '开户银行',
    `bank_account` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '银行账号',
    `contact` char(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '联系人',
    `contact_mobile` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '联系电话',
    `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '备注',
    `status` enum('normal','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal' COMMENT '状态',
    `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
    `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
    `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '供应商管理表';

CREATE TABLE IF NOT EXISTS `__PREFIX__equipment_repair`  (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `archive_id` bigint(20) UNSIGNED NOT NULL COMMENT '档案ID',
    `equipment_id` bigint(20) UNSIGNED NOT NULL COMMENT '设备ID',
    `register_uid` bigint(20) NULL DEFAULT 0 COMMENT '报修人员',
    `registertime` bigint(16) NULL DEFAULT 0 COMMENT '报修日期',
    `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '报修登记',
    `register_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '报修配图',
    `repair_uid` bigint(20) NULL DEFAULT 0 COMMENT '维修人员',
    `repairtime` bigint(16) NULL DEFAULT 0 COMMENT '维修日期',
    `repair_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '维修登记',
    `repair_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '维修配图',
    `failure_cause_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '故障原因ID',
    `consuming` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '维修耗时',
    `status` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '维修状态',
    `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
    `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
    `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '设备维修表';

CREATE TABLE IF NOT EXISTS `__PREFIX__equipment_department`  (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '部门名称',
    `equipment_manage` tinyint(1) NOT NULL DEFAULT 0 COMMENT '设备管理权限',
    `status` enum('normal','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal' COMMENT '状态',
    `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
    `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
    `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '部门管理表';

CREATE TABLE IF NOT EXISTS `__PREFIX__equipment_staff`  (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `user_id` bigint(20) NULL DEFAULT 0 COMMENT '用户ID',
    `department_id` bigint(20) NULL DEFAULT 0 COMMENT '部门ID',
    `workno` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '工号',
    `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '职位',
    `openid` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT 'OPENID',
    `status` enum('normal','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal' COMMENT '状态',
    `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
    `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
    `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '员工管理表';

-- ----------------------------
-- 2.0.0 BEGIN
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__equipment_plan` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `coding` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '唯一编码',
    `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '计划名称',
    `type` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '计划类型',
    `periodicity` bigint(16) UNSIGNED NOT NULL DEFAULT 1 COMMENT '计划周期',
    `first_duetime` bigint(16) DEFAULT NULL COMMENT '首次执行日期',
    `last_duetime` bigint(16) DEFAULT NULL COMMENT '计划结束日期',
    `status` enum('normal','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal' COMMENT '状态',
    `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
    `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
    `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '设备计划表';

CREATE TABLE IF NOT EXISTS `__PREFIX__equipment_plan_archive` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `plan_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '计划ID',
    `archive_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '档案ID',
    `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
    `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '设备计划设备关联表';

CREATE TABLE IF NOT EXISTS `__PREFIX__equipment_plan_field` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `plan_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '计划ID',
    `label` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '字段名称',
    `name` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '字段命名',
    `type` char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '字段类型',
    `default` char(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '默认值',
    `options` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '选项',
    `attributes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '属性',
    `require` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否必填',
    `sort` tinyint(2) NOT NULL DEFAULT 99 COMMENT '排序',
    `status` enum('normal','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal' COMMENT '状态',
    `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
    `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
    `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '设备计划字段表';

CREATE TABLE IF NOT EXISTS `__PREFIX__equipment_plan_task` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `coding` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '唯一编码',
    `plan_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '计划ID',
    `equipment_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '设备ID',
    `task_uid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '处理人ID',
    `status` char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT '状态',
    `starttime` bigint(16) DEFAULT NULL COMMENT '开始时间',
    `duetime` bigint(16) DEFAULT NULL COMMENT '到期时间',
    `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
    `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
    `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '设备计划任务表';

CREATE TABLE IF NOT EXISTS `__PREFIX__equipment_plan_user` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `plan_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '计划ID',
    `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
    `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
    `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '设备计划用户关联表';

CREATE TABLE IF NOT EXISTS `__PREFIX__equipment_record` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `equipment_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '设备ID',
    `relate_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联ID',
    `add_uid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加用户ID',
    `name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '记录名称',
    `type` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '记录类型',
    `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '记录内容',
    `status` enum('normal','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal' COMMENT '状态',
    `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
    `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
    `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '设备记录表';

-- 【设备档案表】添加'设备参数'字段parameter
ALTER TABLE `__PREFIX__equipment_archive` ADD COLUMN `parameter` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '设备参数' AFTER `name`;

-- ----------------------------
-- 2.0.0 END
-- ----------------------------

-- ----------------------------
-- 2.2.0 BEGIN
-- ----------------------------

CREATE TABLE IF NOT EXISTS `__PREFIX__equipment_failure_cause` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `name` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '故障原因',
    `status` enum('normal','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal' COMMENT '状态',
    `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
    `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
    `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '设备故障原因表';

-- 【设备维修表】添加'故障原因ID'字段failure_cause_id；'维修耗时'字段consuming
ALTER TABLE `__PREFIX__equipment_repair` ADD COLUMN `failure_cause_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '故障原因ID' AFTER `repair_image`;
ALTER TABLE `__PREFIX__equipment_repair` ADD COLUMN `consuming` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '维修耗时' AFTER `failure_cause_id`;

-- ----------------------------
-- 2.2.0 END
-- ----------------------------

-- ----------------------------
-- 2.3.0 BEGIN
-- ----------------------------

-- 【部门管理表】添加'设备管理权限'字段equipment_manage
ALTER TABLE `__PREFIX__equipment_department` ADD COLUMN `equipment_manage` tinyint(1) NOT NULL DEFAULT 0 COMMENT '设备管理权限' AFTER `name`;

-- ----------------------------
-- 2.3.0 END
-- ----------------------------

-- ----------------------------
-- 2.5.0 BEGIN
-- ----------------------------

CREATE TABLE IF NOT EXISTS `__PREFIX__equipment_reminder_users` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `staff_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '员工ID',
    `type` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '类型',
    `status` enum('normal','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal' COMMENT '状态',
    `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
    `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
    `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '提醒人员表';

-- 【维修工单表】添加'工单编号'字段repair_code
ALTER TABLE `__PREFIX__equipment_repair` ADD COLUMN `repair_code` varchar(20) NULL DEFAULT '' COMMENT '工单编号' AFTER `id`;
ALTER TABLE `__PREFIX__equipment_repair` ADD COLUMN `assigntime` bigint(16) NULL DEFAULT 0 COMMENT '派单时间' AFTER `repair_uid`;

-- ----------------------------
-- 2.5.0 END
-- ----------------------------