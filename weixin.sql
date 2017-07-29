CREATE TABLE `weixin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `biz` varchar(255) DEFAULT '' COMMENT '公众号唯一标识biz',
  `collect` int(11) DEFAULT '1' COMMENT '记录采集时间的时间戳',
  PRIMARY KEY (`id`)
) ;


CREATE TABLE `post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `biz` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '文章对应的公众号biz',
  `field_id` int(11) NOT NULL COMMENT '微信定义的一个id，每条文章唯一',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '文章标题',
  `title_encode` text CHARACTER SET utf8 NOT NULL COMMENT '文章编码，防止文章出现emoji',
  `digest` varchar(500) NOT NULL DEFAULT '' COMMENT '文章摘要',
  `content_url` varchar(500) CHARACTER SET utf8 NOT NULL COMMENT '文章地址',
  `source_url` varchar(500) CHARACTER SET utf8 NOT NULL COMMENT '阅读原文地址',
  `cover` varchar(500) CHARACTER SET utf8 NOT NULL COMMENT '封面图片',
  `is_multi` int(11) NOT NULL COMMENT '是否多图文',
  `is_top` int(11) NOT NULL COMMENT '是否头条',
  `datetime` int(11) NOT NULL COMMENT '文章时间戳',
  `readNum` int(11) NOT NULL DEFAULT '1' COMMENT '文章阅读量',
  `likeNum` int(11) NOT NULL DEFAULT '0' COMMENT '文章点赞量',
  PRIMARY KEY (`id`)
) ;
ALTER TABLE  `post` ADD  `comment` TEXT NOT NULL DEFAULT  ''
CREATE TABLE `tmplist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `content_url` varchar(100) DEFAULT NULL COMMENT '文章地址',
  `load` int(11) DEFAULT '0' COMMENT '读取中标记',
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_url` (`content_url`)
) ;
