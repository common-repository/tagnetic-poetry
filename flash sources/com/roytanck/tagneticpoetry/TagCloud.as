package com.roytanck.tagneticpoetry
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.display.Stage;
	import flash.display.StageAlign;
	import flash.display.StageScaleMode;
	import flash.display.LoaderInfo;
	import flash.events.Event;
	import flash.net.URLRequest;
	import flash.net.URLLoader;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	import flash.text.TextFormat;
	import flash.geom.ColorTransform;
	import flash.events.MouseEvent;
	import flash.ui.ContextMenu;
	import flash.ui.ContextMenuItem;
	import flash.events.ContextMenuEvent;
	import flash.net.navigateToURL;
	import flash.net.URLRequest;
	import com.roytanck.tagneticpoetry.Tag;

	public class TagCloud extends MovieClip	{
		// private vars
		private var mcList:Array;
		private var tagsize:Number;
		private var holder:MovieClip;
		private var myXML:XML;
		
		public function TagCloud(){
			// settings
			var swfStage:Stage = this.stage;
			swfStage.scaleMode = StageScaleMode.NO_SCALE;
			swfStage.align = StageAlign.TOP_LEFT;
			// add context menu item
			var myContextMenu:ContextMenu = new ContextMenu();
			myContextMenu.hideBuiltInItems();
			var item:ContextMenuItem = new ContextMenuItem("Tagnetic Poetry by Roy Tanck and Merel Zwart");
			myContextMenu.customItems.push(item);
			this.contextMenu = myContextMenu;
			item.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, menuItemSelectHandler);
			// get flashvar for tag size
			tagsize = ( this.loaderInfo.parameters.tagsize == null ) ? 100 : Number(this.loaderInfo.parameters.tagsize);
			// load or parse the data
			myXML = new XML();
			if( this.loaderInfo.parameters.mode == null )	{
				// base url
				var a:Array = this.loaderInfo.url.split("/");
				a.pop();
				var baseURL:String = a.join("/") + "/";
				// load XML file
				var XMLPath = ( this.loaderInfo.parameters.xmlpath == null ) ? baseURL + "tagcloud.xml" : this.loaderInfo.parameters.xmlpath;
				var myXMLURL:URLRequest = new URLRequest( XMLPath );
				var myLoader:URLLoader = new URLLoader(myXMLURL);
				myLoader.addEventListener("complete", xmlLoaded);
				function xmlLoaded(event:Event):void {
						myXML = XML(myLoader.data); // test with tags
						init( myXML );
				}
			} else {
				switch( this.loaderInfo.parameters.mode ){
					case "tags":
						myXML = new XML( this.loaderInfo.parameters.tagcloud );
						break;
					case "cats":
						myXML = new XML("<tags></tags>");
						addCategories( this.loaderInfo.parameters.categories );
						break;
					default:
						myXML = new XML( this.loaderInfo.parameters.tagcloud );
						addCategories( this.loaderInfo.parameters.categories );
						break;
				}
				init( myXML );
			}
		}
		
		private function addCategories( cats:String ){
			// unescape leave spaces as '+', so we have to filter these out manually
			cats = unescape(cats).replace(/\+/g, " ");
			// use the fact that WP output line breaks to split the string into bits
			var cArray:Array = cats.split("<br />");
			// loop though them to find the smallest and largest 'tags'
			var largest:Number = 0;
			var pattern:RegExp = /\d/g;
			for( var i:Number=0; i<cArray.length-1; i++ ){
				var parts:Array = cArray[i].split( "</a>" );
				// user regular extpressions to get rid of extra stuff
				var nr:Number = Number( parts[1].match(pattern).join("") );
				largest = Math.max( largest, nr );
			}
			// how much must we scale the categories to match the tags?
			var scalefactor:Number = 14 / largest;
			// loop through them again and add to XML
			for( i=0; i<cArray.length-1; i++ ){
				parts = cArray[i].split( "</a>" );
				nr = Number( parts[1].match(pattern).join("") );
				var node:String = "<a style=\"" + ((nr*scalefactor)+8) + "\"" + parts[0].substr( parts[0].indexOf("<a")+2 ) + "</a>";
				myXML.appendChild( node );
			}
		}
		
		private function init( o:XML ):void {
			// set some vars
			mcList = [];
			// create holder mc, center it
			holder = new MovieClip();
			var s:Stage = this.stage;
			addChild(holder);
			// create movie clips
			for each( var node:XML in o.a ){
				// create mc
				var mc:Tag = new Tag( node );
				mc.scaleX = mc.scaleY = tagsize/100;
				holder.addChild(mc);
				// store reference
				mcList.push( mc );
			}
			// distribute the tags
			positionAll();
		}
		
		private function positionAll():void {		
			var max:Number = mcList.length;
			var stageAspect:Number = stage.stageWidth/stage.stageHeight;
			// sort by importance
			mcList.sort( function( a:Tag, b:Tag){ return (a.size<b.size) ? 1 : -1; } );
			// distibute
			for( var i:Number=0; i<max; i++){
				var pos:Array = [];
				mcList[i].rotation = Math.random()*8 - 4;
				var border:Number = 0.025 * (( stage.stageWidth + stage.stageHeight )/2);
				var xroom:Number = stage.stageWidth - mcList[i].width - border;
				var yroom:Number = stage.stageHeight - mcList[i].height - border;
				var xOffset:Number = mcList[i].width/2;
				var yOffset:Number = mcList[i].height/2;
				// find up to 100 possible positions, test them for hit and total aspect of holder
				if( xroom > mcList[i].width && yroom > mcList[i].height ){
					for( var j:Number=0; j<100; j++ ){
						mcList[i].x = (border/2) + Math.random()*xroom + xOffset;
						mcList[i].y = (border/2) + Math.random()*yroom  + yOffset;
						var hit:Boolean = false;
						for( var k:Number=0; k<i; k++ ){
							if( mcList[i].hitTestObject( mcList[k] ) ){ hit = true; }
						}
						if( !hit ){
							pos.push( { x: mcList[i].x, y: mcList[i].y } );
							break;
						}
					}
				}
				if( pos.length > 0 ){
					mcList[i].x = pos[0].x;
					mcList[i].y = pos[0].y;
				} else {
					trace("no suitable place found");
					mcList[i].visible = false;
				}
			}
		}
		
		private function menuItemSelectHandler( e:ContextMenuEvent ):void {
			var request:URLRequest = new URLRequest( "http://www.roytanck.com" );
			navigateToURL(request);
		}

	}

}