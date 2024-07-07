-- ----------------------------
-- Records of fa_equipment_archive
-- ----------------------------
INSERT INTO `__PREFIX__equipment_archive` (`id`, `model`, `name`, `parameter`, `amount`, `supplier_id`, `purchasetime`, `region`, `responsible_uid`, `document`, `remark`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (1, 'MPZ/2', '水基型灭火器2L', '灭火级别：1A 55B，瓶身材质：碳钢', 4, 1, 1667318400, '生产一车间', 201, '', '', 'normal', 1669691036, 1669691238, NULL);
INSERT INTO `__PREFIX__equipment_archive` (`id`, `model`, `name`, `parameter`, `amount`, `supplier_id`, `purchasetime`, `region`, `responsible_uid`, `document`, `remark`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (2, 'MC-T1650-HCS', '缠绕包装机', '', 2, 2, 1663257600, '仓库区', 201, '', '', 'normal', 1669691087, 1669691087, NULL);
INSERT INTO `__PREFIX__equipment_archive` (`id`, `model`, `name`, `parameter`, `amount`, `supplier_id`, `purchasetime`, `region`, `responsible_uid`, `document`, `remark`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (3, 'V-0.25/8', '空气压缩机', '', 5, 2, 1651334400, '生产一车间', 202, '', '', 'normal', 1669691123, 1669691123, NULL);
INSERT INTO `__PREFIX__equipment_archive` (`id`, `model`, `name`, `parameter`, `amount`, `supplier_id`, `purchasetime`, `region`, `responsible_uid`, `document`, `remark`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (4, 'JG-400A', '型材切割机', '单相交流串励电动机', 3, 2, 1645113600, '生产三车间', 202, '', NULL, 'normal', 1669704713, 1669704713, NULL);
-- ----------------------------
-- Records of fa_equipment_department
-- ----------------------------
INSERT INTO `__PREFIX__equipment_department` (`id`, `name`, `equipment_manage`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (1, '管理部门', 1, 'normal', 1669688745, 1669688745, NULL);
INSERT INTO `__PREFIX__equipment_department` (`id`, `name`, `equipment_manage`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (2, '维修部门', 1, 'normal', 1669688757, 1669688757, NULL);
INSERT INTO `__PREFIX__equipment_department` (`id`, `name`, `equipment_manage`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (3, '生产部门', 0, 'normal', 1669688764, 1669688764, NULL);
-- ----------------------------
-- Records of fa_equipment_equipment
-- ----------------------------
INSERT INTO `__PREFIX__equipment_equipment` (`id`, `archive_id`, `coding`, `equipment_code`, `work_status`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (1, 1, 'EEIGXANB', 'E221129-001', 'repairing', 'normal', 1669691036, 1669710501, NULL);
INSERT INTO `__PREFIX__equipment_equipment` (`id`, `archive_id`, `coding`, `equipment_code`, `work_status`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (2, 1, 'EINYEXSL', 'E221129-002', 'normal', 'normal', 1669691036, 1669691036, NULL);
INSERT INTO `__PREFIX__equipment_equipment` (`id`, `archive_id`, `coding`, `equipment_code`, `work_status`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (3, 1, 'EOQTYBHV', 'E221129-003', 'normal', 'normal', 1669691036, 1669691036, NULL);
INSERT INTO `__PREFIX__equipment_equipment` (`id`, `archive_id`, `coding`, `equipment_code`, `work_status`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (4, 1, 'ERCUWJEA', 'E221129-004', 'normal', 'normal', 1669691036, 1669691036, NULL);
INSERT INTO `__PREFIX__equipment_equipment` (`id`, `archive_id`, `coding`, `equipment_code`, `work_status`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (5, 2, 'EHBMOWAX', 'E221129-005', 'normal', 'normal', 1669691087, 1669710151, NULL);
INSERT INTO `__PREFIX__equipment_equipment` (`id`, `archive_id`, `coding`, `equipment_code`, `work_status`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (6, 2, 'EYANSPRF', 'E221129-006', 'normal', 'normal', 1669691087, 1669691087, NULL);
INSERT INTO `__PREFIX__equipment_equipment` (`id`, `archive_id`, `coding`, `equipment_code`, `work_status`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (7, 3, 'EMGSTAKY', 'E221129-007', 'normal', 'normal', 1669691123, 1669691123, NULL);
INSERT INTO `__PREFIX__equipment_equipment` (`id`, `archive_id`, `coding`, `equipment_code`, `work_status`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (8, 3, 'EHACRXVN', 'E221129-008', 'normal', 'normal', 1669691123, 1669691123, NULL);
INSERT INTO `__PREFIX__equipment_equipment` (`id`, `archive_id`, `coding`, `equipment_code`, `work_status`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (9, 3, 'ELBDIQPS', 'E221129-009', 'normal', 'normal', 1669691123, 1669691123, NULL);
INSERT INTO `__PREFIX__equipment_equipment` (`id`, `archive_id`, `coding`, `equipment_code`, `work_status`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (10, 3, 'EYIGJFET', 'E221129-010', 'normal', 'normal', 1669691123, 1669691123, NULL);
INSERT INTO `__PREFIX__equipment_equipment` (`id`, `archive_id`, `coding`, `equipment_code`, `work_status`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (11, 3, 'EZVKSIHW', 'E221129-011', 'normal', 'normal', 1669691123, 1669691123, NULL);
INSERT INTO `__PREFIX__equipment_equipment` (`id`, `archive_id`, `coding`, `equipment_code`, `work_status`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (12, 4, 'EPCYXZKF', 'E221129-012', 'normal', 'normal', 1669704713, 1669704713, NULL);
INSERT INTO `__PREFIX__equipment_equipment` (`id`, `archive_id`, `coding`, `equipment_code`, `work_status`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (13, 4, 'EJKLHDFY', 'E221129-013', 'normal', 'normal', 1669704713, 1669704713, NULL);
INSERT INTO `__PREFIX__equipment_equipment` (`id`, `archive_id`, `coding`, `equipment_code`, `work_status`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (14, 4, 'EVBUJDQT', 'E221129-014', 'normal', 'normal', 1669704713, 1669704713, NULL);
-- ----------------------------
-- Records of fa_equipment_failure_cause
-- ----------------------------
INSERT INTO `__PREFIX__equipment_failure_cause` (`id`, `name`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (1, '其他原因', 'normal', 1669709924, 1669709924, NULL);
INSERT INTO `__PREFIX__equipment_failure_cause` (`id`, `name`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (2, '配件质量问题', 'normal', 1669709929, 1669709929, NULL);
INSERT INTO `__PREFIX__equipment_failure_cause` (`id`, `name`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (3, '维护保养不到位', 'normal', 1669709935, 1669709935, NULL);
INSERT INTO `__PREFIX__equipment_failure_cause` (`id`, `name`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (4, '自然磨损', 'normal', 1669709945, 1669709945, NULL);
INSERT INTO `__PREFIX__equipment_failure_cause` (`id`, `name`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (5, '违规操作', 'normal', 1669709953, 1669709953, NULL);
-- ----------------------------
-- Records of fa_equipment_plan
-- ----------------------------
INSERT INTO `__PREFIX__equipment_plan` (`id`, `coding`, `name`, `type`, `periodicity`, `first_duetime`, `last_duetime`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (1, 'PHJXUSWY', '巡检计划001', 'inspection', 3, 1669651199, 1677599999, 'normal', 1669691444, 1669691444, NULL);
INSERT INTO `__PREFIX__equipment_plan` (`id`, `coding`, `name`, `type`, `periodicity`, `first_duetime`, `last_duetime`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (2, 'PXDJBCWO', '保养计划001', 'maintenance', 7, 1669651199, 1677254399, 'normal', 1669691683, 1669691683, NULL);
-- ----------------------------
-- Records of fa_equipment_plan_archive
-- ----------------------------
INSERT INTO `__PREFIX__equipment_plan_archive` (`id`, `plan_id`, `archive_id`, `createtime`, `deletetime`) VALUES (1, 1, 3, 1669691444, NULL);
INSERT INTO `__PREFIX__equipment_plan_archive` (`id`, `plan_id`, `archive_id`, `createtime`, `deletetime`) VALUES (2, 1, 2, 1669691444, NULL);
INSERT INTO `__PREFIX__equipment_plan_archive` (`id`, `plan_id`, `archive_id`, `createtime`, `deletetime`) VALUES (3, 2, 1, 1669691683, NULL);
-- ----------------------------
-- Records of fa_equipment_plan_field
-- ----------------------------
INSERT INTO `__PREFIX__equipment_plan_field` (`id`, `plan_id`, `label`, `name`, `type`, `default`, `options`, `attributes`, `require`, `sort`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (1, 1, '油位是否正常', 'input_0', 'radio', '', '[\"\\u6b63\\u5e38\",\"\\u4e0d\\u6b63\\u5e38\"]', NULL, 1, 99, 'normal', 1669691444, 1669691444, NULL);
INSERT INTO `__PREFIX__equipment_plan_field` (`id`, `plan_id`, `label`, `name`, `type`, `default`, `options`, `attributes`, `require`, `sort`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (2, 1, '油管是否渗漏', 'input_1', 'radio', '', '[\"\\u5426\",\"\\u662f\"]', NULL, 1, 99, 'normal', 1669691444, 1669691444, NULL);
INSERT INTO `__PREFIX__equipment_plan_field` (`id`, `plan_id`, `label`, `name`, `type`, `default`, `options`, `attributes`, `require`, `sort`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (3, 1, '螺栓是否紧固', 'input_2', 'radio', '', '[\"\\u662f\",\"\\u5426\"]', NULL, 1, 99, 'normal', 1669691444, 1669691444, NULL);
INSERT INTO `__PREFIX__equipment_plan_field` (`id`, `plan_id`, `label`, `name`, `type`, `default`, `options`, `attributes`, `require`, `sort`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (4, 1, '轴承是否卡涩损坏', 'input_3', 'radio', '', '[\"\\u5426\",\"\\u662f\"]', NULL, 1, 99, 'normal', 1669691444, 1669691444, NULL);
INSERT INTO `__PREFIX__equipment_plan_field` (`id`, `plan_id`, `label`, `name`, `type`, `default`, `options`, `attributes`, `require`, `sort`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (5, 2, '压力是否正常', 'input_0', 'radio', '', '[\"\\u6b63\\u5e38\",\"\\u8d85\\u538b\",\"\\u9700\\u5145\\u88c5\"]', NULL, 1, 99, 'normal', 1669691683, 1669691683, NULL);
INSERT INTO `__PREFIX__equipment_plan_field` (`id`, `plan_id`, `label`, `name`, `type`, `default`, `options`, `attributes`, `require`, `sort`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (6, 2, '保险销是否锈蚀', 'input_1', 'radio', '', '[\"\\u5426\",\"\\u662f\"]', NULL, 1, 99, 'normal', 1669691683, 1669691683, NULL);
INSERT INTO `__PREFIX__equipment_plan_field` (`id`, `plan_id`, `label`, `name`, `type`, `default`, `options`, `attributes`, `require`, `sort`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (7, 2, '筒体有无变形', 'input_2', 'radio', '', '[\"\\u5426\",\"\\u662f\"]', NULL, 1, 99, 'normal', 1669691683, 1669691683, NULL);
-- ----------------------------
-- Records of fa_equipment_plan_task
-- ----------------------------
INSERT INTO `__PREFIX__equipment_plan_task` (`id`, `coding`, `plan_id`, `equipment_id`, `task_uid`, `status`, `starttime`, `duetime`, `createtime`, `updatetime`, `deletetime`) VALUES (1, 'TIBLGWUD', 1, 5, 0, 'overdue', 1669392000, 1669651199, 1669691444, 1669711804, NULL);
INSERT INTO `__PREFIX__equipment_plan_task` (`id`, `coding`, `plan_id`, `equipment_id`, `task_uid`, `status`, `starttime`, `duetime`, `createtime`, `updatetime`, `deletetime`) VALUES (2, 'TESVCOND', 1, 6, 0, 'overdue', 1669392000, 1669651199, 1669691444, 1669711804, NULL);
INSERT INTO `__PREFIX__equipment_plan_task` (`id`, `coding`, `plan_id`, `equipment_id`, `task_uid`, `status`, `starttime`, `duetime`, `createtime`, `updatetime`, `deletetime`) VALUES (3, 'TMDOVLRN', 1, 7, 0, 'overdue', 1669392000, 1669651199, 1669691444, 1669711804, NULL);
INSERT INTO `__PREFIX__equipment_plan_task` (`id`, `coding`, `plan_id`, `equipment_id`, `task_uid`, `status`, `starttime`, `duetime`, `createtime`, `updatetime`, `deletetime`) VALUES (4, 'TNSIZDPF', 1, 8, 0, 'overdue', 1669392000, 1669651199, 1669691444, 1669711804, NULL);
INSERT INTO `__PREFIX__equipment_plan_task` (`id`, `coding`, `plan_id`, `equipment_id`, `task_uid`, `status`, `starttime`, `duetime`, `createtime`, `updatetime`, `deletetime`) VALUES (5, 'TIKGHBMX', 1, 9, 0, 'overdue', 1669392000, 1669651199, 1669691444, 1669711804, NULL);
INSERT INTO `__PREFIX__equipment_plan_task` (`id`, `coding`, `plan_id`, `equipment_id`, `task_uid`, `status`, `starttime`, `duetime`, `createtime`, `updatetime`, `deletetime`) VALUES (6, 'TYNMWJGE', 1, 10, 0, 'overdue', 1669392000, 1669651199, 1669691444, 1669711804, NULL);
INSERT INTO `__PREFIX__equipment_plan_task` (`id`, `coding`, `plan_id`, `equipment_id`, `task_uid`, `status`, `starttime`, `duetime`, `createtime`, `updatetime`, `deletetime`) VALUES (7, 'TCNTOJVW', 1, 11, 0, 'overdue', 1669392000, 1669651199, 1669691444, 1669711804, NULL);
INSERT INTO `__PREFIX__equipment_plan_task` (`id`, `coding`, `plan_id`, `equipment_id`, `task_uid`, `status`, `starttime`, `duetime`, `createtime`, `updatetime`, `deletetime`) VALUES (8, 'TJQWDIXY', 1, 5, 0, 'pending', 1669651200, 1669910399, 1669691444, 1669691444, NULL);
INSERT INTO `__PREFIX__equipment_plan_task` (`id`, `coding`, `plan_id`, `equipment_id`, `task_uid`, `status`, `starttime`, `duetime`, `createtime`, `updatetime`, `deletetime`) VALUES (9, 'THYZXMTU', 1, 6, 0, 'pending', 1669651200, 1669910399, 1669691444, 1669691444, NULL);
INSERT INTO `__PREFIX__equipment_plan_task` (`id`, `coding`, `plan_id`, `equipment_id`, `task_uid`, `status`, `starttime`, `duetime`, `createtime`, `updatetime`, `deletetime`) VALUES (10, 'TXIKOVJW', 1, 7, 0, 'pending', 1669651200, 1669910399, 1669691444, 1669691444, NULL);
INSERT INTO `__PREFIX__equipment_plan_task` (`id`, `coding`, `plan_id`, `equipment_id`, `task_uid`, `status`, `starttime`, `duetime`, `createtime`, `updatetime`, `deletetime`) VALUES (11, 'TEDLPYMT', 1, 8, 0, 'pending', 1669651200, 1669910399, 1669691444, 1669691444, NULL);
INSERT INTO `__PREFIX__equipment_plan_task` (`id`, `coding`, `plan_id`, `equipment_id`, `task_uid`, `status`, `starttime`, `duetime`, `createtime`, `updatetime`, `deletetime`) VALUES (12, 'TGFQELNI', 1, 9, 0, 'pending', 1669651200, 1669910399, 1669691444, 1669691444, NULL);
INSERT INTO `__PREFIX__equipment_plan_task` (`id`, `coding`, `plan_id`, `equipment_id`, `task_uid`, `status`, `starttime`, `duetime`, `createtime`, `updatetime`, `deletetime`) VALUES (13, 'TWDNEIJX', 1, 10, 201, 'finish', 1669651200, 1669910399, 1669691444, 1669710239, NULL);
INSERT INTO `__PREFIX__equipment_plan_task` (`id`, `coding`, `plan_id`, `equipment_id`, `task_uid`, `status`, `starttime`, `duetime`, `createtime`, `updatetime`, `deletetime`) VALUES (14, 'TXVFHJMY', 1, 11, 0, 'pending', 1669651200, 1669910399, 1669691444, 1669691444, NULL);
INSERT INTO `__PREFIX__equipment_plan_task` (`id`, `coding`, `plan_id`, `equipment_id`, `task_uid`, `status`, `starttime`, `duetime`, `createtime`, `updatetime`, `deletetime`) VALUES (15, 'TDIZOYPT', 1, 5, 0, 'pending', 1669910400, 1670169599, 1669691444, 1669691444, NULL);
INSERT INTO `__PREFIX__equipment_plan_task` (`id`, `coding`, `plan_id`, `equipment_id`, `task_uid`, `status`, `starttime`, `duetime`, `createtime`, `updatetime`, `deletetime`) VALUES (16, 'TBQCDUOH', 1, 6, 0, 'pending', 1669910400, 1670169599, 1669691444, 1669691444, NULL);
INSERT INTO `__PREFIX__equipment_plan_task` (`id`, `coding`, `plan_id`, `equipment_id`, `task_uid`, `status`, `starttime`, `duetime`, `createtime`, `updatetime`, `deletetime`) VALUES (17, 'THVBGQJO', 1, 7, 0, 'pending', 1669910400, 1670169599, 1669691444, 1669691444, NULL);
INSERT INTO `__PREFIX__equipment_plan_task` (`id`, `coding`, `plan_id`, `equipment_id`, `task_uid`, `status`, `starttime`, `duetime`, `createtime`, `updatetime`, `deletetime`) VALUES (18, 'TGAFMQRH', 1, 8, 0, 'pending', 1669910400, 1670169599, 1669691444, 1669691444, NULL);
INSERT INTO `__PREFIX__equipment_plan_task` (`id`, `coding`, `plan_id`, `equipment_id`, `task_uid`, `status`, `starttime`, `duetime`, `createtime`, `updatetime`, `deletetime`) VALUES (19, 'TVLRFGDX', 1, 9, 0, 'pending', 1669910400, 1670169599, 1669691444, 1669691444, NULL);
INSERT INTO `__PREFIX__equipment_plan_task` (`id`, `coding`, `plan_id`, `equipment_id`, `task_uid`, `status`, `starttime`, `duetime`, `createtime`, `updatetime`, `deletetime`) VALUES (20, 'TCUMXRWS', 1, 10, 0, 'pending', 1669910400, 1670169599, 1669691444, 1669691444, NULL);
-- ----------------------------
-- Records of fa_equipment_plan_user
-- ----------------------------
INSERT INTO `__PREFIX__equipment_plan_user` (`id`, `plan_id`, `user_id`, `createtime`, `deletetime`) VALUES (1, 1, 201, 1669691444, NULL);
INSERT INTO `__PREFIX__equipment_plan_user` (`id`, `plan_id`, `user_id`, `createtime`, `deletetime`) VALUES (2, 1, 202, 1669691444, NULL);
INSERT INTO `__PREFIX__equipment_plan_user` (`id`, `plan_id`, `user_id`, `createtime`, `deletetime`) VALUES (3, 2, 201, 1669691683, NULL);
-- ----------------------------
-- Records of fa_equipment_record
-- ----------------------------
INSERT INTO `__PREFIX__equipment_record` (`id`, `equipment_id`, `relate_id`, `add_uid`, `name`, `type`, `content`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (1, 5, 1, 201, '维修结果：已维修', 'repair', '[]', 'normal', 1669710151, 1669710151, NULL);
INSERT INTO `__PREFIX__equipment_record` (`id`, `equipment_id`, `relate_id`, `add_uid`, `name`, `type`, `content`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (2, 10, 13, 201, '巡检结果：正常', 'inspection', '{\"images\":[],\"remark\":\"\",\"form_data\":[{\"label\":\"\\u6cb9\\u4f4d\\u662f\\u5426\\u6b63\\u5e38\",\"value\":\"\\u6b63\\u5e38\"},{\"label\":\"\\u6cb9\\u7ba1\\u662f\\u5426\\u6e17\\u6f0f\",\"value\":\"\\u5426\"},{\"label\":\"\\u87ba\\u6813\\u662f\\u5426\\u7d27\\u56fa\",\"value\":\"\\u662f\"},{\"label\":\"\\u8f74\\u627f\\u662f\\u5426\\u5361\\u6da9\\u635f\\u574f\",\"value\":\"\\u5426\"}],\"work_status\":\"\\u6b63\\u5e38\"}', 'normal', 1669710239, 1669710239, NULL);
INSERT INTO `__PREFIX__equipment_record` (`id`, `equipment_id`, `relate_id`, `add_uid`, `name`, `type`, `content`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (3, 5, 1, 0, '巡检结果：已逾期', 'inspection', '[]', 'normal', 1669711804, 1669711804, NULL);
INSERT INTO `__PREFIX__equipment_record` (`id`, `equipment_id`, `relate_id`, `add_uid`, `name`, `type`, `content`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (4, 6, 2, 0, '巡检结果：已逾期', 'inspection', '[]', 'normal', 1669711804, 1669711804, NULL);
INSERT INTO `__PREFIX__equipment_record` (`id`, `equipment_id`, `relate_id`, `add_uid`, `name`, `type`, `content`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (5, 7, 3, 0, '巡检结果：已逾期', 'inspection', '[]', 'normal', 1669711804, 1669711804, NULL);
INSERT INTO `__PREFIX__equipment_record` (`id`, `equipment_id`, `relate_id`, `add_uid`, `name`, `type`, `content`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (6, 8, 4, 0, '巡检结果：已逾期', 'inspection', '[]', 'normal', 1669711804, 1669711804, NULL);
INSERT INTO `__PREFIX__equipment_record` (`id`, `equipment_id`, `relate_id`, `add_uid`, `name`, `type`, `content`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (7, 9, 5, 0, '巡检结果：已逾期', 'inspection', '[]', 'normal', 1669711804, 1669711804, NULL);
INSERT INTO `__PREFIX__equipment_record` (`id`, `equipment_id`, `relate_id`, `add_uid`, `name`, `type`, `content`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (8, 10, 6, 0, '巡检结果：已逾期', 'inspection', '[]', 'normal', 1669711804, 1669711804, NULL);
INSERT INTO `__PREFIX__equipment_record` (`id`, `equipment_id`, `relate_id`, `add_uid`, `name`, `type`, `content`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (9, 11, 7, 0, '巡检结果：已逾期', 'inspection', '[]', 'normal', 1669711804, 1669711804, NULL);
-- ----------------------------
-- Records of fa_equipment_repair
-- ----------------------------
INSERT INTO `__PREFIX__equipment_repair` (`id`, `repair_code`, `archive_id`, `equipment_id`, `register_uid`, `registertime`, `content`, `register_image`, `repair_uid`, `assigntime`, `repairtime`, `repair_content`, `repair_image`, `failure_cause_id`, `consuming`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (1, 'R221129-001', 2, 5, 203, 1669709883, '机器突然不工作了', '', 201, 1669701000, 1669710151, '违规操作导致异物卡住机器无法运作', '', 5, 268, 'repaired', 1669709883, 1669710151, NULL);
INSERT INTO `__PREFIX__equipment_repair` (`id`, `repair_code`, `archive_id`, `equipment_id`, `register_uid`, `registertime`, `content`, `register_image`, `repair_uid`, `assigntime`, `repairtime`, `repair_content`, `repair_image`, `failure_cause_id`, `consuming`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (2, 'R221129-002', 1, 1, 203, 1669710501, '保险销拉不出', '', 0, 0, 0, NULL, '', 0, 0, 'pending', 1669710501, 1669710501, NULL);
-- ----------------------------
-- Records of fa_equipment_staff
-- ----------------------------
INSERT INTO `__PREFIX__equipment_staff` (`id`, `user_id`, `department_id`, `workno`, `position`, `openid`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (1, 201, 2, 'T001', '技术总监', '', 'normal', 1669688805, 1669710388, NULL);
INSERT INTO `__PREFIX__equipment_staff` (`id`, `user_id`, `department_id`, `workno`, `position`, `openid`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (2, 202, 2, 'T008', '机修工', '', 'normal', 1669690876, 1669690918, NULL);
INSERT INTO `__PREFIX__equipment_staff` (`id`, `user_id`, `department_id`, `workno`, `position`, `openid`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (3, 203, 3, 'P002', '装配工', '', 'normal', 1669691760, 1669712262, NULL);
-- ----------------------------
-- Records of fa_equipment_supplier
-- ----------------------------
INSERT INTO `__PREFIX__equipment_supplier` (`id`, `supplier_code`, `name`, `relationship`, `bank`, `bank_account`, `contact`, `contact_mobile`, `remark`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (1, 'GYS-0001', '江山江博消防器材有限公司', 'special', '北京银行上海路支行', '8888888888888888', '李四', '13456789871', '', 'normal', 1669690703, 1669690703, NULL);
INSERT INTO `__PREFIX__equipment_supplier` (`id`, `supplier_code`, `name`, `relationship`, `bank`, `bank_account`, `contact`, `contact_mobile`, `remark`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES (2, 'GYS-0002', '特级机械供应有限公司', 'important', '临商银行银雀山支行', '123123123123123', '王五', '13898764857', '', 'normal', 1669690738, 1669690738, NULL);
-- ----------------------------
-- Records of fa_user
-- ----------------------------
INSERT INTO `__PREFIX__user` (`id`, `group_id`, `username`, `nickname`, `password`, `salt`, `email`, `mobile`, `avatar`, `level`, `gender`, `birthday`, `bio`, `money`, `score`, `successions`, `maxsuccessions`, `prevtime`, `logintime`, `loginip`, `loginfailure`, `joinip`, `jointime`, `createtime`, `updatetime`, `token`, `status`, `verification`) VALUES (201, 0, '13888888888', '李工', '4f370aa46e875d1868dbc0e3f984a49d', '1Sx6RE', '', '13888888888', '', 1, 0, NULL, '', 0.00, 0, 1, 1, 1669688805, 1669709986, '127.0.0.1', 0, '127.0.0.1', 1669688805, 1669688805, 1669709986, '', 'normal', '');
INSERT INTO `__PREFIX__user` (`id`, `group_id`, `username`, `nickname`, `password`, `salt`, `email`, `mobile`, `avatar`, `level`, `gender`, `birthday`, `bio`, `money`, `score`, `successions`, `maxsuccessions`, `prevtime`, `logintime`, `loginip`, `loginfailure`, `joinip`, `jointime`, `createtime`, `updatetime`, `token`, `status`, `verification`) VALUES (202, 0, '13666666666', '张工', 'f22fcb6d31e18c971a811192fecc8a13', 'i5Q8Io', '', '13666666666', '', 1, 0, NULL, '', 0.00, 0, 1, 1, 1669690876, 1669690876, '127.0.0.1', 0, '127.0.0.1', 1669690876, 1669690876, 1669690918, '', 'normal', '');
INSERT INTO `__PREFIX__user` (`id`, `group_id`, `username`, `nickname`, `password`, `salt`, `email`, `mobile`, `avatar`, `level`, `gender`, `birthday`, `bio`, `money`, `score`, `successions`, `maxsuccessions`, `prevtime`, `logintime`, `loginip`, `loginfailure`, `joinip`, `jointime`, `createtime`, `updatetime`, `token`, `status`, `verification`) VALUES (203, 0, '13555555555', '小王', 'b5c6360e246ede7bf0e16410237ed18f', 'KPtzp2', '', '13555555555', '', 1, 0, NULL, '', 0.00, 0, 1, 1, 1669710393, 1669710728, '127.0.0.1', 0, '127.0.0.1', 1669691760, 1669691760, 1669710728, '', 'normal', '');
