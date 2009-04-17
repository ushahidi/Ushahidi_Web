<?php 
/**
 * Feed atom view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
<?php echo "<?xml version=\"1.0\"?>"; ?>
<feed xmlns="http://www.w3.org/2005/Atom"<?php if(isset($georss)) echo ' xmlns:georss="http://www.georss.org/georss"';?>>
  <title type="text"><?php echo $feed_title; ?></title>
  <subtitle type="html"><?php echo $feed_description; ?></subtitle>
  <updated><?php echo gmdate("c", strtotime($feed_date)); ?></updated>
  <id><?php echo $feed_url; ?></id>
  <link rel="alternate" type="text/html" href="<?php echo $site_url; ?>"/>
  <link rel="self" type="application/atom+xml" href="<?php echo $feed_url; ?>"/>
  <generator uri="<?php echo $site_url; ?>" version="1.0">Ushahidi Engine</generator><?php
foreach ($items as $item) { ?>

  <entry>
    <title><?php echo $item['title']; ?></title>
    <link rel="alternate" type="text/html" href="<?php echo $item['link']; ?>"/>
    <updated><?php echo gmdate("c", strtotime($item['date'])); ?></updated>
    <published><?php echo gmdate("c", strtotime($item['date'])); ?></published>
    <content type="xhtml" xml:lang="en" 
     xml:base="http://diveintomark.org/">
      <div xmlns="http://www.w3.org/1999/xhtml">
        <?php echo $item['description']; ?>
      </div>
    </content>
<?php if(isset($item['point'])) echo "  <georss:point>".$item['point'][0]." ".$item['point'][1]."</georss:point>\n"; ?>
  </entry>	<?php 
	}	?>
</feed>