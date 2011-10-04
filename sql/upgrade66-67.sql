ALTER TABLE `category` MODIFY `category_image` VARCHAR(255);
ALTER TABLE `category` MODIFY `category_image_thumb` VARCHAR(255);

UPDATE `settings` SET `db_version` = '67' WHERE `id`=1 LIMIT 1;