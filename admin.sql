/*
Navicat MariaDB Data Transfer

Source Server         : localhost
Source Server Version : 100130
Source Host           : localhost:3306
Source Database       : yii2

Target Server Type    : MariaDB
Target Server Version : 100130
File Encoding         : 65001

Date: 2018-04-20 22:24:34
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES ('1', 'admin', '9KmOWMhPNH8_cxDy4z4O8c2QC-_fWxCX', '$2y$13$NsQCaljSrni2Q.rup5Viw.LSwn9qi96BRdU1HMq1bQ6yuUR48aLQy', null, 'liu.lipeng@newsnow.com.cn', '10', '1523542318', '1524234168');
INSERT INTO `admin` VALUES ('2', 'cpaul', 'P-jaReUApvbm8YJAw7HaiSDRYAvV_z8u', '$2y$13$mj0jW.N6gUjOzG92Yq.qauQIwo/jzhwSlfGeFDdKDxKea9AwQOj4W', null, 'paul@133.com', '10', '1523718544', '1523718544');
