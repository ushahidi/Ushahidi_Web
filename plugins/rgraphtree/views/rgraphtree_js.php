var labelType, useGradients, nativeTextSupport, animate;

(function() {
  var ua = navigator.userAgent,
      iStuff = ua.match(/iPhone/i) || ua.match(/iPad/i),
      typeOfCanvas = typeof HTMLCanvasElement,
      nativeCanvasSupport = (typeOfCanvas == 'object' || typeOfCanvas == 'function'),
      textSupport = nativeCanvasSupport 
        && (typeof document.createElement('canvas').getContext('2d').fillText == 'function');
  //I'm setting this based on the fact that ExCanvas provides text support for IE
  //and that as of today iPhone/iPad current text support is lame
  labelType = (!nativeCanvasSupport || (textSupport && !iStuff))? 'Native' : 'HTML';
  nativeTextSupport = labelType == 'Native';
  useGradients = nativeCanvasSupport;
  animate = !(iStuff || !nativeCanvasSupport);
})();

var Log = {
  elem: false,
  write: function(text){
    //if (!this.elem) 
      //this.elem = document.getElementById('log');
    //this.elem.innerHTML = text;
    //this.elem.style.left = (200 - this.elem.offsetWidth / 2) + 'px';
  }
};


function rgraphtree_activate(into){
    //init data
    var json = <?php echo $json; ?>;
    //end
    
    //init RGraph
    var rgraph = new $jit.RGraph({
        //Where to append the visualization
        injectInto: into,
        //Optional: create a background canvas that plots
        //concentric circles.
        background: {
          CanvasStyles: {
			'strokeStyle': '#555'
          }
        },
        //Add navigation capabilities:
        //zooming by scrolling and panning.
        Navigation: {
          enable: true,
          panning: true,
          zooming: 10
        },
        //Set Node and Edge styles.
        Node: {
          color: '#666673'
        },
        
        Edge: {
          color: '#990000',
          lineWidth:1
        },

        onBeforeCompute: function(node){
            Log.write("centering " + node.name + "...");
            //Add the relation list in the right column.
            //This list is taken from the data property of each JSON node.
            //$jit.id('inner-details').innerHTML = node.data.relation;
        },
        
        onAfterCompute: function(){
            Log.write("done");
        },
        //Add the name of the node in the correponding label
        //and a click handler to move the graph.
        //This method is called once, on label creation.
        onCreateLabel: function(domElement, node){
            domElement.innerHTML = node.name;
            domElement.onclick = function(){
                rgraph.onClick(node.id);
            };
        },
        //Change some label dom properties.
        //This method is called each time a label is plotted.
        onPlaceLabel: function(domElement, node){
            var style = domElement.style;
            style.display = '';
            style.cursor = 'pointer';

            if (node._depth <?php echo '<='; ?> 1) {
                style.fontSize = "0.8em";
                style.color = "#333";
            
            } else if(node._depth == 2){
                style.fontSize = "0.7em";
                style.color = "#494949";
            
            } else {
                style.display = 'none';
            }

            var left = parseInt(style.left);
            var w = domElement.offsetWidth;
            style.left = (left - w / 2) + 'px';
        }
    });
    //load JSON data
    rgraph.loadJSON(json);
    //trigger small animation
    rgraph.graph.eachNode(function(n) {
      var pos = n.getPos();
      pos.setc(-200, -200);
    });
    rgraph.compute('end');
    rgraph.fx.animate({
      modes:['polar'],
      duration: 2000
    });
    //end
}

// Draw it when the page is ready
$(document).ready(rgraphtree_activate('rgraphtree'));

// Do some full screen magic
$('a.bigger_rgraphtree').click(function(){

	$('a.bigger_rgraphtree').css({"display":"none"});
	
	$('.rgraphtree-content').css({"height":"100%","width":"100%","position":"absolute","left":"0px","top":"0px","z-index":"9000","padding":"0px","margin":"0px"});
	
	$('#rgraphtree').css({"height":$(window).height(), "width":$(window).width()});
	
	$('#rgraphtree').empty();
	
	rgraphtree_activate('rgraphtree');
	
	$('a.smaller_rgraphtree').css({"display":"inline","position":"absolute","z-index":"1000"});
});

// Do some small screen magic
$('a.smaller_rgraphtree').click(function(){
	
	$('a.smaller_rgraphtree').css({"display":"none"});
	
	$('.rgraphtree-content').css({"height":"250px","width":"285px","position":"relative"});
	
	$('#rgraphtree').css({"height":"250px","width":"285px","position":"relative"});
	
	$('#rgraphtree').empty();
	
	rgraphtree_activate('rgraphtree');
	
	$('a.bigger_rgraphtree').css({"display":"inline"});
	
});

