package com.roytanck.tagneticpoetry
{
	import flash.display.Sprite;
	import flash.display.MovieClip;
	import flash.text.TextField;
	import flash.events.Event;
	import flash.text.TextFieldAutoSize;
	import flash.text.TextFormat;
	import flash.events.MouseEvent;
	import flash.net.navigateToURL;
	import flash.net.URLRequest;
	import flash.display.Graphics;
	import flash.geom.Rectangle;

	public class Tag extends Sprite {
		
		private var _back:Sprite;
		private var _shadow:Sprite;
		public var _node:XML;
		private var _pos:Object;

		public function Tag( node:XML ){
			_node = node;
			// create the text field
			var tf:TextField = new TextField();
			tf.autoSize = TextFieldAutoSize.LEFT;
			tf.selectable = false;
			// set styles
			var format:TextFormat = new TextFormat();
			format.font = "Arial";
			format.bold = true;
			format.color = 0x333333;
			format.size = 1.6 * getNumberFromString( node["@style"] );
			tf.defaultTextFormat = format;
			tf.embedFonts = true;
			// set text
			tf.text = node.toString();
			tf.mouseEnabled = false;
			addChild(tf);
			// scale and add
			tf.x = -this.width / 2;
			tf.y = -this.height / 2;
			// create the back
			_back = new Sprite();
			_back.graphics.beginFill(0xffffff, 1);
			_back.graphics.drawRect(0, 0, tf.textWidth+20, tf.textHeight+5);
			_back.graphics.endFill();
			addChildAt(_back, 0);
			_back.x = -(tf.textWidth/2) - 10;
			_back.y = -(tf.textHeight/2) - 2;
			// create the drop shadow
			_shadow = new Sprite();
			_shadow.graphics.beginFill(0x000000, .5);
			_shadow.graphics.drawRect(0, 0, tf.textWidth+20, tf.textHeight+5);
			_shadow.graphics.endFill();
			addChildAt(_shadow, 0);
			_shadow.x = -(tf.textWidth/2) - 7;
			_shadow.y = -(tf.textHeight/2) + 1;
			// events
			this.buttonMode = true;
			addEventListener(MouseEvent.MOUSE_DOWN, mouseDownHandler);
			addEventListener(MouseEvent.MOUSE_UP, mouseUpHandler);
		}
		
		private function mouseDownHandler( e:MouseEvent ):void {
			// move to top of parent's display object container
			parent.setChildIndex( this, parent.numChildren - 1 );
			// store current position
			_pos = { x:this.x, y:this.y };
			// calculate available space and start drag
			var bounds = new Rectangle( this.width/2, this.height/2, stage.stageWidth - this.width, stage.stageHeight - this.height );
			this.startDrag( false, bounds );
		}
		
		private function mouseUpHandler( e:MouseEvent ):void {
			this.stopDrag();
			// if not move, goto url
			if( this.x == _pos.x && this.y == _pos.y ){
				var request:URLRequest = new URLRequest( _node["@href"] );
				navigateToURL(request,"_self");
			}
		}

		private function getNumberFromString( s:String ):Number {
			return( Number( s.match( /(\d|\.|\,)/g ).join("").split(",").join(".") ) );
		}
		
		public function get size():Number { return getNumberFromString( _node["@style"] ) };

	}

}
