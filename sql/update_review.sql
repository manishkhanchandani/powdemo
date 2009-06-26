 ALTER TABLE `reviews` ADD `review_type` ENUM( 'user', 'critic' ) NOT NULL DEFAULT 'critic',
ADD `review_source` VARCHAR( 200 ) NULL 