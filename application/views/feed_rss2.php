<?php echo "<?xml version=\"1.0\"?>"; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title><?php echo $feed_title; ?></title>
		<link><?php echo $site_url; ?></link>
		<pubDate><?php echo $feed_date; ?></pubDate>
		<description><?php echo $feed_description; ?></description>
		<generator>Ushahidi Engine</generator>
		<atom:link href="<?php echo $feed_url; ?>" rel="self" type="application/rss+xml" />
<?php echo $feeds; ?>
	</channel>
</rss>