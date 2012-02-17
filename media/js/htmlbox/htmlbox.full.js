//============================= START OF CLASS ==============================//
// CLASS: HtmlBox                                                            //
//===========================================================================//
   /**
    * HtmlBox is a cross-browser widget that replaces the normal textboxes with
    * rich text area like in OpenOffice Text. It has Ajax support out of the box.
    * PS. It requires JQuery in order to function.
    * TODO: Smilies, CSS for Safari
    *<code>
    * </code>
    * Copyright@2007-2011 Remiya Solutions All rights reserved! 
	* @author Remiya Solutions
	* @version 4.0.3
    */
(function($){ 
$.fn.htmlbox=function(options){
    // START: Settings
        // Are there any plugins?
    var colors = (typeof document.htmlbox_colors === 'function')?document.htmlbox_colors():['silver','silver','white','white','yellow','yellow','orange','orange','red','red','green','green','blue','blue','brown','brown','black','black'];
	var styles = (typeof document.htmlbox_styles === 'function')?document.htmlbox_styles():[['No Styles','','']];
	var syntax = (typeof document.htmlbox_syntax === 'function')?document.htmlbox_syntax():[['No Syntax','','']];
	var urm = (typeof htmlbox_undo_redo_manager === 'function')?new htmlbox_undo_redo_manager():false;
	// Default option
	var d={
	    toolbars:[["bold","italic","underline"]],      // Buttons
		idir:"./images/",// HtmlBox Image Directory, This is needed for the images to work
		icons:"default",  // Icon set
		about:true,
		skin:"default",  // Skin, silver
		output:"xhtml",  // Output
		toolbar_height:24,// Toolbar height
		tool_height:16,   // Tools height
		tool_width:16,    // Tools width
		tool_image_height:16,  // Tools image height
		tool_image_width:16,  // Tools image width
		css:"body{margin:3px;font-family:verdana;font-size:11px;}p{margin:0px;}",
		success:function(data){alert(data);}, // AJAX on success
		error:function(a,b,c){return this;}   // AJAX on error
	};
	
	// User options
    d = $.extend(d, options);
    
    // Is forward slash added to the image directory
    if(d.idir.substring(d.idir.length-1)!=="/"){d.idir+="/";}
    // END: Settings
	
	// ------------- START: PRIVATE METHODS -----------------//
	
    //========================= START OF METHOD ===========================//
    //  METHOD: get_selection                                              //
    //=====================================================================//
	   /**
	    * Returns the selected (X)HTML code
	    * @access private
	    */
	var get_selection = function(){
	    var range;
		if($.browser.msie){
	       range = d.iframe.contentWindow.document.selection.createRange();
		   if (range.htmlText && range.text){return range.htmlText;}
	    }else{
		   if (d.iframe.contentWindow.getSelection) {
		       var selection = d.iframe.contentWindow.getSelection();
		       if (selection.rangeCount>0&&window.XMLSerializer){
                   range=selection.getRangeAt(0);
                   var html=new XMLSerializer().serializeToString(range.cloneContents());
			       return html;
               }if (selection.rangeCount > 0) {
		           range = selection.getRangeAt(0);
			       var clonedSelection = range.cloneContents();
			       var div = document.createElement('div');
			       div.appendChild(clonedSelection);
			       return div.innerHTML;
		       }
			}
		}
	};
    //=====================================================================//
    //  METHOD: get_selection                                              //
    //========================== END OF METHOD ============================//
	
	//========================= START OF METHOD ===========================//
    //  METHOD: in_array                                                   //
    //=====================================================================//
	 /**
	    * Coppies the PHP in_array function. This is useful for Objects.
	    * @access private
	    */
	var in_array=function(o,a){
	   for (var i in a){ if((i===o)){return true;} }
       return false;
	};
    //=====================================================================//
    //  METHOD: in_array                                                   //
    //========================== END OF METHOD ============================//
	
    //========================= START OF METHOD ===========================//
    //  METHOD: insert_text                                                //
    //=====================================================================//
	   /**
	    * Inserts text at the cursor position or selection
	    * @access private
	    */
	var insert_text = function(text,start,end){
	    if($.browser.msie){
		    d.iframe.contentWindow.focus();
	        if(typeof d.idoc.selection !== "undefined" && d.idoc.selection.type !== "Text" && d.idoc.selection.type !== "None"){start = false;d.idoc.selection.clear();}
		    var sel = d.idoc.selection.createRange();sel.pasteHTML(text);
			if (text.indexOf("\n") === -1) {
			    if (start === false) {} else {
                    if (typeof start !== "undefined") {
                        sel.moveStart("character", - text.length + start);
                        sel.moveEnd("character", - end);
                    } else {
                        sel.moveStart("character", - text.length);
                    }
                }
                sel.select();
            }
		}else{
		    d.idoc.execCommand("insertHTML", false, text);
		}
		// Updating the textarea component, so whenever it is posted it will send all the data
	    if ($("#"+d.id).is(":visible") === false) {
	        var html = $("#1"+d.id).is(":visible")?$("#"+d.id).val():html = d.iframe.contentWindow.document.body.innerHTML;		    
	        html = (typeof getXHTML === 'function')?getXHTML(html):html;
		    $("#"+d.id).val(html);
			if(urm){urm.add(html);} // Undo Redo
		    if(undefined!==d.change){d.change();}
	    }		
	};
    //=====================================================================//
    //  METHOD: insert_text                                                //
    //========================== END OF METHOD ============================//

	//========================= START OF METHOD ===========================//
    //  METHOD: keyup                                                      //
    //=====================================================================//
	   /**
	    * Keyup event.
	    * @access private 
	    */
	var keyup = function(e){
	    // Updating the textarea component, so whenever it is posted it will send all the data
		var html = $("#1"+d.id).is(":visible")?$("#"+d.id).val():html = d.iframe.contentWindow.document.body.innerHTML;
		if(urm){urm.add(html);} // Undo Redo
	    html = (typeof getXHTML === 'function')?getXHTML(html):html;
		$("#"+d.id).val(html);
		if(undefined!==d.change){d.change();}
	};
    //=====================================================================//
    //  METHOD: keyup                                                      //
    //========================== END OF METHOD ============================//
	
    //========================= START OF METHOD ===========================//
    //  METHOD: style                                                      //
    //=====================================================================//
	   /**
	    * Sets the CSS style to the HtmlBox iframe
	    * @access private
	    */
    var style = function(){
	    // START: HtmlBox Style
        if(d.css.indexOf("background:")===-1){d.css+="body{background:white;}";}
        if(d.css.indexOf("background-image:")===-1){
		    d.css=d.css+"body{background-image:none;}";
		}
        
		if( d.idoc.createStyleSheet) {
		  d.idoc.createStyleSheet().cssText=d.css;
		}else {
		  var css=d.idoc.createElement('link');
		  css.rel='stylesheet'; css.href='data:text/css,'+escape(d.css);
		  if($.browser.opera){
			 d.idoc.documentElement.appendChild(css);
		  }else{
			 d.idoc.getElementsByTagName("head")[0].appendChild(css);
		  }
		}
		// END: HtmlBox Style
	};
    //=====================================================================//
    //  METHOD: style                                                      //
    //========================== END OF METHOD ============================//
	
    //========================= START OF METHOD ===========================//
    //  METHOD: toolbar                                                    //
    //=====================================================================//
	   /**
	    * The toolbar of HtmlBox
	    * @return this
		* @access private
	    */
	var toolbar=function(){
	    var h = "";
	    if(d.about&&!in_array(d.toolbars[0],"about")){d.toolbars[0][d.toolbars[0].length]="separator";d.toolbars[0][d.toolbars[0].length]="about";}
		for(var k=0;k<d.toolbars.length;k++){
		    var toolbar = d.toolbars[k];
			h += "<tr><td class='"+d.id+"_tb' valign='middle'><table cellspacing='1' cellpadding='0'>";
			for(var i=0;i<(toolbar.length);i++){
				var img = (d.icons==="default")?d.idir+"default/"+toolbar[i]+".gif":d.idir+d.icons+"/"+toolbar[i]+".png";
	            if(undefined===toolbar[i]){continue;}
	            // START: Custom button
	            else if(typeof(toolbar[i])!=='string'){
	                img = d.idir+d.icons+"/"+toolbar[i].icon;
	                var cmd = "var cmd = unescape(\""+escape( toolbar[i].command.toString() )+"\");eval(\"var fn=\"+cmd);fn()'";
	                h += "<td class='"+d.id+"_html_button' valign='middle' align='center' onclick='"+cmd+"' title='"+toolbar[i].tooltip+"'><image src='"+img+"'></td>";
			    }
	            // END: Custom button
				else if(toolbar[i]==="separator"){h += "<td valign='middle' align='center'><image src='"+d.idir+"separator.gif' style='margin-right:1px;margin-left:3px;height:13px;'></td>";}
				else if(toolbar[i]==="fontsize"){
				    h += "<td valign='middle' align='center'><select id='"+d.id+"_fontsize' onchange='global_hb[\""+d.id+"\"].cmd(\"fontsize\",this.options[this.selectedIndex].value)' style='font-size:12px;'><option value='' selected>- SIZE -</option><option value='1'>1</option><option value='2'>2</option><option value='3'>3</option><option value='4'>4</option><option value='5'>5</option><option value='6'>6</option><option value='7'>7</option></select></td>";
			    }else if(toolbar[i]==="fontfamily"){
				    h += "<td valign='middle' align='center'><select id='"+d.id+"_fontfamily' onchange='global_hb[\""+d.id+"\"].cmd(\"fontname\",this.options[this.selectedIndex].value)' style='font-size:12px;'><option value='' selected>- FONT -</option><option value='arial' style='font-family:arial;'>Arial</option><option value='courier' style='font-family:courier;'>Courier</option><option value='cursive' style='font-family:cursive;'>Cursive</option><option value='georgia' style='font-family:georgia;'>Georgia</option><option value='monospace' style='font-family:monospace;'>Monospace</option><option value='tahoma' style='font-family:tahoma;'>Tahoma</option><option value='verdana' style='font-family:verdana;'>Verdana</option></select></td>";
				}else if(toolbar[i]==="formats"){
				    h += "<td valign='middle' align='center'><select id='"+d.id+"_formats' onchange='global_hb[\""+d.id+"\"].cmd(\"format\",this.options[this.selectedIndex].value)' style='font-size:12px;'><option value='' selected>- FORMATS -</option><option value='h1'>Heading 1</option><option value='h2'>Heading 2</option><option value='h3'>Heading 3</option><option value='h4'>Heading 4</option><option value='h5'>Heading 5</option><option value='h6'>Heading 6</option><option value='p'>Paragraph</option><option value='pindent'>First Indent</option><option value='pre'>Preformatted</option></select></td>";
				}else if(toolbar[i]==="fontcolor"){
				    h += "<td valign='middle' align='center'><select id='"+d.id+"_fontcolor' onchange='global_hb[\""+d.id+"\"].cmd(\"fontcolor\",this.options[this.selectedIndex].value)' style='font-size:12px;'><option value='' selected>-COLOR-</option>";
					for(var m=0;m<colors.length;m++){ if(m%2){continue;}
					   h+="<option value='"+colors[m]+"' style='background:"+colors[m]+";color:"+colors[m]+";'>"+colors[m]+"</option>";
					}
					h += "</select></td>";
				}else if(toolbar[i]==="highlight"){
				    h += "<td valign='middle' align='center'><select id='"+d.id+"_highlight' onchange='global_hb[\""+d.id+"\"].cmd(\"backcolor\",this.options[this.selectedIndex].value)' style='font-size:12px;'><option value='' selected>-HIGHLIGHT-</option>";
					for(var n=0;n<colors.length;n++){ if(n%2){continue;}
					   h+="<option value='"+colors[n]+"' style='background:"+colors[n]+";color:"+colors[n]+";'>"+colors[n]+"</option>";
					}
					h += "</select></td>";
				}else if(toolbar[i]==="styles"){
				    h += "<td valign='middle' align='center'><select id='"+d.id+"_styles' onchange='global_hb[\""+d.id+"\"].cmd(\"styles\",this.options[this.selectedIndex].value);this.options[0].selected=\"true\";' style='font-size:12px;' style='background:white;'><option value='' selected>-STYLES-</option>";
					for(var o=0;o<styles.length;o++){ if(o%2){continue;}
					   h+="<option value='"+o+"' style='background:white;color:red;'>"+styles[o][0]+"</option>";
					}
					h += "</select></td>";
				}else if(toolbar[i]==="syntax"){
				    h += "<td valign='middle' align='center'><select id='"+d.id+"_syntax' onchange='global_hb[\""+d.id+"\"].cmd(\"syntax\",this.options[this.selectedIndex].value);this.options[0].selected=\"true\";' style='font-size:12px;'><option value='' selected>-SYNTAX-</option>";
					for(var p=0;p<syntax.length;p++){ if(p%2){continue;}
					   h+="<option value='"+p+"' style='background:white;color:red;'>"+syntax[p][0]+"</option>";
					}
					h += "</select></td>";
				}
				// Commands
				var cmds = {"about":"About","bold":"Bold","center":"Center","code":"View Code","copy":"Copy","cut":"Cut","hr":"Insert Line","link":"Insert Link","image":"Insert Image","indent":"Indent","italic":"Italic","justify":"Justify","left":"Left","ol":"Numbered List","outdent":"Outdent","paragraph":"Insert Paragraph","paste":"Paste","quote":"Quote","redo":"Redo","removeformat":"Remove Format","right":"Right","strike":"Strikethrough","striptags":"Strip Tags","sub":"Subscript","sup":"Superscript","ul":"Bulleted List","underline":"Underline","undo":"Undo","unlink":"Remove Link"};
				if(in_array(toolbar[i],cmds)){h += "<td class='"+d.id+"_html_button' valign='middle' align='center' onclick='global_hb[\""+d.id+"\"].cmd(\""+toolbar[i]+"\")' title='"+cmds[toolbar[i]]+"'><image src='"+img+"'></td>";}
		    }
			h += "</table></td></tr>";
		}
		return h;
	};
    //=====================================================================//
    //  METHOD: toolbar                                                    //
    //========================== END OF METHOD ============================//
	
    //========================= START OF METHOD ===========================//
    //  METHOD: wrap_tags                                                  //
    //=====================================================================//
	   /**
	    * Wraps tags around the cursor position or selection
	    * @access private
	    */
	this.wrap_tags = function(start,end){
	   var sel = get_selection(); 
	   if(undefined===sel){sel="";}
	   if(undefined===end){end="";}
	   insert_text(start+sel+end,start.length,end.length);
	};
    //=====================================================================//
    //  METHOD: wrap_tags                                                  //
    //========================== END OF METHOD ============================//
	
	// -------------- END: PRIVATE METHODS ------------------//
	
	// ------------- START: PUBLIC METHODS -----------------//
    //========================= START OF METHOD ===========================//
    //  METHOD: _init                                                      //
    //=====================================================================//
	/**
	  * Draws the HtmlBox on the screen
	  * @return this
	  * @access private	  
	  */
	this._init = function(is_init){
	    if(undefined===window.global_hb){global_hb=[];}
        if(!$(this).attr("id")){$(this).attr("id","jqhb_"+global_hb.length);d.id="jqhb_"+global_hb.length;global_hb[d.id]=global_hb;}else{d.id=$(this).attr("id");}
	    if(undefined === global_hb[d.id]){global_hb[d.id]=this;}
	    // START: Timeout to allow creation of DesignMode
	    //if(undefined===is_init){setTimeout("global_hb['"+d.id+"'].init(true)",250);return false;}
		// END: Timeout to allow creation of DesignMode
		d.ta_wrap_id = d.id+"_wrap";
		var w=$(this).css("width");var h=$(this).css("height");$(this).wrap("<table id='"+d.id+"_wrap' width='"+w+"' style='height:"+h+";border:2px solid #E9EAEF;' cellspacing='0' cellpadding='0'><tr><td id='"+d.id+"_container'></td></tr></table>");
		// START: Appending toolbar
		$(this).parent().parent().parent().parent().prepend(toolbar());
		$("."+d.id+"_tb").height(d.toolbar_height);
		
		$("."+d.id+"_html_button").each(function(){
			// Set tool dimension
		    $(this).width(d.tool_width).height(d.tool_height);
		    // Set image dimension
		    $(this).find("image").each(function(){$(this).width(d.tool_image_width).height(d.tool_image_height);});
		    // Set borders
		    $(this).css("border","1px solid transparent").css("background","transparent").css("margin","1px 1px 1px 1px").css("padding","1px");
		    $(this).mouseover(function(){$(this).css("border","1px solid #BFCAFF").css("background","#EFF2FF");});
			$(this).mouseout(function(){$(this).css("border","1px solid transparent").css("background","transparent");});
			}
		);
		
		// Selectors
		$("."+d.id+"_tb").find("select").each(function(){
		    $(this).css("border","1px solid #E9EAEF").css("background","transparent").css("margin","2px 2px 3px 2px");
			if($.browser.mozilla){$(this).css("padding","0").css("position","relative").css("top","-2px");}
		    }
		);		 
		// END: Appending toolbar
		
		// START: Skin
		// default
		var hb_border = "1px solid #7F7647";
		var hb_background = "#DFDDD1";
		var tb_border = "1px solid #7F7647";
		if(d.skin==="blue"){
			hb_border = "1px solid #7E9DB9";
			hb_background = "#D7E3F2";
			tb_border = "1px solid #7E9DB9";
		}
        if(d.skin==="red"){
			hb_border = "1px solid #B91E00";
			hb_background = "#FFD7CF";
			tb_border = "1px solid #B91E00";
		}
        if(d.skin==="green"){
			hb_border = "1px solid #8DB900";
			hb_background = "#D5EF86";
			tb_border = "1px solid #8DB900";
		}
        if(d.skin==="silver"){
			hb_border = "1px solid #DDDDDD";
			hb_background = "#F4F4F3";
			tb_border = "1px solid #DDDDDD";
		}
		
		$("#"+d.id+"_wrap").css("border",hb_border);
		$("#"+d.id+"_wrap").css("background",hb_background);
		$("#"+d.id+"_container").css("background","white");
		$("."+d.id+"_tb").css("border-bottom",tb_border);
		
		//$("."+d.id+"_tb").css("background-image","url("+d.idir+"bg_blue.gif)");
		//style='background:silver;border-bottom:1px outset white'
		// END: Skin
		try {
		   var iframe=document.createElement("IFRAME");// var doc=null;
		   $(iframe).css("width",w).css("height",h).attr("id",d.id+"_html").css("border","0");
		   $(this).parent().prepend(iframe);
		   // START: Shortcuts for less code
		   d.iframe = iframe;
		   d.idoc = iframe.contentWindow.document;
		   // END: Shortcuts
		   
		   d.idoc.designMode="on";
		   // START: Insert text
		      // Is there text in the textbox?
		   var text = ($(this).val()==="")?"":$(this).val();
		   if($.browser.mozilla||$.browser.safari){
			   //if(text===""){text="&nbsp;";}
			   d.idoc.open('text/html', 'replace'); d.idoc.write(text); d.idoc.close();
		   }else{
	           if(text!==""){d.idoc.write(text);}
			   else{
			       // Needed by IE to initialize the iframe body
			       if($.browser.msie){d.idoc.write("&nbsp;");}
			   }
		   }
		   // Needed by browsers other than MSIE to become editable
		   if($.browser.msie===false){iframe.contentWindow.document.body.contentEditable = true;}
		   // END: Insert text
		   
		   // START: HtmlBox Style
		   if(d.css.indexOf("background:")===-1){d.css+="body{background:white;}";}
		   if(d.css.indexOf("background-image:")===-1){
		       d.css=d.css+"body{background-image:none;}";
		   }
		   
		   if(d.idoc.createStyleSheet) {
		       setTimeout("global_hb['"+d.id+"'].set_text(global_hb['"+d.id+"'].get_html())",10);
		   }else {style();}
		   // END: HtmlBox Style
		   
		   // START: Adding events
		   if(iframe.contentWindow.document.attachEvent){
		       iframe.contentWindow.document.attachEvent("onkeyup", keyup);
		   }else{
			   iframe.contentWindow.document.addEventListener("keyup",keyup,false);
		   }
		   $(this).hide();
	    }catch(e){
	       alert("This rich text component is not supported by your browser.\n"+e);
		   $(this).show();
	    }
		return this;
	};
    //=====================================================================//
    //  METHOD: _init                                                      //
    //========================== END OF METHOD ============================//
	
    //========================= START OF METHOD ===========================//
    //  METHOD: cmd                                                        //
    //=====================================================================//
	   /**
	    * Executes a user-specified command
		* @since 2.0
	    * @return this
	    */
	this.cmd = function(cmd,arg1){
	   // When user clicks toolbar button make sure it always targets its respective WYSIWYG
       d.iframe.contentWindow.focus();
	   // START: Prepare commands
	   if(cmd==="paragraph"){cmd="format";arg1="p";}
	   var cmds = {"center":"justifycenter","hr":"inserthorizontalrule","justify":"justifyfull","left":"justifyleft","ol":"insertorderedlist","right":"justifyright","strike":"strikethrough","sub":"subscript","sup":"superscript","ul":"insertunorderedlist"};
	   if(in_array(cmd,cmds)){cmd=cmds[cmd];}
       // END: Prepare commands
	   if(cmd==="code"){
	       var text = this.get_html();
	       if($("#"+d.id).is(":visible")){		       
		       $("#"+d.id).hide();		   
		       $("#"+d.id+"_html").show();
			   this.set_text(text);
		   }else{
		       $("#"+d.id).show();
		       $("#"+d.id+"_html").hide();
			   this.set_text(text);
			   $("#"+d.id).focus();
		   }
		   
	   }else if(cmd==="link"){
		   d.idoc.execCommand("createlink", false, prompt("Paste Web Address URL Here:"));
	   }else if(cmd==="image"){
		   d.idoc.execCommand("insertimage", false, prompt("Paste Image URL Here:"));
	   }else if(cmd==="fontsize"){
		   d.idoc.execCommand(cmd, false,arg1);
	   }else if(cmd==="backcolor"){
	       if($.browser.msie){
		   d.idoc.execCommand("backcolor", false,arg1);
		   }else{
		   d.idoc.execCommand("hilitecolor", false,arg1);
		   }
	   }else if(cmd==="fontcolor"){
	       d.idoc.execCommand("forecolor", false,arg1);
	   }else if(cmd==="fontname"){
		   d.idoc.execCommand(cmd, false, arg1);
	   }else if(cmd==="cut"){
	       if($.browser.msie === false){
		       alert("Available in IExplore only.\nUse CTRL+X to cut text!");
		   }else{
	           d.idoc.execCommand('Cut');
	       }
	   }else if(cmd==="copy"){
	       if($.browser.msie === false){
		       alert("Available in IExplore only.\nUse CTRL+C to copy text!");
		   }else{
	           d.idoc.execCommand('Copy');
	       }
	   }else if(cmd==="paste"){
	       if($.browser.msie === false){
		       alert("Available in IExplore only.\nUse CTRL+V to paste text!");
		   }else{
	           d.idoc.execCommand('Paste');
	       }
	   }else if(cmd==="format"){
	       if(arg1==="pindent"){this.wrap_tags('<p style="text-indent:20px;">','</p>');}
		   else if(arg1!==""){d.idoc.execCommand('formatBlock', false, "<"+arg1+">");}
	   }else if(cmd==="striptags"){
	       var sel = get_selection();
		   sel = sel.replace(/(<([^>]+)>)/ig,"");
		   insert_text(sel); 
	   }else if(cmd==="quote"){
	       this.wrap_tags('<br /><div style="position:relative;top:10px;left:11px;font-size:11px;font-family:verdana;">Quote</div><div class="quote" contenteditable="true" style="border:1px inset silver;margin:10px;padding:5px;background:#EFF7FF;">','</div><br />');
	   }else if(cmd==="styles"){
	       this.wrap_tags(styles[arg1][1],styles[arg1][2]);
	   }else if(cmd==="syntax"){
	       this.wrap_tags(syntax[arg1][1],syntax[arg1][2]);
	   }else if(cmd==="bold"){
	       this.wrap_tags("<b>","</b>");
	   }else if(cmd==="undo"&&urm){
	       if(urm.can_undo()){
		       var undo = urm.undo();
			   this.set_text(undo);
			   return true;
		   }
	   }else if(cmd==="redo"&&urm){
	       if(urm.can_redo()){
		       var redo = urm.redo();
			   this.set_text(redo);
			   return true;
		   }
	   }else if(cmd==="about"){
		   var about = "<p>HtmlBox is a modern, cross-browser, interactive, open-source text area built on top of the excellent jQuery library.</p>";
		   about += "<p style='margin:2px;'><b>Official Website:</b> <a href='http://remiya.com' target='_blank'>http://remiya.com</a></p>";
		   about += "<p style='margin:2px;'><b>License:</b> MIT license</p>";
		   about += "<p style='margin:2px;'><b>Version:</b> 4.0</p>";
		   about += "<p style='margin:2px;'><b>Credits:</b></p>";
		   about += "<p style='margin:2px;padding-left:20px;'><a href='http://jquery.com/' target='_blank'>JQuery (JavaScript Framework)</a></p>";
		   about += "<p style='margin:2px;padding-left:20px;'><a href='http://www.famfamfam.com/lab/icons/silk/' target='_blank'>Silk (Icon Set)</a></p>";
		   var html = '<table cellspacing="3" cellpadding="0" width="100%" height="100%"  style="background:#D7E3F2;border:2px solid #7E9DB9;font-family:verdana;font-size:12px;">';
	       html += '<tr><td align="center" valign="middle" height="30" style="font-size:16px;"><b>About HtmlBox</b></td></tr>';
	       html += '<tr><td style="border:1px solid #7E9DB9;background:white;font-size:11px;" valign="top"><div style="overflow:auto;height:140px;" >'+about+'</div></td></tr>';
	       html += '<tr><td height="20"><table width="100%" style="font-family:verdana;font-size:10px;"><tr><td align="left">Copyright&copy;2009 Remiya Solutions<br>All right reserved!</td><td align="right"><button style="width:60px;height:24px;font-family:verdana;font-size:11px;" onclick="$(\'#'+d.id+'_about\').fadeOut(500);">Close</button></td></tr></table></td></tr>';
	       html += '</table>';
	       
	       var w = 300;var h = 200;
	       var top = ($(window).height()-200)/2+$(document).scrollTop();
	       var left = ($(window).width()-300)/2;
	       if ($("#"+d.id+"_about").length === 0){
               $("body").append("<div id='"+d.id+"_about' style='display:none;position:absolute;background:red;width:"+w+"px;height:"+h+"px;top:"+top+"px;left:"+left+"px;'>about</div>");
		       $("#"+d.id+"_about").html(html);
		   }else{
			   $("#"+d.id+"_about").css("top",top);
			   $("#"+d.id+"_about").css("left",left);
		   }
	       $("#"+d.id+"_about").focus();
	       $("#"+d.id+"_about").fadeIn(1000);
	   }else{
	       d.idoc.execCommand(cmd, false, null);
	   }
	   //Setting the changed text to textearea
	   if($("#"+d.id).is(":visible")===false){
	      $("#"+d.id).val(this.get_html());
	      // Register change
		  if(urm){urm.add(this.get_html());}
		  if(undefined!==d.change){d.change();}
	   }
	};
    //=====================================================================//
    //  METHOD: cmd                                                        //
    //========================== END OF METHOD ============================//
		
    //========================= START OF METHOD ===========================//
    //  METHOD: get_text                                                   //
    //=====================================================================//
	   /**
	    * Returns the text without tags of the HtmlBox
		* @since 1.2
	    * @return this
	    */
	this.get_text = function(){
	   // Is textbox visible?
	   if($("#"+d.id).is(":visible")){ return $("#"+d.id).val(); }
	   // Iframe is visible
	   var text;
	   if($.browser.msie){
	       text = d.iframe.contentWindow.document.body.innerText;
	   }else{
	       var html = d.iframe.contentWindow.document.body.ownerDocument.createRange();
		   html.selectNodeContents(d.iframe.contentWindow.document.body);
		   text = html;
	   }
	   return text;
	};
    //=====================================================================//
    //  METHOD: get_text                                                   //
    //========================== END OF METHOD ============================//
	
    //========================= START OF METHOD ===========================//
    //  METHOD: set_text                                                  //
    //=====================================================================//
	   /**
	    * Sets the text as a content of the HtmlBox
		* @since 1.2
	    * @return this
	    */
	this.set_text = function(txt){
	   var text = (undefined===txt)?"":txt;
	   if(text==="" && $.browser.safari){text = "&nbsp;";}// Bug in Chrome and Safari
	   // Is textarea visible? Writing to it.
	   if($("#"+d.id).is(":visible")){
	       $("#"+d.id).val(text);
	   }else{
	     // Textarea not visible. write to iframe
	     if($.browser.mozilla||$.browser.safari){
		   //if($.trim(text)===""){text="&nbsp;";}
		   d.idoc.open('text/html', 'replace'); d.idoc.write(text); d.idoc.close();
	     }else{
		   d.idoc.body.innerHTML = "";
	       if(text!==""){d.idoc.write(text);}
	     }
	     style(); // Setting the CSS style for the iframe
		 d.idoc.body.contentEditable = true;
		 
	   }
	   if(urm){urm.add(this.get_html());}
	   if(undefined!==d.change){d.change();}
	   return this;
	};
    //=====================================================================//
    //  METHOD: set_text                                                   //
    //========================== END OF METHOD ============================//
	
	//========================= START OF METHOD ===========================//
    //  METHOD: get_html                                                   //
    //=====================================================================//
	   /**
	    * Returns the (X)HTML content of the HtmlBox
	    * @return this
	    */
	this.get_html = function(){
	   var html;
	   if($("#"+d.id).is(":visible")){
	      html = $("#"+d.id).val();
	   }else{
	      html = d.iframe.contentWindow.document.body.innerHTML;
	   }
	   if(typeof getXHTML === 'function'){ return getXHTML(html); }else{return html;}
	};
    //=====================================================================//
    //  METHOD: get_html                                                   //
    //========================== END OF METHOD ============================//
    
    //========================= START OF METHOD ===========================//
    //  METHOD: change                                                     //
    //=====================================================================//
       /**
        * Specifies a function to be executed on text change in the HtmlBox
        */
	this.change=function(fn){d.change=fn;return this;};
    //=====================================================================//
    //  METHOD: change                                                     //
    //========================== END OF METHOD ============================//
	
    //========================= START OF METHOD ===========================//
    //  METHOD: remove                                                     //
    //=====================================================================//
       /**
        * Removes the HtmlBox instance from the DOM and the globalspace
        */
	this.remove = function(){
		global_hb[d.id]=undefined;
	    $("#"+d.id+"_wrap").remove();
	    if ($("#"+d.id+"_about").length === 0){$("#"+d.id+"_about").remove();}
	};
    //=====================================================================//
    //  METHOD: remove                                                     //
    //========================== END OF METHOD ============================//
	
    //========================= START OF METHOD ===========================//
    //  METHOD: post                                                       //
    //=====================================================================//
	   /**
	    * Posts the form data to the specified URL using Ajax
        * @param String the URL to post to
	    * @param String the text to be posted, default the (X)HTML text
	    * @return this;
	    */
	this.post=function(url,data){
	    if(undefined===data){data=this.get_html();} data=(d.id+"="+data);
		$.ajax({type: "POST", data: data,url: url,dataType: "html",error:d.error,success:d.success});
	};
    //=====================================================================//
    //  METHOD: post                                                       //
    //========================== END OF METHOD ============================//
	
    //========================= START OF METHOD ===========================//
    //  METHOD: get                                                        //
    //=====================================================================//
	   /**
	    * Gets the form data to the specified URL using Ajax
        * @param String the URL to get to
	    * @param String the text to be posted, default the (X)HTML text
	    * @return this;
	    */
	this.get=function(url,data){
	    if(undefined===data){data=this.get_html();} data=(d.id+"="+data);
		$.ajax({type: "GET", data: data,url: url,dataType: "html",error:d.error,success:d.success});
	};
    //=====================================================================//
    //  METHOD: get                                                        //
    //========================== END OF METHOD ============================//
	
    //========================= START OF METHOD ===========================//
    //  METHOD: success                                                    //
    //=====================================================================//
       /**
        * Specifies what is to be executed on successful Ajax POST or GET
        */
	this.success=function(fn){d.success=fn;return this;};
    //=====================================================================//
    //  METHOD: success                                                    //
    //========================== END OF METHOD ============================//

    //========================= START OF METHOD ===========================//
    //  METHOD: error                                                      //
    //=====================================================================//
       /**
        * Specifies what is to be executed on error Ajax POST or GET
		* @return {HtmlBox} the instance of this HtmlBox
        */
	this.error=function(fn){d.error=fn;return this;};
    //=====================================================================//
    //  METHOD: error                                                      //
    //========================== END OF METHOD ============================//

	// -------------- END: PUBLIC METHODS ------------------//
	this._init(false);
	return this;
};
})(jQuery);
//===========================================================================//
// CLASS: HtmlBox                                                            //
//============================== END OF CLASS ===============================//