<?php echo "<?xml version=\"1.0\"?>"; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title><?php echo $feed_title; ?></title>
		<link><?php echo $feed_url; ?></link>
		<pubDate><?php echo $feed_date; ?></pubDate>
		<description><?php echo $feed_description; ?></descriptions>
		<generator>Ushahidi Engine</generator>
		<?php echo $feeds; ?>
		<atom:link href="http://dallas.example.com/rss.xml" rel="self" type="application/rss+xml" />
	</channel>
</rss>