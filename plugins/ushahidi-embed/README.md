ushahidi-embed
==============

Ushahidi plugin to support simple embedding of the ushahidi map in an iframe

You can use this to make the map on the main page of the site display in an iframe embedded in another site

Just install this in the plugins directory, then go into the ushahidi admin and activate the plugin

Then you should have a new view at

  http://your.domain.com/embed

You put this in a iframe with something like 

    <iframe width="960" height="650" src="http://your.domain.com/embed" />

NOTE: you need any links displayed inside the iframe to take you out of the iframe when you click them.
Otherwise things will look funny.   This is done in the links in the plugin with 
  target='_parent' 
  
in the anchor tags

HOWEVER, there are two anchor tags that appear in the map popup that also have to be fixed. This plugin does not 
fix those, so for now we are just manually editing the files when we install this.

The two places you need to make the edits are:

  media/js/ushahidi.js
  
  in the middle of the file somewhere:
  
    if (typeof(event.feature.attributes.link) != 'undefined' &&
        event.feature.attributes.link != '') {
    
        content += "<a target='_parent' href='"+event.feature.attributes.link+"'>" +
                "More Information</a><br/>";
    }

  
  
  application/controller/json.php
  
  in the last function in the file:
      
        protected function get_title($title, $url)
        {
                $item_name = "<a target='_parent' href='$url'>".$title."</a>";
                $item_name = str_replace(array(chr(10),chr(13)), ' ', $item_name);
                return $item_name;
        }

  
  

