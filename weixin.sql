   -- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2017 年 08 月 09 日 01:56
-- 服务器版本: 5.5.53
-- PHP 版本: 5.4.45

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- 数据库: `weixin_caiji`
--

-- --------------------------------------------------------

--
-- 表的结构 `post`
--

CREATE TABLE IF NOT EXISTS `post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `biz` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '文章对应的公众号biz',
  `field_id` int(11) DEFAULT NULL COMMENT '微信定义的一个id，每条文章唯一',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '文章标题',
  `title_encode` text CHARACTER SET utf8 NOT NULL COMMENT '文章编码，防止文章出现emoji',
  `digest` varchar(500) NOT NULL DEFAULT '' COMMENT '文章摘要',
  `source_url` varchar(500) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '阅读原文地址',
  `cover` varchar(500) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '封面图片',
  `is_multi` int(11) DEFAULT '0' COMMENT '是否多图文',
  `is_top` int(11) NOT NULL DEFAULT '0' COMMENT '是否头条',
  `datetime` int(11) DEFAULT NULL COMMENT '文章时间戳',
  `readNum` int(11) NOT NULL DEFAULT '1' COMMENT '文章阅读量',
  `likeNum` int(11) NOT NULL DEFAULT '0' COMMENT '文章点赞量',
  `comment` text NOT NULL,
  `mid` varchar(100) DEFAULT '' COMMENT '文章mid',
  `content_url` varchar(500) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=81 ;

-- --------------------------------------------------------

--
-- 表的结构 `tmplist`
--

CREATE TABLE IF NOT EXISTS `tmplist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `load` int(10) DEFAULT '0' COMMENT '读取中标记',
  `content_url` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=81 ;

-- --------------------------------------------------------

--
-- 表的结构 `weixin`
--

CREATE TABLE IF NOT EXISTS `weixin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `biz` varchar(255) DEFAULT '' COMMENT '公众号唯一标识biz',
  `collect` int(11) DEFAULT '1' COMMENT '记录采集时间的时间戳',
  `name` varchar(200) DEFAULT '',
  `des` varchar(200) DEFAULT '',
  `icon` varchar(200) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=2 ;
