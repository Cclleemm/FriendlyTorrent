/*
 * FaceList 1.1 - Facebook Style List Box
 *
 * Copyright (c) 2008 Ian Tearle (iantearle.com)
 * Original take by Xavier Domenech 
 * Original autocomplete  by Dylan Verheul, Dan G. Switzer, Anjesh Tuladhar, Jörn Zaefferer
 *
 * $Date: 2008-07-07 11:02:02 -0000 (Wed, 06 Feb 2008) 
 *
 *	ChangeLog:
 *  11/11/08    Changes from Theo Chakkapark (linkedin.com/in/theochakkapark): 
 *				- Now properly matches against all returned elements
 * 				- Does not allow duplicate results
 *				- Does not need to show ID along with the result
 *	08/07/08 	- Added .replace(/ /g,'_') to string replaces to allow for multiple spaces in the string.
 *				- Added data[0] to select first array from split string
 *	09/07/08	- Altered data[1] in remove_tag_data() function so it passes data[1] from the onClick function
 */
(function($) {
	
$.fn.extend({
	autocomplete: function(urlOrData, options) {
		var isUrl = typeof urlOrData == "string";
		options = $.extend({}, $.Autocompleter.defaults, {
			url: isUrl ? urlOrData : null,
			data: isUrl ? null : urlOrData,
			delay: isUrl ? $.Autocompleter.defaults.delay : 10,
			max: options ? 10 : 150
		}, options);
		// if highlight is set to false, replace it with a do-nothing function
		options.highlight = options.highlight || function(value) { return value; };
		
		return this.each(function() {
			new $.Autocompleter(this, options);
		});
	},
	result: function(handler) {
		return this.bind("result", handler);
	},
	search: function(handler) {
		return this.trigger("search", [handler]);
	},
	flushCache: function() {
		return this.trigger("flushCache");
	},
	setOptions: function(options){
		return this.trigger("setOptions", [options]);
	},
	unautocomplete: function() {
		return this.trigger("unautocomplete");
	}
});

$.Autocompleter = function(input, options) {

	var KEY = {
		UP: 38,
		DOWN: 40,
		DEL: 8,
		TAB: 9,
		RETURN: 13,
		ESC: 27,
		COMMA: 188,
		PAGEUP: 33,
		PAGEDOWN: 34
	};
	// Create $ object for input element
	var $input = $(input).attr("autocomplete", "off");
	
	$input.click(function (){$('.default').remove();$('#result_list').append('<div class="default">'+options.intro_text+'</div>');$('#result_list').css('display','');})
	$('body').click(function (){ $('#result_list ul').remove();});
	$('.facelist').click(function (){ $input.focus();});
	var timeout;
	var previousValue = "";
	var cache = $.Autocompleter.Cache(options);
	var hasFocus = 0;
	var lastKeyPressCode;
	var config = {
		mouseDownOnSelect: true
	};
	var select = $.Autocompleter.Select(options, input, selectCurrent, config);
	
	$input.before('<input type="hidden" name="'+options.result_field+'" id="'+options.result_field+'" value="">');

	
	$input.keydown(function(event) {
		// track last key pressed
		lastKeyPressCode = event.keyCode;
		switch(event.keyCode) {
		
			case KEY.UP:
				event.preventDefault();
				if ( select.visible() ) {
					select.prev();
				} else {
					onChange(0, true);
				}
				break;
				
			case KEY.DOWN:
				event.preventDefault();
				if ( select.visible() ) {
					select.next();
				} else {
					onChange(0, true);
				}
				break;
				
			case KEY.PAGEUP:
				event.preventDefault();
				if ( select.visible() ) {
					select.pageUp();
				} else {
					onChange(0, true);
				}
				break;
				
			case KEY.PAGEDOWN:
				event.preventDefault();
				if ( select.visible() ) {
					select.pageDown();
				} else {
					onChange(0, true);
				}
				break;
			
			// matches also semicolon
			case KEY.TAB:
			case KEY.RETURN:
				if( selectCurrent() ){
					//$input.blur(); // Removed so when you hit retun focus goes back to the input box.
					event.preventDefault();
				}
				break;
			case KEY.DEL:
				arr_user = $('#'+options.result_field).val().split(",");
				data_tag = new String(arr_user[arr_user.length - 2]);
				if($input.val() == "" && $('#bit-'+data_tag) && $('#bit-'+data_tag).attr('class') == "token token_selected")
				{
					Remove_tag_data(arr_user[arr_user.length - 2]);
				}
				else if($input.val() == "" && $('#bit-'+arr_user[arr_user.length - 2]) && $('#bit-'+arr_user[arr_user.length - 2]).attr('class') != "token token_selected")
				{
					$('#bit-'+data_tag).addClass('token_selected');
				}
				else if($input.val().length == 1)
				{
					$('#result_list ul').remove();
					$('#result_list div').remove();
					$('#result_list').append('<div class="default">'+options.intro_text+'</div>');
				}
				else
				{
					onChange(1, true);
				}
					
			break;	
			case KEY.ESC:
				select.hide();
				$('#result_list ul').remove();
				$input.val('');
				$input.blur();
				break;
				
			default:
				clearTimeout(timeout);
				timeout = setTimeout(onChange, options.delay);
				break;
		}
	}).keypress(function() {
		// having fun with opera - remove this binding and Opera submits the form when we select an entry via return
	}).focus(function(){
		// track whether the field has focus, we shouldn't process any
		// results if the field no longer has focus
		hasFocus++;
	}).blur(function() {
		hasFocus = 0;
	}).click(function() {
		// show select when clicking in a focused field
		if ( hasFocus++ > 1 && !select.visible() ) {
			onChange(0, true);
		}
	}).bind("search", function() {
		// TODO why not just specifying both arguments?
		var fn = (arguments.length > 1) ? arguments[1] : null;
		function findValueCallback(q, data) {
			var result;
			if( data && data.length ) {
				for (var i=0; i < data.length; i++) {
					if( data[i].result.toLowerCase() == q.toLowerCase() ) {
						result = data[i];
						break;
					}
				}
			}
			if( typeof fn == "function" ) fn(result);
			else $input.trigger("result", result && [result.data, result.value]);
		}
	}).bind("flushCache", function() {
		cache.flush();
	}).bind("setOptions", function() {
		$.extend(options, arguments[1]);
		// if we've updated the data, repopulate
		if ( "data" in arguments[1] )
			cache.populate();
	}).bind("unautocomplete", function() {
		select.unbind();
		$input.unbind();
	});
	function Remove_tag_data(data)
	{
		options.data += ','+data;
		options.data = new String(data).split(",").sort().toString();
		$('#bit-'+new String(data).replace(/ /g,'_')).remove();
		$('#'+options.result_field).val(new String($('#'+options.result_field).val().replace(data,"")));
		repairValueList();
		return false;
	}
	function MakeBox(data)
	{
		elemLI = $('<li id="bit-'+new String(data[1]).replace(/ /g,'_')+'" class="token"><span><span><span><span>'+data[0]+'</span></span></span></span></li>').click(function () {$('.token').removeClass('token_selected'); $(this).addClass("token_selected");},function () {$(this).removeClass("token_selected");});
		elemA = $('<span class="x"> .x</span>').click(function (){Remove_tag_data(data[1]); return false;});
		$(elemLI).append(elemA);
		$('#result_list ul').remove();
		$('.token-input').before(elemLI);
		$input.val('');
		$input.focus();
		$('#list_user').focus();
	}
	function selectCurrent() {
		var selected = select.selected();
		if( !selected )
			return false;
		
		if($('#result_list ul').get() != "" && $('#result_list ul#no_result').get() == "")
		{	
			RemoveData(selected.data);
			MakeBox(selected.data);
			return true;
		}
		else
			return false;
	}
	function RemoveData(data)
	{
		list_users = new String(options.data).replace(/ /g,'_');
		options.data = list_users.replace(data[1],"");
		$('#'+options.result_field).val( $('#'+options.result_field).val()+","+data[1]);
		repairValueList();
	};
	
	function repairValueList(){
		//Begin cleanup
		var result = $('#'+options.result_field).val();
		result = result.split(",");
		//Loop through the array and rebuild the string
		var i = 0;
		var str = "";
		for(i=0; i < result.length; i++){
			if(result[i] == ""){
				continue;
			}
			str = str + result[i];
			str = str + ",";				
		}
		
		$('#'+options.result_field).val(str);		
	};
	
	function onChange(crap, skipPrevCheck) {
		crap == 1?valor = $input.val().substring(0,($input.val().length-1)):valor = $input.val();
		request(valor.toLowerCase(), receiveData, noData);
	};
	
	function noData() {
		$('.default').remove();
		$('#result_list ul').remove()
		$('#result_list').append('<ul id="no_result"><li>'+options.no_result+'</li></ul>')
	};

	function receiveData(q, data) {
		if (q != "" && data && data.length && hasFocus ) {
			select.display(data, q);
			select.show();
		} else {
			$('#result_list ul').remove();
		}
	};

	function request(term, success, failure) {
		term = term.toLowerCase();
		var data = cache.load(term);
		//ORGINAL
		/*// recieve the cached data
		if (data && data.length) {
			success(term, data);
		} else {
			failure(term);
		}*/
		//ORIGINAL END
		$('#result_list').html('<div class="default">Searching...</div>');
		if(typeof options.url == 'string')
		{
			var sep = options.url.indexOf('?') == -1 ? '?' : '&'; 
			var url = options.url + sep + "q=" + encodeURI(term);
			
			$.get(
				url,
				function(data)
				{
					data = parse(data);
					cache.add(term, data);
					receiveData(term, data);
					if(data.length == 0){
							$('#result_list').html('<div class="default">No Results Found</div>');
					}
				}
				);		
		}
		else
		{
			var data = cache.load(term);
			// recieve the cached data
			if (data && data.length) {
				success(term, data);
			} else {
				failure(term);
			}
		}
		
	};
	
	function parse(data) {
		var parsed = [];
		var rows = data.split("\n");
		for (var i=0; i < rows.length; i++) {
			var row = $.trim(rows[i]);
			if (row) {
				row = row.split("|",2);
				parsed[parsed.length] = {
					data: row,
					value: row[0],
					result: options.formatResult && options.formatResult(row, row[0]) || row[0]
				};
			}
		}
		return parsed;
	};
};

$.Autocompleter.defaults = {
	minChars: 1,
	delay: 400,
	cacheLength: 1,
	max: 100,
	selectFirst: true,
	width: 0,
	highlight: function(value, term) {
		return value.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + term + ")(?![^<>]*>)(?![^&;]+;)", "gi"), "<em>$1</em>");
	},
   	intro_text: 'Type in the box',
	result_field: 'to_users',
	no_result: 'No user result'
};

$.Autocompleter.Cache = function(options) {

	var data = {};
	var length = 0;
	
	function matchSubset(s, sub) {
		var i = s.toLowerCase().indexOf(sub);
		if (i == -1) return false;
		return i == 0 || true;
	};
	
	function add(q, value) {
		if (length > options.cacheLength){
			flush();
		}
		if (!data[q]){ 
			length++;
		}
		data[q] = value;
	}
	
	function populate(){
		if( !options.data ) return false;
		// track the matches
		var stMatchSets = {},
			nullData = 0;

		// no url was specified, we need to adjust the cache length to make sure it fits the local data store
		if( !options.url ) options.cacheLength = 1;
		
		// track all options for minChars = 0
		stMatchSets[""] = [];
		
		// loop through the array and create a lookup structure
		for ( var i = 0, ol = options.data.length; i < ol; i++ ) {
			var rawValue = options.data[i];
			// if rawValue is a string, make an array otherwise just reference the array
			rawValue = (typeof rawValue == "string") ? [rawValue] : rawValue;
			var value = new String(rawValue);
			if ( value === false )
				continue;
				
			var firstChar = value.charAt(0).toLowerCase();
			// if no lookup array for this character exists, look it up now
			if( !stMatchSets[firstChar] ) 
				stMatchSets[firstChar] = [];

			// if the match is a string
			var row = {
				value: value,
				data: rawValue,
				result: options.formatResult && options.formatResult(rawValue) || value
			};
			
			// push the current match into the set list
			stMatchSets[firstChar].push(row);

			// keep track of minChars zero items
			if ( nullData++ < options.max ) {
				stMatchSets[""].push(row);
			}
		};

		// add the data items to the cache
		$.each(stMatchSets, function(i, value) {
			// increase the cache size
			options.cacheLength++;
			// add to the cache
			add(i, value);
		});
	}
	
	// populate any existing data
	setTimeout(populate, 25);
	
	function flush(){
		data = {};
		length = 0;
	}
	
	return {
		flush: flush,
		add: add,
		populate: populate,
		load: function(q) {
			if (!options.cacheLength || !length)
				return null;
			/* 
			 * if dealing w/local data and matchContains than we must make sure
			 * to loop through all the data collections looking for matches
			 */
				// track all matches
				var csub = [];
				// loop through all the data grids for matches
				for( var k in data ){
					// don't search through the stMatchSets[""] (minChars: 0) cache
					// this prevents duplicates
					if( k.length > 0 ){
						var c = data[k];
						$.each(c, function(i, x) {
							// if we've got a match, add it to the array
							if (matchSubset(x.value, q)) {
								csub.push(x);
							}
						});
					}
				}				
				return csub;
			
			return null;
		}
	};
};

$.Autocompleter.Select = function (options, input, select, config) {
	var CLASSES = {
		ACTIVE: "auto-focus"
	};
	
	var listItems,
		active = -1,
		data,
		term = "",
		element,
		list;
	
	// Create results
	function init() {
		element = $('#result_list');
		list = $('<ul>').appendTo(element).mouseover( function(event) {
			if(target(event).nodeName && target(event).nodeName.toUpperCase() == 'LI') {
	            active = $("li", list).removeClass().index(target(event));
			    $(target(event)).addClass(CLASSES.ACTIVE);            
	        }
		}).click(function(event) {
			$(target(event)).addClass(CLASSES.ACTIVE);
			select();
			input.focus();
			return false;
		}).mousedown(function() {
			config.mouseDownOnSelect = true;
		}).mouseup(function() {
			config.mouseDownOnSelect = false;
		});
		
		
		if( options.width > 0 )
			element.css("width", options.width);
			
	} 
	
	function target(event) {
		var element = event.target;
		while(element && element.tagName != "LI")
			element = element.parentNode;
		// more fun with IE, sometimes event.target is empty, just ignore it then
		if(!element)
			return [];
		return element;
	}

	function moveSelect(step) {
		listItems.slice(active, active + 1).removeClass();
		movePosition(step);
        var activeItem = listItems.slice(active, active + 1).addClass(CLASSES.ACTIVE);
        
	};
	
	function movePosition(step) {
		active += step;
		if (active < 0) {
			active = listItems.size() - 1;
		} else if (active >= listItems.size()) {
			active = 0;
		}
	}
	
	function limitNumberOfItems(available) {
		return options.max && options.max < available
			? options.max
			: available;
	}
	
	function fillList() {
		list.empty();
		$('.default').remove();
		var max = limitNumberOfItems(data.length);
		lista = new String(options.data);
		
		var current = new String($('#'+options.result_field).val()); //Do not allow duplicates by first getting the current list of numbers
		var results = false;
		
		var duplicates = $('#'+options.result_field).val();
		duplicates = duplicates.split(",");
		
		for (var i=0; i < max; i++) {
			//Check for duplicates - if the ID matches in any part of the data hidden field, then reject the result
			var found = false;
			var j=0;
			for(j=0; j < duplicates.length; j++){
				if(duplicates[j] == data[i].data[1]){
					found = true;
					j = duplicates.length;	
				}
			}

			if(found == true){
				continue;	
			}
			
			if (!data[i]){
				continue;
			}
			var formatted = new String(data[i].data[0]);
			if ( formatted === false ){
				continue;
			}
			var li = $("<li>").html( options.highlight(formatted, term) ).appendTo(list)[0];
			lista = new String(options.data);
			$.data(li, "ac_data", data[i]);
			results = true;
		
		}
		
		if(results == false){
			$('#result_list').html('<div class="default">No Results Found</div>');
		}
		
		listItems = list.find("li");
		if ( options.selectFirst ) {
			listItems.slice(0, 1).addClass(CLASSES.ACTIVE);
			active = 0;
		}
	}
	
	return {
		display: function(d, q) {
			$('#result_list ul').remove();
			init();
			data = d;
			term = q;
			fillList();
		},
		next: function() {
			moveSelect(1);
		},
		prev: function() {
			moveSelect(-1);
		},
		pageUp: function() {
			if (active != 0 && active - 8 < 0) {
				moveSelect( -active );
			} else {
				moveSelect(-8);
			}
		},
		pageDown: function() {
			if (active != listItems.size() - 1 && active + 8 > listItems.size()) {
				moveSelect( listItems.size() - 1 - active );
			} else {
				moveSelect(8);
			}
		},
		hide: function() {
			element && element.hide();
			active = -1;
		},
		visible : function() {
			return element && element.is(":visible");
		},
		current: function() {
			return this.visible() && (listItems.filter("." + CLASSES.ACTIVE)[0] || options.selectFirst && listItems[0]);
		},
		show: function() {
			var offset = $(input).offset();
			element.css({
				//width: typeof options.width == "string" || options.width > 0 ? options.width : $(input).width(),
			//	top: offset.top + input.offsetHeight,
			//	left: offset.left
			}).show();
            
		},
		selected: function() {
			return listItems && $.data(listItems.filter("." + CLASSES.ACTIVE)[0], "ac_data");
		},
		unbind: function() {
			element && element.remove();
		}
	};
};


$.Autocompleter.Selection = function(field, start, end) {
	if( field.createTextRange ){
		var selRange = field.createTextRange();
		selRange.collapse(true);
		selRange.moveStart("character", start);
		selRange.moveEnd("character", end);
		selRange.select();
	} else if( field.setSelectionRange ){
		field.setSelectionRange(start, end);
	} else {
		if( field.selectionStart ){
			field.selectionStart = start;
			field.selectionEnd = end;
		}
	}
	field.focus();
};

})(jQuery);