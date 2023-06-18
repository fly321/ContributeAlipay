/*
 Navicat Premium Data Transfer

 Source Server         : mysql_docker
 Source Server Type    : MySQL
 Source Server Version : 80027 (8.0.27)
 Source Host           : host.docker.internal:3307
 Source Schema         : alipay

 Target Server Type    : MySQL
 Target Server Version : 80027 (8.0.27)
 File Encoding         : 65001

 Date: 18/06/2023 16:07:33
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for alipay_order
-- ----------------------------
DROP TABLE IF EXISTS `alipay_order`;
CREATE TABLE `alipay_order`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `sn` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '订单号',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '备注',
  `price` decimal(10, 2) UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` int UNSIGNED NULL DEFAULT NULL COMMENT '修改时间',
  `alipay_sn` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '支付宝官方sn',
  `pay_time` int NULL DEFAULT NULL COMMENT '支付时间',
  `status` tinyint(1) NULL DEFAULT 0 COMMENT '状态:0=待支付,1=已支付',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `SN`(`sn` ASC) USING BTREE,
  INDEX `ALISN`(`alipay_sn` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of alipay_order
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
