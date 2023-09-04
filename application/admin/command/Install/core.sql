/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1
 Source Server Type    : MySQL
 Source Server Version : 80029
 Source Host           : localhost:3306
 Source Schema         : taohuadao

 Target Server Type    : MySQL
 Target Server Version : 80029
 File Encoding         : 65001

 Date: 02/09/2023 19:01:05
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for fa_admin
-- ----------------------------
DROP TABLE IF EXISTS `fa_admin`;
CREATE TABLE `fa_admin`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '昵称',
  `password` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '密码盐',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '头像',
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '电子邮箱',
  `mobile` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '手机号码',
  `loginfailure` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '失败次数',
  `loginsuccess` int NULL DEFAULT 0 COMMENT '登录次数',
  `logintime` bigint NULL DEFAULT NULL COMMENT '登录时间',
  `loginip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '登录IP',
  `createtime` bigint NULL DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint NULL DEFAULT NULL COMMENT '更新时间',
  `token` varchar(59) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT 'Session标识',
  `status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal' COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '管理员表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_admin
-- ----------------------------
INSERT INTO `fa_admin` VALUES (1, 'admin', 'Admin', '', '', '/assets/img/avatar.png', 'admin@admin.com', '', 0, 0, 1693239561, '127.0.0.1', 1491635035, 1693239561, 'f7a775b0-e30d-4fa8-917f-58998df7cf54', 'normal');

-- ----------------------------
-- Table structure for fa_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `fa_admin_log`;
CREATE TABLE `fa_admin_log`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `admin_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '管理员ID',
  `username` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '管理员名字',
  `url` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '操作页面',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '日志标题',
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内容',
  `ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT 'IP',
  `useragent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT 'User-Agent',
  `createtime` bigint NULL DEFAULT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `name`(`username`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '管理员日志表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_admin_log
-- ----------------------------

-- ----------------------------
-- Table structure for fa_area
-- ----------------------------
DROP TABLE IF EXISTS `fa_area`;
CREATE TABLE `fa_area`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `pid` int NULL DEFAULT NULL COMMENT '父id',
  `shortname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '简称',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '名称',
  `mergename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '全称',
  `level` tinyint NULL DEFAULT NULL COMMENT '层级:1=省,2=市,3=区/县',
  `pinyin` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '拼音',
  `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '长途区号',
  `zip` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '邮编',
  `first` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '首字母',
  `lng` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '经度',
  `lat` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '纬度',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `pid`(`pid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '地区表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_area
-- ----------------------------

-- ----------------------------
-- Table structure for fa_attachment
-- ----------------------------
DROP TABLE IF EXISTS `fa_attachment`;
CREATE TABLE `fa_attachment`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `category` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '类别',
  `admin_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '管理员ID',
  `user_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '会员ID',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '物理路径',
  `imagewidth` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '宽度',
  `imageheight` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '高度',
  `imagetype` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '图片类型',
  `imageframes` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '图片帧数',
  `filename` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '文件名称',
  `filesize` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '文件大小',
  `mimetype` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT 'mime类型',
  `extparam` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '透传数据',
  `createtime` bigint NULL DEFAULT NULL COMMENT '创建日期',
  `updatetime` bigint NULL DEFAULT NULL COMMENT '更新时间',
  `uploadtime` bigint NULL DEFAULT NULL COMMENT '上传时间',
  `storage` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'local' COMMENT '存储位置',
  `sha1` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '文件 sha1编码',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '附件表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_attachment
-- ----------------------------

-- ----------------------------
-- Table structure for fa_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `fa_auth_group`;
CREATE TABLE `fa_auth_group`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '父组别',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '组名',
  `rules` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '规则ID',
  `createtime` bigint NULL DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint NULL DEFAULT NULL COMMENT '更新时间',
  `status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '分组表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_auth_group
-- ----------------------------
INSERT INTO `fa_auth_group` VALUES (1, 0, 'Admin group', '*', 1491635035, 1491635035, 'normal');

-- ----------------------------
-- Table structure for fa_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `fa_auth_group_access`;
CREATE TABLE `fa_auth_group_access`  (
  `uid` int UNSIGNED NOT NULL COMMENT '会员ID',
  `group_id` int UNSIGNED NOT NULL COMMENT '级别ID',
  UNIQUE INDEX `uid_group_id`(`uid`, `group_id`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE,
  INDEX `group_id`(`group_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '权限分组表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_auth_group_access
-- ----------------------------
INSERT INTO `fa_auth_group_access` VALUES (1, 1);

-- ----------------------------
-- Table structure for fa_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `fa_auth_rule`;
CREATE TABLE `fa_auth_rule`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` enum('menu','file') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'file' COMMENT 'menu为菜单,file为权限节点',
  `pid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '父ID',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '规则名称',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '规则名称',
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '图标',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '规则URL',
  `condition` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '条件',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '备注',
  `ismenu` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否为菜单',
  `menutype` enum('addtabs','blank','dialog','ajax') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '菜单类型',
  `extend` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '扩展属性',
  `py` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '拼音首字母',
  `pinyin` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '拼音',
  `createtime` bigint NULL DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint NULL DEFAULT NULL COMMENT '更新时间',
  `weigh` int NOT NULL DEFAULT 0 COMMENT '权重',
  `status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE,
  INDEX `pid`(`pid`) USING BTREE,
  INDEX `weigh`(`weigh`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 102 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '节点表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_auth_rule
-- ----------------------------
INSERT INTO `fa_auth_rule` VALUES (1, 'file', 0, 'index/directions', '使用说明', 'fa fa-newspaper-o', '', '', '', 1, 'addtabs', '', 'sysm', 'shiyongshuoming', 1693243742, 1693243742, 900, 'normal');
INSERT INTO `fa_auth_rule` VALUES (2, 'file', 1, 'index/directions/index', '查看', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'zk', 'zhakan', 1693243760, 1693243760, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (3, 'file', 0, 'web/sites', '站点管理', 'fa fa-internet-explorer', '', '', '', 1, 'addtabs', '', 'zdgl', 'zhandianguanli', 1693243786, 1693243786, 890, 'normal');
INSERT INTO `fa_auth_rule` VALUES (4, 'file', 3, 'web/sites/index', '查看', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'zk', 'zhakan', 1693243802, 1693243802, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (5, 'file', 3, 'web/sites/edit', '编辑', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'bj', 'bianji', 1693243866, 1693243866, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (6, 'file', 3, 'web/sites/add', '添加', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'tj', 'tianjia', 1693243881, 1693243881, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (7, 'file', 3, 'web/sites/del', '删除', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'sc', 'shanchu', 1693243892, 1693243892, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (8, 'file', 3, 'web/sites/multi', '批量更新', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'plgx', 'pilianggengxin', 1693243907, 1693243907, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (9, 'file', 0, 'web/categorys', '分类管理', 'fa fa-align-left', '', '', '', 1, 'addtabs', '', 'flgl', 'fenleiguanli', 1693243943, 1693243943, 880, 'normal');
INSERT INTO `fa_auth_rule` VALUES (10, 'file', 9, 'web/categorys/index', '查看', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'zk', 'zhakan', 1693243957, 1693243965, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (11, 'file', 9, 'web/categorys/edit', '编辑', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'bj', 'bianji', 1693243977, 1693243977, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (12, 'file', 9, 'web/categorys/add', '添加', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'tj', 'tianjia', 1693243988, 1693243988, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (13, 'file', 9, 'web/categorys/del', '删除', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'sc', 'shanchu', 1693243999, 1693243999, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (14, 'file', 9, 'web/categorys/multi', '批量更新', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'plgx', 'pilianggengxin', 1693244023, 1693244023, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (15, 'file', 0, 'web/site_model', '模板管理', 'fa fa-window-maximize', '', '', '', 1, 'addtabs', '', 'mbgl', 'mubanguanli', 1693244075, 1693244075, 870, 'normal');
INSERT INTO `fa_auth_rule` VALUES (16, 'file', 15, 'web/site_model/index', '查看', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'zk', 'zhakan', 1693244091, 1693244091, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (17, 'file', 15, 'web/site_model/download', '下载', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'xz', 'xiazai', 1693244137, 1693244137, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (18, 'file', 15, 'web/site_model/detail', '详情', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'xq', 'xiangqing', 1693244158, 1693244158, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (19, 'file', 0, 'web/advertisement', '广告管理', 'fa fa-joomla', '', '', '', 1, 'addtabs', '', 'gggl', 'guanggaoguanli', 1693244194, 1693244194, 860, 'normal');
INSERT INTO `fa_auth_rule` VALUES (20, 'file', 19, 'web/advertisement/index', '查看', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'zk', 'zhakan', 1693244210, 1693244210, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (21, 'file', 19, 'web/advertisement/edit', '编辑', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'bj', 'bianji', 1693244237, 1693244237, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (22, 'file', 19, 'web/advertisement/multi', '批量更新', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'plgx', 'pilianggengxin', 1693244258, 1693244258, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (23, 'file', 0, 'web/statistics', '数据统计', 'fa fa-area-chart', '', '', '', 1, 'addtabs', '', 'sjtj', 'shujutongji', 1693244294, 1693244294, 850, 'normal');
INSERT INTO `fa_auth_rule` VALUES (24, 'file', 23, 'web/statistics/index', '查看', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'zk', 'zhakan', 1693244304, 1693244304, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (25, 'file', 0, 'web/links', '友链管理', 'fa fa-chain', '', '', '', 1, 'addtabs', '', 'ylgl', 'youlianguanli', 1693244324, 1693244324, 840, 'normal');
INSERT INTO `fa_auth_rule` VALUES (26, 'file', 25, 'web/links/index', '查看', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'zk', 'zhakan', 1693244336, 1693244336, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (27, 'file', 25, 'web/links/edit', '编辑', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'bj', 'bianji', 1693244350, 1693244350, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (28, 'file', 25, 'web/links/add', '添加', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'tj', 'tianjia', 1693244359, 1693244359, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (29, 'file', 25, 'web/links/del', '删除', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'sc', 'shanchu', 1693244369, 1693244369, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (30, 'file', 25, 'web/links/multi', '批量更新', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'plgx', 'pilianggengxin', 1693244392, 1693244392, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (31, 'file', 0, 'system', '系统管理', 'fa fa-cogs', '', '', '', 1, 'addtabs', '', 'xtgl', 'xitongguanli', 1693244428, 1693244428, 830, 'normal');
INSERT INTO `fa_auth_rule` VALUES (32, 'file', 31, 'general/profile', '个人资料', 'fa fa-user', '', '', '', 1, 'addtabs', '', 'grzl', 'gerenziliao', 1693244460, 1693244477, 820, 'normal');
INSERT INTO `fa_auth_rule` VALUES (33, 'file', 32, 'general/profile/index', '查看', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'zk', 'zhakan', 1693244492, 1693244492, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (34, 'file', 32, 'general/profile/update', '更新个人信息', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'gxgrxx', 'gengxingerenxinxi', 1693244507, 1693244507, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (35, 'file', 32, 'general/profile/add', '添加', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'tj', 'tianjia', 1693244516, 1693244516, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (36, 'file', 32, 'general/profile/edit', '编辑', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'bj', 'bianji', 1693244526, 1693244526, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (37, 'file', 32, 'general/profile/del', '删除', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'sc', 'shanchu', 1693244539, 1693244539, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (38, 'file', 32, 'general/profile/multi', '批量更新', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'plgx', 'pilianggengxin', 1693244549, 1693244549, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (39, 'file', 31, 'auth/group', '角色组', 'fa fa-group', '', '', '', 1, 'addtabs', '', 'jsz', 'juesezu', 1693244570, 1693244570, 810, 'normal');
INSERT INTO `fa_auth_rule` VALUES (40, 'file', 39, 'auth/group/index', '查看', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'zk', 'zhakan', 1693244583, 1693244583, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (41, 'file', 39, 'auth/group/add', '添加', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'tj', 'tianjia', 1693244591, 1693244591, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (42, 'file', 39, 'auth/group/edit', '编辑', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'bj', 'bianji', 1693244600, 1693244600, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (43, 'file', 39, 'auth/group/del', '删除', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'sc', 'shanchu', 1693244609, 1693244609, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (44, 'file', 31, 'auth/admin', '管理员管理', 'fa fa-user', '', '', '', 1, 'addtabs', '', 'glygl', 'guanliyuanguanli', 1693244679, 1693244679, 800, 'normal');
INSERT INTO `fa_auth_rule` VALUES (45, 'file', 44, 'auth/admin/index', '查看', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'zk', 'zhakan', 1693244707, 1693244707, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (46, 'file', 44, 'auth/admin/add', '添加', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'tj', 'tianjia', 1693244716, 1693244716, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (47, 'file', 44, 'auth/admin/edit', '编辑', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'bj', 'bianji', 1693244725, 1693244725, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (48, 'file', 44, 'auth/admin/del', '删除', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'sc', 'shanchu', 1693244734, 1693244734, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (49, 'file', 31, 'auth/adminlog', '管理员日志', 'fa fa-list-alt', '', '', '', 1, 'addtabs', '', 'glyrz', 'guanliyuanrizhi', 1693244760, 1693244760, 790, 'normal');
INSERT INTO `fa_auth_rule` VALUES (50, 'file', 49, 'auth/adminlog/index', '查看', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'zk', 'zhakan', 1693244773, 1693244773, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (51, 'file', 49, 'auth/adminlog/detail', '详情', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'xq', 'xiangqing', 1693244787, 1693244787, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (52, 'file', 49, 'auth/adminlog/del', '删除', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'sc', 'shanchu', 1693244799, 1693244799, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (53, 'file', 31, 'general/config', '系统配置', 'fa fa-cog', '', '', '', 1, 'addtabs', '', 'xtpz', 'xitongpeizhi', 1693244818, 1693244818, 780, 'normal');
INSERT INTO `fa_auth_rule` VALUES (54, 'file', 53, 'general/config/index', '查看', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'zk', 'zhakan', 1693244832, 1693244832, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (55, 'file', 53, 'general/config/add', '添加', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'tj', 'tianjia', 1693244841, 1693244841, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (56, 'file', 53, 'general/config/edit', '编辑', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'bj', 'bianji', 1693244851, 1693244851, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (57, 'file', 53, 'general/config/del', '删除', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'sc', 'shanchu', 1693244860, 1693244860, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (58, 'file', 53, 'general/config/multi', '批量更新', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'plgx', 'pilianggengxin', 1693244868, 1693244868, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (59, 'file', 0, 'user/user', '用户管理', 'fa fa-user', '', '', '', 1, 'addtabs', '', 'yhgl', 'yonghuguanli', 1693244896, 1693244896, 770, 'normal');
INSERT INTO `fa_auth_rule` VALUES (60, 'file', 59, 'user/user/index', '查看', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'zk', 'zhakan', 1693244909, 1693244909, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (61, 'file', 59, 'user/user/edit', '编辑', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'bj', 'bianji', 1693244918, 1693244918, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (62, 'file', 59, 'user/user/add', '添加', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'tj', 'tianjia', 1693244926, 1693244926, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (63, 'file', 59, 'user/user/del', '删除', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'sc', 'shanchu', 1693244941, 1693244941, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (64, 'file', 59, 'user/user/multi', '批量更新', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'plgx', 'pilianggengxin', 1693244947, 1693244947, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (65, 'file', 0, 'web/orders', '订单管理', 'fa fa-shopping-bag', '', '', '', 1, 'addtabs', '', 'ddgl', 'dingdanguanli', 1693244980, 1693244980, 760, 'normal');
INSERT INTO `fa_auth_rule` VALUES (66, 'file', 65, 'web/orders/index', '查看', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'zk', 'zhakan', 1693244991, 1693244991, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (67, 'file', 0, 'web/payment_method', '支付配置', 'fa fa-cc-paypal', '', '', '', 1, 'addtabs', '', 'zfpz', 'zhifupeizhi', 1693245014, 1693245014, 750, 'normal');
INSERT INTO `fa_auth_rule` VALUES (68, 'file', 67, 'web/payment_method/index', '查看', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'zk', 'zhakan', 1693245026, 1693245026, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (69, 'file', 67, 'web/payment_method/edit', '编辑', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'bj', 'bianji', 1693245041, 1693245041, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (70, 'file', 67, 'web/payment_method/add', '添加', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'tj', 'tianjia', 1693245051, 1693245051, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (71, 'file', 67, 'web/payment_method/del', '删除', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'sc', 'shanchu', 1693245060, 1693245060, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (72, 'file', 67, 'web/payment_method/multi', '批量更新', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'plgx', 'pilianggengxin', 1693245078, 1693245078, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (73, 'file', 0, 'web/vip_packages', 'VIP配置', 'fa fa-vimeo', '', '', '', 1, 'addtabs', '', 'Vpz', 'VIPpeizhi', 1693245111, 1693245111, 740, 'normal');
INSERT INTO `fa_auth_rule` VALUES (74, 'file', 73, 'web/vip_packages/index', '查看', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'zk', 'zhakan', 1693245121, 1693245126, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (75, 'file', 73, 'web/vip_packages/edit', '编辑', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'bj', 'bianji', 1693245140, 1693245140, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (76, 'file', 73, 'web/vip_packages/multi', '批量更新', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'plgx', 'pilianggengxin', 1693245156, 1693245156, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (77, 'file', 0, 'web/charge_integral', '积分配置', 'fa fa-trophy', '', '', '', 1, 'addtabs', '', 'jfpz', 'jifenpeizhi', 1693245182, 1693245182, 730, 'normal');
INSERT INTO `fa_auth_rule` VALUES (78, 'file', 77, 'web/charge_integral/index', '查看', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'zk', 'zhakan', 1693245196, 1693245196, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (79, 'file', 77, 'web/charge_integral/edit', '编辑', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'bj', 'bianji', 1693245206, 1693245206, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (80, 'file', 77, 'web/charge_integral/add', '查看', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'zk', 'zhakan', 1693245219, 1693245219, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (81, 'file', 77, 'web/charge_integral/del', '删除', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'sc', 'shanchu', 1693245229, 1693245229, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (82, 'file', 77, 'web/charge_integral/multi', '批量更新', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'plgx', 'pilianggengxin', 1693245254, 1693245254, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (83, 'file', 0, 'web/guid_site', '导航站点', 'fa fa-map-marker', '', '', '', 1, 'addtabs', '', 'dhzd', 'daohangzhandian', 1693245283, 1693245283, 720, 'hidden');
INSERT INTO `fa_auth_rule` VALUES (84, 'file', 83, 'web/guid_site/index', '查看', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'zk', 'zhakan', 1693245307, 1693245316, 0, 'hidden');
INSERT INTO `fa_auth_rule` VALUES (85, 'file', 83, 'web/guid_site/edit', '编辑', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'bj', 'bianji', 1693245331, 1693245331, 0, 'hidden');
INSERT INTO `fa_auth_rule` VALUES (86, 'file', 83, 'web/guid_site/add', '添加', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'tj', 'tianjia', 1693245342, 1693245342, 0, 'hidden');
INSERT INTO `fa_auth_rule` VALUES (87, 'file', 83, 'web/guid_site/del', '删除', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'sc', 'shanchu', 1693245354, 1693245354, 0, 'hidden');
INSERT INTO `fa_auth_rule` VALUES (88, 'file', 83, 'web/guid_site/multi', '批量更新', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'plgx', 'pilianggengxin', 1693245368, 1693245368, 0, 'hidden');
INSERT INTO `fa_auth_rule` VALUES (89, 'file', 0, 'web/activity_setting', '活动管理', 'fa fa-fort-awesome', '', '', '', 1, 'addtabs', '', 'hdgl', 'huodongguanli', 1693245409, 1693245409, 710, 'normal');
INSERT INTO `fa_auth_rule` VALUES (90, 'file', 89, 'web/activity_setting/index', '查看', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'zk', 'zhakan', 1693245429, 1693245429, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (91, 'file', 89, 'web/activity_setting/edit', '编辑', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'bj', 'bianji', 1693245440, 1693245440, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (92, 'file', 0, 'web/messages', '公告消息', 'fa fa-bell', '', '', '', 1, 'addtabs', '', 'ggxx', 'gonggaoxiaoxi', 1693245465, 1693245465, 700, 'normal');
INSERT INTO `fa_auth_rule` VALUES (93, 'file', 92, 'web/messages/index', '查看', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'zk', 'zhakan', 1693245481, 1693245481, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (94, 'file', 92, 'web/messages/edit', '编辑', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'bj', 'bianji', 1693245491, 1693245491, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (95, 'file', 92, 'web/messages/add', '添加', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'tj', 'tianjia', 1693245503, 1693245503, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (96, 'file', 92, 'web/messages/del', '删除', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'sc', 'shanchu', 1693245520, 1693245520, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (97, 'file', 92, 'web/messages/multi', '批量更新', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'plgx', 'pilianggengxin', 1693245534, 1693245534, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (98, 'file', 0, 'sync/videos', '更新片源', 'fa fa-refresh', '', '', '', 1, 'addtabs', '', 'gxpy', 'gengxinpianyuan', 1693245565, 1693245565, 690, 'normal');
INSERT INTO `fa_auth_rule` VALUES (99, 'file', 98, 'sync/videos/index', '查看', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'zk', 'zhakan', 1693245579, 1693245579, 0, 'normal');
INSERT INTO `fa_auth_rule` VALUES (100, 'file', 98, 'sync/videos/selectsync', '选择同步', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'xztb', 'xuanzetongbu', 1693245630, 1693245661, 0, 'hidden');
INSERT INTO `fa_auth_rule` VALUES (101, 'file', 98, 'sync/videos/allsync', '全量同步', 'fa fa-circle-o', '', '', '', 0, 'addtabs', '', 'qltb', 'quanliangtongbu', 1693245650, 1693245650, 0, 'normal');

-- ----------------------------
-- Table structure for fa_category
-- ----------------------------
DROP TABLE IF EXISTS `fa_category`;
CREATE TABLE `fa_category`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '父ID',
  `type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '栏目类型',
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '',
  `nickname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '',
  `flag` set('hot','index','recommend') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '',
  `image` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '图片',
  `keywords` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '关键字',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '描述',
  `diyname` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '自定义名称',
  `createtime` bigint NULL DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint NULL DEFAULT NULL COMMENT '更新时间',
  `weigh` int NOT NULL DEFAULT 0 COMMENT '权重',
  `status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `weigh`(`weigh`, `id`) USING BTREE,
  INDEX `pid`(`pid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '分类表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_category
-- ----------------------------
INSERT INTO `fa_category` VALUES (1, 0, 'page', '官方新闻', 'news', 'recommend', '/assets/img/qrcode.png', '', '', 'news', 1491635035, 1491635035, 1, 'normal');
INSERT INTO `fa_category` VALUES (2, 0, 'page', '移动应用', 'mobileapp', 'hot', '/assets/img/qrcode.png', '', '', 'mobileapp', 1491635035, 1491635035, 2, 'normal');
INSERT INTO `fa_category` VALUES (3, 2, 'page', '微信公众号', 'wechatpublic', 'index', '/assets/img/qrcode.png', '', '', 'wechatpublic', 1491635035, 1491635035, 3, 'normal');
INSERT INTO `fa_category` VALUES (4, 2, 'page', 'Android开发', 'android', 'recommend', '/assets/img/qrcode.png', '', '', 'android', 1491635035, 1491635035, 4, 'normal');
INSERT INTO `fa_category` VALUES (5, 0, 'page', '软件产品', 'software', 'recommend', '/assets/img/qrcode.png', '', '', 'software', 1491635035, 1491635035, 5, 'normal');
INSERT INTO `fa_category` VALUES (6, 5, 'page', '网站建站', 'website', 'recommend', '/assets/img/qrcode.png', '', '', 'website', 1491635035, 1491635035, 6, 'normal');
INSERT INTO `fa_category` VALUES (7, 5, 'page', '企业管理软件', 'company', 'index', '/assets/img/qrcode.png', '', '', 'company', 1491635035, 1491635035, 7, 'normal');
INSERT INTO `fa_category` VALUES (8, 6, 'page', 'PC端', 'website-pc', 'recommend', '/assets/img/qrcode.png', '', '', 'website-pc', 1491635035, 1491635035, 8, 'normal');
INSERT INTO `fa_category` VALUES (9, 6, 'page', '移动端', 'website-mobile', 'recommend', '/assets/img/qrcode.png', '', '', 'website-mobile', 1491635035, 1491635035, 9, 'normal');
INSERT INTO `fa_category` VALUES (10, 7, 'page', 'CRM系统 ', 'company-crm', 'recommend', '/assets/img/qrcode.png', '', '', 'company-crm', 1491635035, 1491635035, 10, 'normal');
INSERT INTO `fa_category` VALUES (11, 7, 'page', 'SASS平台软件', 'company-sass', 'recommend', '/assets/img/qrcode.png', '', '', 'company-sass', 1491635035, 1491635035, 11, 'normal');
INSERT INTO `fa_category` VALUES (12, 0, 'test', '测试1', 'test1', 'recommend', '/assets/img/qrcode.png', '', '', 'test1', 1491635035, 1491635035, 12, 'normal');
INSERT INTO `fa_category` VALUES (13, 0, 'test', '测试2', 'test2', 'recommend', '/assets/img/qrcode.png', '', '', 'test2', 1491635035, 1491635035, 13, 'normal');

-- ----------------------------
-- Table structure for fa_config
-- ----------------------------
DROP TABLE IF EXISTS `fa_config`;
CREATE TABLE `fa_config`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '变量名',
  `group` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '分组',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '变量标题',
  `tip` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '变量描述',
  `type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '类型:string,text,int,bool,array,datetime,date,file',
  `visible` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '可见条件',
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '变量值',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '变量字典数据',
  `rule` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '验证规则',
  `extend` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '扩展属性',
  `setting` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '配置',
  `index` smallint NULL DEFAULT NULL COMMENT '排序',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 32 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '系统配置' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_config
-- ----------------------------
INSERT INTO `fa_config` VALUES (1, 'name', 'basic', 'Site name', '请填写站点名称', 'string', '', '桃花岛', '', 'required', '', NULL, 1);
INSERT INTO `fa_config` VALUES (2, 'beian', 'basic', 'Beian', '粤ICP备15000000号-1', 'string', '1', '', '', '', '', NULL, NULL);
INSERT INTO `fa_config` VALUES (3, 'cdnurl', 'basic', 'Cdn url', '如果全站静态资源使用第三方云储存请配置该值', 'string', '1', '', '', '', '', '', NULL);
INSERT INTO `fa_config` VALUES (4, 'version', 'basic', 'Version', '如果静态资源有变动请重新配置该值', 'string', '1', '1.0.125', '', 'required', '', NULL, NULL);
INSERT INTO `fa_config` VALUES (5, 'timezone', 'basic', 'Timezone', '', 'string', '1', 'Asia/Shanghai', '', 'required', '', NULL, NULL);
INSERT INTO `fa_config` VALUES (6, 'forbiddenip', 'basic', 'Forbidden ip', '一行一条记录', 'text', '1', '', '', '', '', NULL, NULL);
INSERT INTO `fa_config` VALUES (7, 'languages', 'basic', 'Languages', '', 'array', '1', '{\"backend\":\"zh-cn\",\"frontend\":\"zh-cn\"}', '', 'required', '', NULL, NULL);
INSERT INTO `fa_config` VALUES (8, 'fixedpage', 'basic', 'Fixed page', '请尽量输入左侧菜单栏存在的链接', 'string', '1', 'index/directions', '', 'required', '', NULL, NULL);
INSERT INTO `fa_config` VALUES (9, 'categorytype', 'dictionary', 'Category type', '', 'array', '', '{\"default\":\"Default\",\"page\":\"Page\",\"article\":\"Article\",\"test\":\"Test\"}', '', '', '', '', NULL);
INSERT INTO `fa_config` VALUES (10, 'configgroup', 'dictionary', 'Config group', '', 'array', '', '{\"basic\":\"Basic\"}', '', '', '', '', NULL);
INSERT INTO `fa_config` VALUES (11, 'mail_type', 'basic', 'Mail type', '选择邮件发送方式', 'select', '', '1', '[\"请选择\",\"SMTP\"]', '', '', NULL, 3);
INSERT INTO `fa_config` VALUES (12, 'mail_smtp_host', 'basic', 'Mail smtp host', '错误的配置发送邮件会导致服务器超时', 'string', '', '', '', '', '', NULL, 4);
INSERT INTO `fa_config` VALUES (13, 'mail_smtp_port', 'basic', 'Mail smtp port', '(不加密默认25,SSL默认465,TLS默认587)', 'string', '', '', '', '', '', NULL, 5);
INSERT INTO `fa_config` VALUES (14, 'mail_smtp_user', 'basic', 'Mail smtp user', '（填写完整用户名）', 'string', '', '', '', '', '', NULL, 6);
INSERT INTO `fa_config` VALUES (15, 'mail_smtp_pass', 'basic', 'Mail smtp password', '（填写您的密码或授权码）', 'string', '', '', '', '', '', NULL, 7);
INSERT INTO `fa_config` VALUES (16, 'mail_verify_type', 'basic', 'Mail vertify type', '（SMTP验证方式[推荐SSL]）', 'select', '', '1', '[\"无\",\"TLS\",\"SSL\"]', '', '', NULL, 8);
INSERT INTO `fa_config` VALUES (17, 'mail_from', 'basic', 'Mail from', '', 'string', '', '', '', '', '', NULL, 9);
INSERT INTO `fa_config` VALUES (18, 'attachmentcategory', 'dictionary', 'Attachment category', '', 'array', '', '{\"category1\":\"Category1\",\"category2\":\"Category2\",\"custom\":\"Custom\"}', '', '', '', '', NULL);
INSERT INTO `fa_config` VALUES (19, 'mail_switch', 'basic', '开启邮箱验证', '邮箱验证是否开启', 'switch', '', '0', NULL, '', '', NULL, 2);
INSERT INTO `fa_config` VALUES (20, 'prevent_gmail_alias_switch', 'basic', '开启防止Gmail多别名', '是否开启防止Gmail多别名', 'switch', '1', '0', NULL, '', '', NULL, 10);
INSERT INTO `fa_config` VALUES (21, 'mail_suffix_whitelist_switch', 'basic', '开启邮箱后缀白名单', '是否开启邮箱后缀白名单', 'switch', '1', '0', NULL, '', '', NULL, 11);
INSERT INTO `fa_config` VALUES (22, 'mail_suffix_whitelist', 'basic', '邮箱后缀白名单', '一行一条记录', 'text', '1', '', NULL, '', '', NULL, 12);
INSERT INTO `fa_config` VALUES (23, 'anti_machine_switch', 'basic', '开启防机器人', '是否开启验证码', 'switch', '', '0', NULL, '', '', NULL, 13);
INSERT INTO `fa_config` VALUES (24, 'google_recaptcha_key', 'basic', '密钥', 'Google reCaptcha V3 申请的密钥', 'string', '', '', NULL, '', '', NULL, 14);
INSERT INTO `fa_config` VALUES (25, 'google_recaptcha_site_key', 'basic', '网站密钥', 'Google reCaptcha V3 申请的网站密钥', 'string', '', '', NULL, '', '', NULL, 15);
INSERT INTO `fa_config` VALUES (26, 'google_recaptcha_score', 'basic', '验证最低得分', 'Google reCaptcha V3 最低通过的得分(0.1~1)', 'number', '', '0.5', NULL, '', '', NULL, 16);
INSERT INTO `fa_config` VALUES (27, 'ip_registered_restriction', 'basic', '开启IP注册限制', '是否开启IP注册限制', 'switch', '', '0', NULL, '', '', NULL, 17);
INSERT INTO `fa_config` VALUES (28, 'ip_max_registered_times', 'basic', '连续几次限制', '同一IP注册几次后开启惩罚', 'number', '', '10', NULL, '', '', NULL, 18);
INSERT INTO `fa_config` VALUES (29, 'ip_punishment_time', 'basic', '惩罚时间', '惩罚时间，单位分钟', 'number', '', '2', NULL, '', '', NULL, 19);
INSERT INTO `fa_config` VALUES (30, 'system_notice', 'hidden', '系统公告信息', '', '', '', '<p style=\"margin: 0px; text-rendering: optimizelegibility; font-feature-settings: \'kern\'; font-kerning: normal; font-family: PingFangSC-Regular, \'PingFang SC\', sans-serif; font-size: 16px;\"><strong>欢迎使用桃花岛CMS建站系统，我们强烈建议您加入我们的群组，以便获取最佳技术支持服务！</strong></p>\n<p style=\"margin: 0px; text-rendering: optimizelegibility; font-feature-settings: \'kern\'; font-kerning: normal; font-family: PingFangSC-Regular, \'PingFang SC\', sans-serif; font-size: 16px;\"><strong>Telegram：https://t.me/taohuadaoCMS</strong></p>\n<p style=\"margin: 0px; text-rendering: optimizelegibility; font-feature-settings: \'kern\'; font-kerning: normal; font-family: PingFangSC-Regular, \'PingFang SC\', sans-serif; font-size: 16px;\"><strong>官方网站：https://www.taohuadao.org</strong></p>\n<p style=\"margin: 0px; text-rendering: optimizelegibility; font-feature-settings: \'kern\'; font-kerning: normal; font-family: PingFangSC-Regular, \'PingFang SC\', sans-serif; font-size: 16px;\"><strong>官方论坛：https://bbs.taohuadao.net</strong><br /><strong>付费业务：https://www.taohuadao.net</strong></p>\n<p style=\"margin: 0px; text-rendering: optimizelegibility; font-feature-settings: \'kern\'; font-kerning: normal; font-family: PingFangSC-Regular, \'PingFang SC\', sans-serif; font-size: 16px;\"><strong>官方github：https://github.com/TaohuadaoTech/CMS</strong></p>\n<p style=\"margin: 0px; text-rendering: optimizelegibility; font-feature-settings: \'kern\'; font-kerning: normal; font-family: PingFangSC-Regular, \'PingFang SC\', sans-serif; font-size: 16px;\">&nbsp;</p>\n<p style=\"margin: 0px; text-rendering: optimizelegibility; font-feature-settings: \'kern\'; font-kerning: normal; font-family: PingFangSC-Regular, \'PingFang SC\', sans-serif; font-size: 16px;\"><span style=\"color: #1f2328; font-family: -apple-system, \'system-ui\', \'Segoe UI\', \'Noto Sans\', Helvetica, Arial, sans-serif, \'Apple Color Emoji\', \'Segoe UI Emoji\'; background-color: #ffffff;\">我们知道目前市面上有各种CMS系统，如苹果CMS、飞飞CMS以及传统的帝国CMS等等。他们都可以用来搭建影视网站，但是他们也都存在着各种问题，如系统臃肿、功能繁多，对于新手站长很不友好，代码分支很多、山寨埋有后门的版本在网络上流传，站长难以辨认。配置繁琐，如采集视频经常会遇到域名失效，需要操作数据库等，难以保证网站随时都能正常访问，一旦出现问题就会造成用户无法访问，用户流失等。所以我们才针对这些站长的痛点，推出了专属于影视建站系统领域的桃花岛CMS系统。</span></p>', NULL, '', '', '', NULL);
INSERT INTO `fa_config` VALUES (31, 'skip_ads_after_seconds', 'basic', '几秒后跳过广告', '几秒后出现跳过广告按钮，0表示不可跳过', 'number', '', '5', NULL, '', '', NULL, 20);

-- ----------------------------
-- Table structure for fa_ems
-- ----------------------------
DROP TABLE IF EXISTS `fa_ems`;
CREATE TABLE `fa_ems`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `event` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '事件',
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '邮箱',
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '验证码',
  `times` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '验证次数',
  `ip` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT 'IP',
  `createtime` bigint NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '邮箱验证码表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_ems
-- ----------------------------

-- ----------------------------
-- Table structure for fa_sms
-- ----------------------------
DROP TABLE IF EXISTS `fa_sms`;
CREATE TABLE `fa_sms`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `event` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '事件',
  `mobile` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '手机号',
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '验证码',
  `times` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '验证次数',
  `ip` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT 'IP',
  `createtime` bigint UNSIGNED NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '短信验证码表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_sms
-- ----------------------------

-- ----------------------------
-- Table structure for fa_sync_actresses
-- ----------------------------
DROP TABLE IF EXISTS `fa_sync_actresses`;
CREATE TABLE `fa_sync_actresses`  (
  `id` int NOT NULL COMMENT 'ID',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '头像',
  `cover` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '照片',
  `chinese_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '中文名称',
  `japanese_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '日文名称',
  `english_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '英文名称',
  `videos_bitmap` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '视频位图',
  `description` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '简介',
  `state` tinyint NOT NULL DEFAULT 1 COMMENT '状态',
  `created_at` timestamp NOT NULL COMMENT '新增时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `IX_name`(`chinese_name`, `japanese_name`, `english_name`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '演员表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_sync_actresses
-- ----------------------------


-- ----------------------------
-- Table structure for fa_sync_categories
-- ----------------------------
DROP TABLE IF EXISTS `fa_sync_categories`;
CREATE TABLE `fa_sync_categories`  (
  `id` int NOT NULL COMMENT 'ID',
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
  `pinyin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '拼音',
  `cover` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '封面',
  `videos_bitmap` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '视频位图',
  `state` tinyint NOT NULL DEFAULT 1 COMMENT '状态',
  `sort` int NOT NULL DEFAULT 0 COMMENT '排序',
  `created_at` timestamp NOT NULL COMMENT '新增时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '分类表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_sync_categories
-- ----------------------------
INSERT INTO `fa_sync_categories` VALUES (6, '中文字幕', '', '', '0', 1, 0, '2023-06-22 16:45:23', '2023-07-19 13:05:05');
INSERT INTO `fa_sync_categories` VALUES (7, '国产精品', '', '', '0', 1, 0, '2023-06-22 16:45:44', '2023-07-19 13:05:20');
INSERT INTO `fa_sync_categories` VALUES (8, '网红头条', '', '', '0', 1, 0, '2023-06-22 16:46:01', '2023-07-19 13:05:50');
INSERT INTO `fa_sync_categories` VALUES (9, '传媒影视', '', '', '0', 1, 0, '2023-06-22 16:46:09', '2023-07-19 13:06:02');
INSERT INTO `fa_sync_categories` VALUES (10, 'AI换脸', '', '', '0', 1, 0, '2023-06-22 16:46:52', '2023-07-19 13:06:13');
INSERT INTO `fa_sync_categories` VALUES (11, '三级综艺', '', '', '0', 1, 0, '2023-06-22 16:46:59', '2023-07-19 13:06:21');
INSERT INTO `fa_sync_categories` VALUES (12, '人妖男同', '', '', '0', 1, 0, '2023-07-19 13:06:28', '2023-07-19 13:06:28');
INSERT INTO `fa_sync_categories` VALUES (13, '制服诱惑', '', '', '0', 1, 0, '2023-07-19 13:06:35', '2023-07-19 13:06:35');
INSERT INTO `fa_sync_categories` VALUES (14, '精彩乱伦', '', '', '0', 1, 0, '2023-07-19 13:06:46', '2023-07-19 13:06:46');
INSERT INTO `fa_sync_categories` VALUES (15, '欧美影片', '', '', '0', 1, 0, '2023-07-19 13:06:53', '2023-07-19 13:06:53');
INSERT INTO `fa_sync_categories` VALUES (16, '精彩动漫', '', '', '0', 1, 0, '2023-07-19 13:06:58', '2023-07-19 13:06:58');
INSERT INTO `fa_sync_categories` VALUES (17, '主奴调教', '', '', '0', 1, 0, '2023-07-19 13:07:05', '2023-07-19 13:07:05');
INSERT INTO `fa_sync_categories` VALUES (18, '角色剧情', '', '', '0', 1, 0, '2023-07-19 13:07:13', '2023-07-19 13:07:13');
INSERT INTO `fa_sync_categories` VALUES (19, '直接开啪', '', '', '0', 1, 0, '2023-07-19 13:07:22', '2023-07-19 13:07:22');
INSERT INTO `fa_sync_categories` VALUES (20, '男友视角', '', '', '0', 1, 0, '2023-07-19 13:07:28', '2023-07-19 13:07:28');
INSERT INTO `fa_sync_categories` VALUES (21, '女同欢愉', '', '', '0', 1, 0, '2023-07-19 13:07:43', '2023-07-19 13:07:43');
INSERT INTO `fa_sync_categories` VALUES (22, '凌辱强暴', '', '', '0', 1, 0, '2023-07-19 13:08:06', '2023-07-19 13:08:06');
INSERT INTO `fa_sync_categories` VALUES (23, '无码影片', '', '', '0', 1, 0, '2023-07-19 13:08:14', '2023-07-19 13:08:14');
INSERT INTO `fa_sync_categories` VALUES (24, '丝袜美腿', '', '', '0', 1, 0, '2023-07-19 13:08:21', '2023-07-19 13:08:21');
INSERT INTO `fa_sync_categories` VALUES (25, '多P群交', '', '', '0', 1, 0, '2023-07-19 13:08:26', '2023-07-19 13:08:26');
INSERT INTO `fa_sync_categories` VALUES (26, '盗摄偷拍', '', '', '0', 1, 0, '2023-07-19 13:08:34', '2023-07-19 13:08:34');

-- ----------------------------
-- Table structure for fa_sync_origins
-- ----------------------------
DROP TABLE IF EXISTS `fa_sync_origins`;
CREATE TABLE `fa_sync_origins`  (
  `id` int NOT NULL COMMENT 'ID',
  `video_url` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '视频CDN域名',
  `img_url` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '图片CDN域名',
  `state` tinyint NOT NULL DEFAULT 1 COMMENT '状态',
  `created_at` timestamp NOT NULL COMMENT '新增时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '视频源设置表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_sync_origins
-- ----------------------------
INSERT INTO `fa_sync_origins` VALUES (1, 'https://xcyede.live', 'https://xcyede.live', 1, '2023-05-25 07:52:08', '2023-08-29 22:59:56');
INSERT INTO `fa_sync_origins` VALUES (2, 'https://flknqh.live', 'https://flknqh.live', 1, '2023-06-23 00:47:20', '2023-08-29 23:00:07');
INSERT INTO `fa_sync_origins` VALUES (3, 'https://uhzusk.live', 'https://uhzusk.live', 1, '2023-06-23 00:47:36', '2023-08-29 23:01:06');
INSERT INTO `fa_sync_origins` VALUES (4, 'https://ikfbsa.live', 'https://ikfbsa.live', 1, '2023-06-23 00:47:42', '2023-08-29 23:01:15');
INSERT INTO `fa_sync_origins` VALUES (5, 'https://lqhkyd.live', 'https://lqhkyd.live', 1, '2023-06-23 00:47:51', '2023-08-29 23:01:24');
INSERT INTO `fa_sync_origins` VALUES (6, 'https://ghatzj.live', 'https://ghatzj.live', 1, '2023-06-23 00:47:58', '2023-08-29 23:01:48');

-- ----------------------------
-- Table structure for fa_sync_tags
-- ----------------------------
DROP TABLE IF EXISTS `fa_sync_tags`;
CREATE TABLE `fa_sync_tags`  (
  `id` int NOT NULL COMMENT 'ID',
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标签名称',
  `created_at` timestamp NOT NULL COMMENT '添加时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `IX_name`(`name`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_sync_tags
-- ----------------------------


-- ----------------------------
-- Table structure for fa_sync_templates
-- ----------------------------
DROP TABLE IF EXISTS `fa_sync_templates`;
CREATE TABLE `fa_sync_templates`  (
  `id` int NOT NULL COMMENT 'ID',
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模板名称',
  `cover` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '封面',
  `version` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '版本号',
  `file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模板文件',
  `size` double NOT NULL COMMENT '模板大小',
  `theme` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '配色',
  `description` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '模板简介',
  `created_at` timestamp NOT NULL COMMENT '新增时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '模板表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_sync_templates
-- ----------------------------
INSERT INTO `fa_sync_templates` VALUES (15, '模板2', 'https://admin.emoppb.buzz/app/admin/upload/img/20230702/首页-0606.jpg', '1.0', 'https://admin.emoppb.buzz/app/admin/upload/files/20230828/taohuadao2.zip', 3864152, '', '', '2023-07-01 21:39:45', '2023-08-28 18:10:11');
INSERT INTO `fa_sync_templates` VALUES (16, '模版3', 'https://admin.emoppb.buzz/app/admin/upload/img/20230702/taohuadao3.png', '1.0', 'https://admin.emoppb.buzz/app/admin/upload/files/20230828/taohuadao3.zip', 2421994, '', '', '2023-07-02 14:35:33', '2023-08-28 18:10:30');
INSERT INTO `fa_sync_templates` VALUES (17, '模板1', 'https://admin.emoppb.buzz/app/admin/upload/img/20230702/photo_2023-07-02_19-04-41.jpg', '1.0', 'https://admin.emoppb.buzz/app/admin/upload/files/20230828/taohuadao1.zip', 2576062, '', '', '2023-07-02 19:05:52', '2023-08-28 18:10:47');
INSERT INTO `fa_sync_templates` VALUES (18, '模板4', 'https://admin.emoppb.buzz/app/admin/upload/img/20230702/photo_2023-07-02_19-06-32.jpg', '1.0', 'https://admin.emoppb.buzz/app/admin/upload/files/20230828/taohuadao5.zip', 5560279, 'pink', '', '2023-07-02 19:06:50', '2023-08-28 18:11:10');
INSERT INTO `fa_sync_templates` VALUES (19, '模版5', 'https://admin.emoppb.buzz/app/admin/upload/img/20230709/模版5.png', '1.0', 'https://admin.emoppb.buzz/app/admin/upload/files/20230828/taohuadao5.zip', 5560279, 'yellow', '', '2023-07-09 19:14:14', '2023-08-28 18:11:32');

-- ----------------------------
-- Table structure for fa_sync_videos
-- ----------------------------
DROP TABLE IF EXISTS `fa_sync_videos`;
CREATE TABLE `fa_sync_videos`  (
  `id` int NOT NULL COMMENT 'ID',
  `vid` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '视频ID',
  `sn` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '番号',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
  `pinyin` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '拼音',
  `category_id` int UNSIGNED NOT NULL COMMENT '所属分类',
  `origin_id` int NOT NULL COMMENT '所属视频源',
  `description` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '视频简介',
  `cover` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '封面图',
  `m3u8_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '播放地址',
  `share_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '分享地址',
  `time` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '视频时长',
  `size` double NOT NULL COMMENT '文件大小',
  `resolution` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '分辨率',
  `bit_rate` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '视频码率',
  `tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标签列表',
  `tags_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标签列表ID',
  `actresses` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '演员列表',
  `actresses_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '演员列表ID',
  `views` int NOT NULL DEFAULT 0 COMMENT '播放量',
  `favorites` int NOT NULL DEFAULT 0 COMMENT '收藏量',
  `like` int NOT NULL DEFAULT 0 COMMENT '顶一下',
  `dislike` int NOT NULL DEFAULT 0 COMMENT '踩一下',
  `state` tinyint NOT NULL DEFAULT 1 COMMENT '状态',
  `release_date` date NULL DEFAULT NULL COMMENT '发行时间',
  `created_at` timestamp NOT NULL COMMENT '新增时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `vid_unique`(`vid`) USING BTREE,
  INDEX `IX_categoryid`(`category_id`) USING BTREE,
  INDEX `IX_createdat`(`created_at`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '视频表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_sync_videos
-- ----------------------------


-- ----------------------------
-- Table structure for fa_test
-- ----------------------------
DROP TABLE IF EXISTS `fa_test`;
CREATE TABLE `fa_test`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int NULL DEFAULT 0 COMMENT '会员ID',
  `admin_id` int NULL DEFAULT 0 COMMENT '管理员ID',
  `category_id` int UNSIGNED NULL DEFAULT 0 COMMENT '分类ID(单选)',
  `category_ids` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '分类ID(多选)',
  `tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '标签',
  `week` enum('monday','tuesday','wednesday') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '星期(单选):monday=星期一,tuesday=星期二,wednesday=星期三',
  `flag` set('hot','index','recommend') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '标志(多选):hot=热门,index=首页,recommend=推荐',
  `genderdata` enum('male','female') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'male' COMMENT '性别(单选):male=男,female=女',
  `hobbydata` set('music','reading','swimming') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '爱好(多选):music=音乐,reading=读书,swimming=游泳',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '标题',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '内容',
  `image` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '图片',
  `images` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '图片组',
  `attachfile` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '附件',
  `keywords` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '关键字',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '描述',
  `city` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '省市',
  `json` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '配置:key=名称,value=值',
  `multiplejson` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '二维数组:title=标题,intro=介绍,author=作者,age=年龄',
  `price` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '价格',
  `views` int UNSIGNED NULL DEFAULT 0 COMMENT '点击',
  `workrange` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '时间区间',
  `startdate` date NULL DEFAULT NULL COMMENT '开始日期',
  `activitytime` datetime NULL DEFAULT NULL COMMENT '活动时间(datetime)',
  `year` year NULL DEFAULT NULL COMMENT '年',
  `times` time NULL DEFAULT NULL COMMENT '时间',
  `refreshtime` bigint NULL DEFAULT NULL COMMENT '刷新时间',
  `createtime` bigint NULL DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint NULL DEFAULT NULL COMMENT '更新时间',
  `deletetime` bigint NULL DEFAULT NULL COMMENT '删除时间',
  `weigh` int NULL DEFAULT 0 COMMENT '权重',
  `switch` tinyint(1) NULL DEFAULT 0 COMMENT '开关',
  `status` enum('normal','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'normal' COMMENT '状态',
  `state` enum('0','1','2') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '1' COMMENT '状态值:0=禁用,1=正常,2=推荐',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '测试表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_test
-- ----------------------------

-- ----------------------------
-- Table structure for fa_user
-- ----------------------------
DROP TABLE IF EXISTS `fa_user`;
CREATE TABLE `fa_user`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `group_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '组别ID',
  `username` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '昵称',
  `password` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '密码盐',
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '电子邮箱',
  `mobile` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '手机号',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '头像',
  `level` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '等级',
  `gender` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '性别',
  `birthday` date NULL DEFAULT NULL COMMENT '生日',
  `bio` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '格言',
  `money` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '余额',
  `score` int NOT NULL DEFAULT 0 COMMENT '积分',
  `my_invite_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '我的邀请码',
  `from_invite_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '注册使用的邀请码',
  `vip_expiration_time` bigint NULL DEFAULT NULL COMMENT 'VIP到期时间',
  `successive_sign_days` int NULL DEFAULT 0 COMMENT '连续签到天数',
  `max_successive_sign_days` int NULL DEFAULT 0 COMMENT '最大连续签到天数',
  `successions` int UNSIGNED NOT NULL DEFAULT 1 COMMENT '连续登录天数',
  `maxsuccessions` int UNSIGNED NOT NULL DEFAULT 1 COMMENT '最大连续登录天数',
  `prevtime` bigint NULL DEFAULT NULL COMMENT '上次登录时间',
  `logintime` bigint NULL DEFAULT NULL COMMENT '登录时间',
  `loginip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '登录IP',
  `loginfailure` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '失败次数',
  `joinip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '加入IP',
  `jointime` bigint NULL DEFAULT NULL COMMENT '加入时间',
  `createtime` bigint NULL DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint NULL DEFAULT NULL COMMENT '更新时间',
  `token` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT 'Token',
  `status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '状态',
  `verification` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '验证',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `username`(`username`) USING BTREE,
  INDEX `email`(`email`) USING BTREE,
  INDEX `mobile`(`mobile`) USING BTREE,
  INDEX `IX_myinvitecode`(`my_invite_code`) USING BTREE,
  INDEX `IX_frominvitecode`(`from_invite_code`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '会员表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_user
-- ----------------------------

-- ----------------------------
-- Table structure for fa_user_group
-- ----------------------------
DROP TABLE IF EXISTS `fa_user_group`;
CREATE TABLE `fa_user_group`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '组名',
  `rules` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '权限节点',
  `createtime` bigint NULL DEFAULT NULL COMMENT '添加时间',
  `updatetime` bigint NULL DEFAULT NULL COMMENT '更新时间',
  `status` enum('normal','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '会员组表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_user_group
-- ----------------------------

-- ----------------------------
-- Table structure for fa_user_money_log
-- ----------------------------
DROP TABLE IF EXISTS `fa_user_money_log`;
CREATE TABLE `fa_user_money_log`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '会员ID',
  `money` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '变更余额',
  `before` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '变更前余额',
  `after` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '变更后余额',
  `memo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '备注',
  `createtime` bigint NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '会员余额变动表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_user_money_log
-- ----------------------------

-- ----------------------------
-- Table structure for fa_user_rule
-- ----------------------------
DROP TABLE IF EXISTS `fa_user_rule`;
CREATE TABLE `fa_user_rule`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int NULL DEFAULT NULL COMMENT '父ID',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '名称',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '标题',
  `remark` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '备注',
  `ismenu` tinyint(1) NULL DEFAULT NULL COMMENT '是否菜单',
  `createtime` bigint NULL DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint NULL DEFAULT NULL COMMENT '更新时间',
  `weigh` int NULL DEFAULT 0 COMMENT '权重',
  `status` enum('normal','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '会员规则表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_user_rule
-- ----------------------------

-- ----------------------------
-- Table structure for fa_user_score_log
-- ----------------------------
DROP TABLE IF EXISTS `fa_user_score_log`;
CREATE TABLE `fa_user_score_log`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '会员ID',
  `score` int NOT NULL DEFAULT 0 COMMENT '变更积分',
  `before` int NOT NULL DEFAULT 0 COMMENT '变更前积分',
  `after` int NOT NULL DEFAULT 0 COMMENT '变更后积分',
  `memo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '备注',
  `createtime` bigint NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '会员积分变动表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_user_score_log
-- ----------------------------

-- ----------------------------
-- Table structure for fa_user_token
-- ----------------------------
DROP TABLE IF EXISTS `fa_user_token`;
CREATE TABLE `fa_user_token`  (
  `token` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Token',
  `user_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '会员ID',
  `createtime` bigint NULL DEFAULT NULL COMMENT '创建时间',
  `expiretime` bigint NULL DEFAULT NULL COMMENT '过期时间',
  PRIMARY KEY (`token`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '会员Token表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_user_token
-- ----------------------------

-- ----------------------------
-- Table structure for fa_user_vip_log
-- ----------------------------
DROP TABLE IF EXISTS `fa_user_vip_log`;
CREATE TABLE `fa_user_vip_log`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL COMMENT '会员ID',
  `viptime` bigint NULL DEFAULT 0 COMMENT '变更秒数',
  `before` bigint NULL DEFAULT 0 COMMENT '变更前过期时间',
  `after` bigint NULL DEFAULT 0 COMMENT '变更后过期时间',
  `memo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '备注',
  `createtime` bigint NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '会员VIP变动表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_user_vip_log
-- ----------------------------

-- ----------------------------
-- Table structure for fa_version
-- ----------------------------
DROP TABLE IF EXISTS `fa_version`;
CREATE TABLE `fa_version`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `oldversion` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '旧版本号',
  `newversion` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '新版本号',
  `packagesize` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '包大小',
  `content` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '升级内容',
  `downloadurl` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '下载地址',
  `enforce` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '强制更新',
  `createtime` bigint NULL DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint NULL DEFAULT NULL COMMENT '更新时间',
  `weigh` int NOT NULL DEFAULT 0 COMMENT '权重',
  `status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '版本表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_version
-- ----------------------------

-- ----------------------------
-- Table structure for fa_web_activity_setting
-- ----------------------------
DROP TABLE IF EXISTS `fa_web_activity_setting`;
CREATE TABLE `fa_web_activity_setting`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `sign_flg` smallint NULL DEFAULT 0 COMMENT '签到活动开启标志【0：禁用；1：启用】',
  `continuity_give_type` smallint NULL DEFAULT NULL COMMENT '每天签到赠送类型【1：VIP；2：积分】',
  `continuity_give_number` int NULL DEFAULT NULL COMMENT '每天签到赠送VIP天数或者积分数量',
  `seven_days_give_type` smallint NULL DEFAULT NULL COMMENT '7天连续签到赠送类型【1：VIP；2：积分】',
  `seven_days_give_number` int NULL DEFAULT NULL COMMENT '7天连续签到赠送VIP天数或者积分数量',
  `invite_flg` smallint NULL DEFAULT 0 COMMENT '邀请好友活动开启标记【0：禁用；1：启用】',
  `invite_stage_number` int NULL DEFAULT NULL COMMENT '每邀请多少好友',
  `invite_stage_give_type` smallint NULL DEFAULT NULL COMMENT '每邀请多少好友赠送类型【1：VIP；2：积分】',
  `invite_stage_give_number` int NULL DEFAULT NULL COMMENT '每邀请多少好友赠送VIP天数或者积分数量',
  `invite_total_number` int NULL DEFAULT NULL COMMENT '累计邀多少好友',
  `invite_total_give_type` smallint NULL DEFAULT NULL COMMENT '累计邀多少好友赠送类型【1：VIP；2：积分】',
  `invite_total_give_number` int NULL DEFAULT NULL COMMENT '累计邀多少好友赠送VIP天数或者积分数量',
  `ip_repeat_flg` smallint NULL DEFAULT 0 COMMENT 'IP去重开启标志【0：禁用；1：启用】',
  `create_time` bigint NOT NULL COMMENT '记录生成时间',
  `update_time` bigint NULL DEFAULT NULL COMMENT '最后一次记录更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '活动设置表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_web_activity_setting
-- ----------------------------
INSERT INTO `fa_web_activity_setting` VALUES (1, 1, 2, 5, 1, 10, 1, 1, 2, 5, 10, 1, 10, 1, 1686311585, 1689847143);

-- ----------------------------
-- Table structure for fa_web_advertisement
-- ----------------------------
DROP TABLE IF EXISTS `fa_web_advertisement`;
CREATE TABLE `fa_web_advertisement`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `position` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '广告位置',
  `title` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '广告标题',
  `url` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '广告链接url，联盟js广告时，填些JS代码',
  `image_pc` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '广告图片PC版',
  `image_h5` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '广告图片H5版',
  `type` smallint NOT NULL DEFAULT 1 COMMENT '类型【1：固定广告位 2：JS广告  3：多值广告位；4：视频广告】',
  `status` smallint NOT NULL DEFAULT 0 COMMENT '状态【1：启用  0：禁用】',
  `create_time` bigint NOT NULL COMMENT '记录生成时间',
  `update_time` bigint NULL DEFAULT NULL COMMENT '最后一次记录更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '广告管理' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_web_advertisement
-- ----------------------------
INSERT INTO `fa_web_advertisement` VALUES (1, '横幅广告', '[\"广告1\",\"广告2\",\"广告3\"]', '[\"https:\\/\\/www.taohuadao.org\",\"https:\\/\\/bbs.taohuadao.net\",\"https:\\/\\/www.taohuadao.net\"]', '[\"\\/assets\\/img\\/default\\/00d85371368663ba614ed4af9a9b2cfb.gif\",\"\\/assets\\/img\\/default\\/f8ab032c334cf3308fead8ca35643952.gif\",\"\\/assets\\/img\\/default\\/d19b3c6efdb0745bcc520f406588242d.gif\"]', '[\"\\/assets\\/img\\/default\\/8f2869a672ac73020bd7e73e67c7d89f.gif\",\"\\/assets\\/img\\/default\\/3ff18040c7520bc24f18eb8be5c47113.gif\",\"\\/assets\\/img\\/default\\/9868f7047c39fc19ad3492cbdcffaa85.gif\"]', 3, 1, 1685950107, 1685950107);
INSERT INTO `fa_web_advertisement` VALUES (2, 'Banner 广告', '[\"banner1\",\"banner2\",\"banner3\"]', '[\"https:\\/\\/www.taohuadao.org\",\"https:\\/\\/bbs.taohuadao.net\",\"https:\\/\\/www.taohuadao.net\"]', '[\"\\/assets\\/img\\/default\\/4bfb587a865f38a3309ad8ae2112c64f.jpg\",\"\\/assets\\/img\\/default\\/cd1366814974468201e013aaaabd51f7.jpg\",\"\\/assets\\/img\\/default\\/2f41d830e4c930a08ee6bd5f0a265edf.jpg\"]', '[\"\\/assets\\/img\\/default\\/1aa0655eb24cb1c63d179238f69369a4.jpg\",\"\\/assets\\/img\\/default\\/62b04365140df05c976db33e16813af0.jpg\",\"\\/assets\\/img\\/default\\/4239e6fb599c565e2527ff21507781f9.jpg\"]', 3, 1, 1685950246, 1685950246);
INSERT INTO `fa_web_advertisement` VALUES (3, '右对联', '广告推广', '', '', '', 1, 0, 1685950278, 1685950278);
INSERT INTO `fa_web_advertisement` VALUES (4, '左对联', '广告推广', '', '', '', 1, 0, 1685950336, 1685950336);
INSERT INTO `fa_web_advertisement` VALUES (5, '联盟 JS', '联盟JS', '', '', '', 2, 0, 1685950344, 1685950344);
INSERT INTO `fa_web_advertisement` VALUES (6, '启动图', '', '', '', '', 1, 0, 1685950362, 1685950362);
INSERT INTO `fa_web_advertisement` VALUES (7, '启动弹窗', '', '', '', '', 1, 0, 1685950371, 1685950371);
INSERT INTO `fa_web_advertisement` VALUES (8, '暂停', '暂停', '', '', '', 1, 0, 1685950379, 1685950379);
INSERT INTO `fa_web_advertisement` VALUES (9, '片头', '片头', '', '', '', 4, 0, 1685950388, 1685950388);
INSERT INTO `fa_web_advertisement` VALUES (10, '底飘', '底飘', 'https://www.taohuadao.org', '/assets/img/default/9e5cb9f1cce70dd29f2281a94386e200.jpg', '/assets/img/default/d765137128daa897cb60d0fb7f366305.jpg', 1, 1, 1685950396, 1685950396);
INSERT INTO `fa_web_advertisement` VALUES (11, '顶飘', '顶飘', 'https://www.taohuadao.org', '/assets/img/default/64774870f4550ee305f1c55387ae97af.jpg', '/assets/img/default/1081f89d981cf5bed2969cf5b8cf5e05.jpg', 1, 1, 1685950405, 1685950405);

-- ----------------------------
-- Table structure for fa_web_categorys
-- ----------------------------
DROP TABLE IF EXISTS `fa_web_categorys`;
CREATE TABLE `fa_web_categorys`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `index` smallint NULL DEFAULT NULL COMMENT '类别的排序序列号',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '类别的中文名称',
  `pinyin` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '类别名称拼音',
  `belong_to` int NULL DEFAULT 0 COMMENT '所属上级id，0则为顶级分类',
  `logo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Logo图像数据',
  `front` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '封面图数据',
  `map_to` int NOT NULL DEFAULT 0 COMMENT '映射到总站分类的列表id',
  `map_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '映射到总站分类名称',
  `mode` smallint NOT NULL COMMENT '类型【0：免费 1：vip  2：点映】',
  `integral` int NULL DEFAULT NULL COMMENT '积分',
  `status` smallint NOT NULL COMMENT '状态【1：启用  0：禁用】',
  `create_time` bigint NOT NULL COMMENT '记录生成时间',
  `update_time` bigint NULL DEFAULT NULL COMMENT '最后一次操作更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `IX_mapto`(`map_to`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 25 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '分类信息' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_web_categorys
-- ----------------------------
INSERT INTO `fa_web_categorys` VALUES (1, 0, '日本AV', 'ribenAV', 0, NULL, NULL, 0, NULL, 0, 0, 1, 1690455358, 1690455630);
INSERT INTO `fa_web_categorys` VALUES (2, 0, '欧美大片', 'oumeidapian', 0, NULL, NULL, 0, NULL, 0, 0, 1, 1690455660, NULL);
INSERT INTO `fa_web_categorys` VALUES (3, 0, '国产原创', 'guochanyuanchuang', 0, NULL, NULL, 0, NULL, 0, 0, 1, 1690455739, 1690455784);
INSERT INTO `fa_web_categorys` VALUES (4, 0, '动漫精品', 'dongmanjingpin', 0, NULL, NULL, 0, NULL, 0, 0, 1, 1690455770, NULL);
INSERT INTO `fa_web_categorys` VALUES (5, 0, '欧美影片', 'oumeiyingpian', 2, NULL, NULL, 15, '欧美影片', 1, 0, 1, 1690457956, NULL);
INSERT INTO `fa_web_categorys` VALUES (6, 0, '精彩动漫', 'jingcaidongman', 4, NULL, NULL, 16, '精彩动漫', 0, 0, 1, 1690458138, NULL);
INSERT INTO `fa_web_categorys` VALUES (7, 0, '三级综艺', 'sanjizongyi', 1, NULL, NULL, 13, '制服诱惑', 0, 0, 1, 1690458212, NULL);
INSERT INTO `fa_web_categorys` VALUES (8, 0, '精彩乱伦', 'jingcailuanlun', 1, NULL, NULL, 14, '精彩乱伦', 0, 0, 1, 1690458275, NULL);
INSERT INTO `fa_web_categorys` VALUES (9, 0, '主奴调教', 'zhunutiaojiao', 1, NULL, NULL, 17, '主奴调教', 0, 0, 1, 1690458317, NULL);
INSERT INTO `fa_web_categorys` VALUES (10, 0, '角色剧情', 'juesejuqing', 1, NULL, NULL, 18, '角色剧情', 0, 0, 1, 1690458357, NULL);
INSERT INTO `fa_web_categorys` VALUES (11, 0, '直接开啪', 'zhijiekaipa', 1, NULL, NULL, 19, '直接开啪', 0, 0, 1, 1690458394, NULL);
INSERT INTO `fa_web_categorys` VALUES (12, 0, '男友视角', 'nanyoushijiao', 1, NULL, NULL, 20, '男友视角', 0, 0, 1, 1690458521, NULL);
INSERT INTO `fa_web_categorys` VALUES (13, 0, '中文字幕', 'zhongwenzimu', 1, NULL, NULL, 6, '中文字幕', 0, 0, 1, 1690458554, NULL);
INSERT INTO `fa_web_categorys` VALUES (14, 0, '国产精品', 'guochanjingpin', 3, NULL, NULL, 7, '国产精品', 0, 0, 1, 1690458595, NULL);
INSERT INTO `fa_web_categorys` VALUES (15, 0, '网红头条', 'wanghongtoutiao', 3, NULL, NULL, 8, '网红头条', 0, 0, 1, 1690458632, NULL);
INSERT INTO `fa_web_categorys` VALUES (16, 0, '传媒影视', 'chuanmeiyingshi', 3, NULL, NULL, 9, '传媒影视', 0, 0, 1, 1690458668, NULL);
INSERT INTO `fa_web_categorys` VALUES (17, 0, 'AI换脸', 'AIhuanlian', 3, NULL, NULL, 10, 'AI换脸', 0, 0, 1, 1690458714, 1691593723);
INSERT INTO `fa_web_categorys` VALUES (18, 0, '女同欢愉', 'nvtonghuanyu', 1, NULL, NULL, 21, '女同欢愉', 0, 0, 1, 1690458775, NULL);
INSERT INTO `fa_web_categorys` VALUES (19, 0, '人妖男同', 'renyaonantong', 1, NULL, NULL, 12, '人妖男同', 0, 0, 1, 1690458839, NULL);
INSERT INTO `fa_web_categorys` VALUES (20, 0, '凌辱强暴', 'lingruqiangbao', 1, NULL, NULL, 22, '凌辱强暴', 0, 0, 1, 1690458913, NULL);
INSERT INTO `fa_web_categorys` VALUES (21, 0, '无码影片', 'wumayingpian', 1, NULL, NULL, 23, '无码影片', 0, 0, 1, 1690458944, NULL);
INSERT INTO `fa_web_categorys` VALUES (22, 0, '丝袜美腿', 'siwameitui', 1, NULL, NULL, 24, '丝袜美腿', 0, 0, 1, 1690458982, NULL);
INSERT INTO `fa_web_categorys` VALUES (23, 0, '多P群交', 'duoPqunjiao', 1, NULL, NULL, 25, '多P群交', 0, 0, 1, 1690459017, NULL);
INSERT INTO `fa_web_categorys` VALUES (24, 0, '盗摄偷拍', 'daoshetoupai', 3, NULL, NULL, 26, '盗摄偷拍', 0, 0, 1, 1690459058, NULL);

-- ----------------------------
-- Table structure for fa_web_charge_integral
-- ----------------------------
DROP TABLE IF EXISTS `fa_web_charge_integral`;
CREATE TABLE `fa_web_charge_integral`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `index` smallint NULL DEFAULT NULL COMMENT '排序序列号',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `integral` int NOT NULL COMMENT '积分数',
  `plus_free` int NULL DEFAULT NULL COMMENT '赠送积分',
  `amount` decimal(10, 2) NOT NULL COMMENT '金额',
  `status` smallint NULL DEFAULT NULL COMMENT '状态【1：启用  0：禁用】',
  `create_time` bigint NOT NULL COMMENT '记录生成时间',
  `update_time` bigint NULL DEFAULT NULL COMMENT '最后一次记录更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '积分价格配置' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_web_charge_integral
-- ----------------------------
INSERT INTO `fa_web_charge_integral` VALUES (1, 0, '充30送10积分', 30, 10, 30.00, 1, 1686053332, 1686053387);
INSERT INTO `fa_web_charge_integral` VALUES (2, 0, '充50送30积分', 50, 30, 50.00, 1, 1687093576, 1687093576);
INSERT INTO `fa_web_charge_integral` VALUES (3, 0, '充100送50积分', 100, 50, 100.00, 1, 1687093596, 1687093596);
INSERT INTO `fa_web_charge_integral` VALUES (4, 0, '充150送100积分', 150, 100, 150.00, 1, 1687093619, 1687093619);
INSERT INTO `fa_web_charge_integral` VALUES (5, 0, '充200送150积分', 200, 150, 200.00, 1, 1687093640, 1687093640);
INSERT INTO `fa_web_charge_integral` VALUES (6, 0, '充250送200积分', 250, 200, 250.00, 1, 1687093671, 1687093671);
INSERT INTO `fa_web_charge_integral` VALUES (7, 0, '充300送250积分', 300, 250, 300.00, 1, 1687093698, 1687093698);
INSERT INTO `fa_web_charge_integral` VALUES (8, 0, '充350送300积分', 350, 300, 350.00, 1, 1687094196, 1689843077);

-- ----------------------------
-- Table structure for fa_web_dic_guid_site_type
-- ----------------------------
DROP TABLE IF EXISTS `fa_web_dic_guid_site_type`;
CREATE TABLE `fa_web_dic_guid_site_type`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '类别名称',
  `create_time` bigint NULL DEFAULT NULL COMMENT '记录生成时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `IX_name`(`name`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '导航站点类别字典表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_web_dic_guid_site_type
-- ----------------------------

-- ----------------------------
-- Table structure for fa_web_dic_message_type
-- ----------------------------
DROP TABLE IF EXISTS `fa_web_dic_message_type`;
CREATE TABLE `fa_web_dic_message_type`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '类型名称',
  `create_time` bigint NULL DEFAULT NULL COMMENT '记录生成时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `IX_name`(`name`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '消息类型字典表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_web_dic_message_type
-- ----------------------------

-- ----------------------------
-- Table structure for fa_web_guid_site
-- ----------------------------
DROP TABLE IF EXISTS `fa_web_guid_site`;
CREATE TABLE `fa_web_guid_site`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `index` smallint NULL DEFAULT NULL COMMENT '排序序列号',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '站点名称',
  `url` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '站点跳转地址',
  `logo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '站点logo图片',
  `type_id` int NULL DEFAULT NULL COMMENT '站点所属类别ID',
  `weight` smallint NULL DEFAULT NULL COMMENT 'SEO状态【0：禁止；1：允许】',
  `status` smallint NULL DEFAULT NULL COMMENT '状态【1：启用 0：禁用】',
  `create_time` bigint NULL DEFAULT NULL COMMENT '记录生成时间',
  `update_time` bigint NULL DEFAULT NULL COMMENT '最后一次记录更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '导航站点信息' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_web_guid_site
-- ----------------------------

-- ----------------------------
-- Table structure for fa_web_links
-- ----------------------------
DROP TABLE IF EXISTS `fa_web_links`;
CREATE TABLE `fa_web_links`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `index` smallint NULL DEFAULT NULL COMMENT '友链的排序序列号',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '友链名称',
  `url` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '友链的跳转地址',
  `weight` smallint NULL DEFAULT 1 COMMENT 'SEO状态【1：允许 0：禁止】',
  `uv_from` smallint NULL DEFAULT 0 COMMENT '访问量',
  `status` smallint NOT NULL COMMENT '状态【1：启用 0：禁用】',
  `create_time` bigint NOT NULL COMMENT '记录生成时间',
  `update_time` bigint NULL DEFAULT NULL COMMENT '最后一次记录更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '友链管理' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_web_links
-- ----------------------------
INSERT INTO `fa_web_links` VALUES (1, 2, '桃花岛论坛', 'https://bbs.taohuadao.net', 1, 0, 1, 1685979438, 1693220711);
INSERT INTO `fa_web_links` VALUES (2, 0, '桃花岛增值服务', 'https://www.taohuadao.net', 1, 0, 1, 1686683729, 1693220525);
INSERT INTO `fa_web_links` VALUES (3, 3, '桃花岛官网', 'https://www.taohuadao.org', 1, 0, 1, 1690461654, 1693220725);
INSERT INTO `fa_web_links` VALUES (4, 4, '官方群', 'https://t.me/taohuadaoCMS', 1, 0, 1, 1693220765, NULL);

-- ----------------------------
-- Table structure for fa_web_message_read
-- ----------------------------
DROP TABLE IF EXISTS `fa_web_message_read`;
CREATE TABLE `fa_web_message_read`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL COMMENT '用户ID',
  `message_bitmap` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '已读消息ID',
  `create_time` bigint NOT NULL COMMENT '创建时间',
  `update_time` bigint NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `IX_userid`(`user_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '消息已读记录' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_web_message_read
-- ----------------------------

-- ----------------------------
-- Table structure for fa_web_messages
-- ----------------------------
DROP TABLE IF EXISTS `fa_web_messages`;
CREATE TABLE `fa_web_messages`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `index` smallint NULL DEFAULT NULL COMMENT '排序序列号',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '消息名称',
  `type_id` int NULL DEFAULT NULL COMMENT '消息分类id',
  `content` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '消息内容',
  `read_flg` smallint NULL DEFAULT NULL COMMENT '是否已读【0：未读；1：已读】',
  `status` smallint NULL DEFAULT NULL COMMENT '状态【1：启用；0：禁用】',
  `create_time` bigint NULL DEFAULT NULL COMMENT '记录生成时间',
  `update_time` bigint NULL DEFAULT NULL COMMENT '最后一次记录更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '公告消息' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_web_messages
-- ----------------------------

-- ----------------------------
-- Table structure for fa_web_model_download
-- ----------------------------
DROP TABLE IF EXISTS `fa_web_model_download`;
CREATE TABLE `fa_web_model_download`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `model_id` int NOT NULL COMMENT '模板ID',
  `model_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '模板名称',
  `model_version` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '模板版本号',
  `model_path` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模板路径',
  `model_theme` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '模板配色',
  `model_status` smallint NULL DEFAULT NULL COMMENT '状态【1：启用  0：禁用】',
  `create_time` bigint NULL DEFAULT NULL COMMENT '模板下载时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `IX_modelid`(`model_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '模板下载信息' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_web_model_download
-- ----------------------------

-- ----------------------------
-- Table structure for fa_web_orders
-- ----------------------------
DROP TABLE IF EXISTS `fa_web_orders`;
CREATE TABLE `fa_web_orders`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `site_id` int NOT NULL COMMENT '站点ID',
  `order_sn` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '订单号',
  `type` smallint NOT NULL COMMENT '订单类型【1：VIP订单；2：积分订单】',
  `user_id` int NULL DEFAULT NULL COMMENT '用户ID',
  `businessid` int NULL DEFAULT NULL COMMENT '套餐ID/积分ID',
  `pay_method_id` int NOT NULL COMMENT '支付方式ID',
  `pay_status` smallint NULL DEFAULT NULL COMMENT '支付状态【1：已支付；2：待支付；3：支付失败；4：已超时；5：未知】',
  `create_time` bigint NOT NULL COMMENT '记录生成时间',
  `update_time` bigint NULL DEFAULT NULL COMMENT '最后一次记录更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `IX_ordersn`(`order_sn`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '订单表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_web_orders
-- ----------------------------

-- ----------------------------
-- Table structure for fa_web_payment_method
-- ----------------------------
DROP TABLE IF EXISTS `fa_web_payment_method`;
CREATE TABLE `fa_web_payment_method`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `index` smallint NULL DEFAULT NULL COMMENT '排序序列号',
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '支付方式名称',
  `image` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '支付图标',
  `channel` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '支付接口：下拉alipay，wechatpay，Epay，手动支付',
  `pay_param_one` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '支付宝appid/Wechat appid/Epay url',
  `pay_param_two` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '支付宝公钥/Wechat 商号/Epay pid',
  `pay_param_three` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '支付宝私钥/Wechat api密钥/Epay秘钥',
  `qrcode_image` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '手动支付的收款二维码图片',
  `status` smallint NULL DEFAULT NULL COMMENT '状态【1：启用  0：禁用】',
  `create_time` bigint NOT NULL COMMENT '记录生成时间',
  `update_time` bigint NULL DEFAULT NULL COMMENT '最后一次记录更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '支付配置' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_web_payment_method
-- ----------------------------
INSERT INTO `fa_web_payment_method` VALUES (1, 0, '微信扫码支付', '/assets/img/default/f3a33800be9218fec4d11542f6721f2b.jpeg', 'Manual', NULL, NULL, NULL, '/assets/img/default/8f9844ea7a9b0df0adb00fa4aab56351.png', 1, 1686289576, 1686291665);
INSERT INTO `fa_web_payment_method` VALUES (2, 0, '支付宝扫码支付', '/assets/img/default/d63e8dad09016665cc2f35570a61c5c2.jpeg', 'Manual', NULL, NULL, NULL, '/assets/img/default/8f9844ea7a9b0df0adb00fa4aab56351.png', 1, 1686290453, 1689846090);
INSERT INTO `fa_web_payment_method` VALUES (3, 0, '银联扫码支付', '/assets/img/default/bd5e4ed574d350d3a10fc48cff2541d6.jpeg', 'Manual', NULL, NULL, NULL, '/assets/img/default/8f9844ea7a9b0df0adb00fa4aab56351.png', 1, 1686291789, 1687095984);

-- ----------------------------
-- Table structure for fa_web_sites
-- ----------------------------
DROP TABLE IF EXISTS `fa_web_sites`;
CREATE TABLE `fa_web_sites`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '站点名称，页面title显示',
  `domain` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '域名',
  `describe` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '站点title描述',
  `keyword` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '站点关键字',
  `model_id` int NOT NULL COMMENT '模板下载记录表ID',
  `model_path` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模板路径',
  `model_theme` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '模板配色',
  `js_code` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '用来统计的JS代码',
  `logo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Logo图像',
  `icon` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Icon图像',
  `declaration` varchar(10000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '网站或免责声明',
  `customer_link` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '客服链接',
  `customer_code` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '客服JS代码',
  `android` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '安卓APK文件路径',
  `ios` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'IOS描述文件路径',
  `status` smallint NOT NULL COMMENT '状态【1：启用  0：禁用】',
  `create_time` bigint NOT NULL COMMENT '记录生成时间',
  `update_time` bigint NULL DEFAULT NULL COMMENT '最后一次操作更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `IX_domain`(`domain`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '站点记录' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_web_sites
-- ----------------------------

-- ----------------------------
-- Table structure for fa_web_statistics
-- ----------------------------
DROP TABLE IF EXISTS `fa_web_statistics`;
CREATE TABLE `fa_web_statistics`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `site_id` int NOT NULL COMMENT '站点id',
  `pv` int NULL DEFAULT 0 COMMENT '浏览量',
  `uv` int NULL DEFAULT 0 COMMENT '访客数',
  `rv` int NULL DEFAULT 0 COMMENT '注册数',
  `vv` int NULL DEFAULT 0 COMMENT '播放量',
  `mv` decimal(20, 2) NULL DEFAULT NULL COMMENT '收款金额',
  `start_time` bigint NOT NULL COMMENT '统计的起始时间',
  `end_time` bigint NOT NULL COMMENT '统计的结束时间',
  `create_time` bigint NULL DEFAULT NULL COMMENT '记录生成时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `IX_createtime`(`create_time`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '数据统计' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_web_statistics
-- ----------------------------

-- ----------------------------
-- Table structure for fa_web_user_sign_log
-- ----------------------------
DROP TABLE IF EXISTS `fa_web_user_sign_log`;
CREATE TABLE `fa_web_user_sign_log`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NULL DEFAULT NULL COMMENT '用户ID',
  `create_time` bigint NULL DEFAULT NULL COMMENT '记录生成时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `IX_createtime`(`create_time`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '用户签到记录' ROW_FORMAT = FIXED;

-- ----------------------------
-- Records of fa_web_user_sign_log
-- ----------------------------

-- ----------------------------
-- Table structure for fa_web_video_collection
-- ----------------------------
DROP TABLE IF EXISTS `fa_web_video_collection`;
CREATE TABLE `fa_web_video_collection`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL COMMENT '用户ID',
  `video_vid` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '视频ID',
  `video_info` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '视频信息',
  `create_time` bigint NOT NULL COMMENT '记录生成时间',
  `update_time` bigint NULL DEFAULT NULL COMMENT '最后一次记录更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `IX_userid`(`user_id`) USING BTREE,
  INDEX `IX_createtime`(`create_time`) USING BTREE,
  INDEX `IX_userid_videoid`(`user_id`, `video_vid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '收藏记录' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_web_video_collection
-- ----------------------------

-- ----------------------------
-- Table structure for fa_web_video_like
-- ----------------------------
DROP TABLE IF EXISTS `fa_web_video_like`;
CREATE TABLE `fa_web_video_like`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL COMMENT '用户ID',
  `video_vid` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '视频ID',
  `video_info` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '视频信息',
  `type` smallint NULL DEFAULT NULL COMMENT '类型【0：攒；1：踩】',
  `create_time` bigint NOT NULL COMMENT '记录生成时间',
  `update_time` bigint NULL DEFAULT NULL COMMENT '最后一次记录更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `IX_userid`(`user_id`) USING BTREE,
  INDEX `IX_createtime`(`create_time`) USING BTREE,
  INDEX `IX_userid_videoid`(`user_id`, `video_vid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '点赞记录' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_web_video_like
-- ----------------------------

-- ----------------------------
-- Table structure for fa_web_video_purchase
-- ----------------------------
DROP TABLE IF EXISTS `fa_web_video_purchase`;
CREATE TABLE `fa_web_video_purchase`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL COMMENT '用户ID',
  `score` int NULL DEFAULT NULL COMMENT '所用积分',
  `video_vid` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '视频ID',
  `video_info` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '视频信息',
  `create_time` bigint NOT NULL COMMENT '记录生成时间',
  `update_time` bigint NULL DEFAULT NULL COMMENT '最后一次记录更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `IX_userid`(`user_id`) USING BTREE,
  INDEX `IX_createtime`(`create_time`) USING BTREE,
  INDEX `IX_userid_videoid`(`user_id`, `video_vid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '视频购买信息' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_web_video_purchase
-- ----------------------------

-- ----------------------------
-- Table structure for fa_web_vip_packages
-- ----------------------------
DROP TABLE IF EXISTS `fa_web_vip_packages`;
CREATE TABLE `fa_web_vip_packages`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '套餐名称',
  `days` int NULL DEFAULT NULL COMMENT '天数',
  `original_price` decimal(10, 2) NULL DEFAULT NULL COMMENT '套餐原价格',
  `sale_price` decimal(10, 2) NOT NULL COMMENT '销售价格',
  `integral` int NULL DEFAULT NULL COMMENT '赠送积分',
  `status` smallint NULL DEFAULT NULL COMMENT '状态【1：启用  0：禁用】',
  `create_time` bigint NOT NULL COMMENT '记录生成时间',
  `update_time` bigint NULL DEFAULT NULL COMMENT '最后一次记录更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'VIP价格配置' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fa_web_vip_packages
-- ----------------------------
INSERT INTO `fa_web_vip_packages` VALUES (1, '永久', -1, 1200.00, 999.00, 45, 1, 1686048835, 1687103631);
INSERT INTO `fa_web_vip_packages` VALUES (2, '两年付', 730, 300.00, 200.00, 40, 1, 1686048835, 1687103630);
INSERT INTO `fa_web_vip_packages` VALUES (3, '年付', 365, 150.00, 100.00, 35, 1, 1686048835, 1687103629);
INSERT INTO `fa_web_vip_packages` VALUES (4, '半年付', 180, 120.00, 60.00, 30, 1, 1686048835, 1687103629);
INSERT INTO `fa_web_vip_packages` VALUES (5, '季付', 90, 80.00, 40.00, 25, 1, 1686048835, 1687103611);
INSERT INTO `fa_web_vip_packages` VALUES (6, '月付', 30, 60.00, 30.00, 20, 1, 1686048835, 1688469696);

SET FOREIGN_KEY_CHECKS = 1;
