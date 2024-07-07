<?php
/**
 * 菜单配置文件
 */

return [
	    [
	        "type" => "file",
	        "name" => "webscan",
	        "title" => "安全防护",
	        "icon" => "fa fa-shield",
	        "condition" => "",
	        "remark" => "",
	        "ismenu" => 1,
	        "sublist" => [
	            [
	                "type" => "file",
	                "name" => "webscan/webscanlog/dashboard",
	                "title" => "攻击概括",
	                "icon" => "fa fa-bar-chart-o",
	                "condition" => "",
	                "remark" => "",
	                "ismenu" => 1
	            ],
	            [
	                "type" => "file",
	                "name" => "webscan/verifies",
	                "title" => "文件校验",
	                "icon" => "fa fa-history",
	                "condition" => "",
	                "remark" => "",
	                "ismenu" => 1,
	                "sublist" => [
	                    [
	                        "type" => "file",
	                        "name" => "webscan/verifies/index",
	                        "title" => "校验首页",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "webscan/verifies/show",
	                        "title" => "查看文件",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "webscan/verifies/trust",
	                        "title" => "加入信任",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "webscan/verifies/trusts",
	                        "title" => "批量信任",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "webscan/verifies/build",
	                        "title" => "初始化数据",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "webscan/verifies/bianli",
	                        "title" => "遍历检查",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ]
	                ]
	            ],
	            [
	                "type" => "file",
	                "name" => "webscan/webscanlog",
	                "title" => "攻击日志",
	                "icon" => "fa fa-warning",
	                "condition" => "",
	                "remark" => "",
	                "ismenu" => 1,
	                "sublist" => [
	                    [
	                        "type" => "file",
	                        "name" => "webscan/webscanlog/index",
	                        "title" => "攻击日志",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "webscan/webscanlog/black",
	                        "title" => "加入黑名单",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ]
	                ]
	            ]
	        ]
	    ]
	];