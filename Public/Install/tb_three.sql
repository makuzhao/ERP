/*
Navicat MySQL Data Transfer

Source Server         : Hong
Source Server Version : 50538
Source Host           : localhost:3308
Source Database       : db_boss

Target Server Type    : MYSQL
Target Server Version : 50538
File Encoding         : 65001

Date: 2014-12-12 14:49:01
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `tb_goods`
-- ----------------------------
DROP TABLE IF EXISTS `tb_goods`;
CREATE TABLE `tb_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `barcode` varchar(255) NOT NULL COMMENT '条形码',
  `product` varchar(255) DEFAULT NULL COMMENT '商品名',
  `letter` varchar(255) DEFAULT NULL COMMENT '大写首字母',
  `type` varchar(255) DEFAULT NULL COMMENT '商品类型',
  `standard` varchar(255) DEFAULT NULL COMMENT '规格',
  `unit` varchar(10) DEFAULT NULL COMMENT '计量单位',
  `price` decimal(18,2) DEFAULT NULL COMMENT '建议售价',
  `info` text COMMENT '商品描述',
  PRIMARY KEY (`id`,`barcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tb_goods
-- ----------------------------

-- ----------------------------
-- Table structure for `tb_sales`
-- ----------------------------
DROP TABLE IF EXISTS `tb_sales`;
CREATE TABLE `tb_sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `out_id` int(11) DEFAULT NULL COMMENT 'tb_out 哪条数据的ID',
  `barcode` varchar(255) NOT NULL COMMENT '商品条形码',
  `product` varchar(255) DEFAULT NULL COMMENT '商品名称',
  `letter` varchar(255) DEFAULT NULL COMMENT '首字母大写',
  `num` int(11) DEFAULT '0' COMMENT '数量',
  `presell` decimal(18,2) DEFAULT '0.00' COMMENT '预售价',
  `amount` decimal(18,2) DEFAULT '0.00' COMMENT '交易金额',
  `salesman` varchar(20) DEFAULT 'No One' COMMENT '销售员',
  `saledate` varchar(20) DEFAULT NULL COMMENT '售出日期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tb_sales
-- ----------------------------

-- ----------------------------
-- Table structure for `tb_storage`
-- ----------------------------
DROP TABLE IF EXISTS `tb_storage`;
CREATE TABLE `tb_storage` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '商品ID',
  `encode` varchar(255) NOT NULL COMMENT '商品编码',
  `barcode` varchar(255) NOT NULL COMMENT '商品独一无二的条形码',
  `letter` varchar(255) DEFAULT NULL COMMENT '首字母大写',
  `product` varchar(255) DEFAULT NULL COMMENT '商品名',
  `outtime` char(20) DEFAULT NULL COMMENT '出货时间',
  `outamount` int(11) DEFAULT NULL COMMENT '出货数量',
  `outprice` decimal(18,2) DEFAULT '0.00' COMMENT '出货单价',
  `outpresell` decimal(18,2) DEFAULT NULL COMMENT '销售价',
  `outsum` decimal(18,2) DEFAULT '0.00' COMMENT '出货金额=出货单价*出货量',
  `people` varchar(255) DEFAULT NULL COMMENT '经手人',
  PRIMARY KEY (`id`,`barcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tb_storage
-- ----------------------------
