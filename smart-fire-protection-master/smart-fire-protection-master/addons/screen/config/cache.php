<?php 
 return array (
  'table_name' => 'fa_screen_data,fa_screen_excel,fa_screen_page,fa_screen_share',
  'self_path' => '',
  'update_data' => '--
-- 1.0.1
-- 分享表添加 excel_id
ALTER TABLE `__PREFIX__screen_share`
ADD COLUMN `excel_id` int(255) NULL DEFAULT NULL COMMENT \'excel_ID\' AFTER `page_id`;',
);