<?php echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>"; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom"<?php if(isset($georss)) echo ' xmlns:georss="http://www.georss.org/georss"';?>>
	<channel>
		<title><?php echo html::specialchars($feed_title); ?></title>
		<link><?php echo $site_url; ?></link>
		<pubDate><?php echo gmdate("D, d M Y H:i:s T", strtotime($feed_date)); ?></pubDate>
		<description><?php echo html::specialchars($feed_description); ?></description>
		<generator>Ushahidi Platform</generator>
		<atom:link href="<?php echo $feed_url; ?>" rel="self" type="application/rss+xml" />
		
		<?php // Event::feed_rss_head - Add to the feed head ?>
		<?php Event::run('ushahidi_action.feed_rss_head'); ?>

		<?php foreach ($items as $item): ?>
			<item>
			<title><?php echo html::specialchars($item['title']); ?></title>
			<link><?php echo $item['link']; ?></link>
			<description><![CDATA[<?php echo html::specialchars($item['description']); ?>]]></description>
			<pubDate><?php echo gmdate("D, d M Y H:i:s T", strtotime($item['date'])); ?></pubDate>
			<guid><?php if(isset($item['guid'])) echo $item['guid']; else echo $item['link'] ?></guid>

			<?php if (isset($item['point'])): ?>
				<georss:point><?php echo $item['point'][0]." ".$item['point'][1]; ?></georss:point>
			<?php endif; ?>

			<?php foreach ($item['categories'] as $category): ?>
				<category><?php echo html::specialchars($category); ?></category>
			<?php endforeach; ?>

			<?php // Event::feed_rss_item - Add to the feed item ?>
			<?php Event::run('ushahidi_action.feed_rss_item', $item['id']); ?>
			</item>
		<?php endforeach; ?>
		
	</channel>
</rss>
