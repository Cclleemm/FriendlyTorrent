var seedbox = new Object;
seedbox.interval = null;
CTRL = false;

$(function()
{
	
	// tooltips
	$('[data-toggle="tooltip"]').tooltip();
	
	$('.popover-btn').popover();
	
	
	var ctrlDown = false;
    var ctrlKey = 17, aKey = 65;

    $(document).keydown(function(e)
    {
        if (e.keyCode == ctrlKey) ctrlDown = true;
    }).keyup(function(e)
    {
        if (e.keyCode == ctrlKey) ctrlDown = false;
    });
    
    
    $('body').click(function() {
		$('#context').hide();
	});
	
	$('#context').click(function(event){
	    event.stopPropagation();
	});
	
	//$("#ajaxContent").css('min-height', $(window).height()+'px');
});

	function loadUpload(){
		$('#file_upload').uploadify({
			'swf'      : adresse+'/uploadify/uploadify.swf',
			'uploader' : adresse+'/action/addTorrent/',
			'buttonClass' : 'btn btn-success btn-sm',
			'buttonText' : '<i class="glyphicon glyphicon-plus"></i> Ajouter',
			'height'    : 27,
			'queueID'  : 'queue',
			'itemTemplate' : '<div id="${fileID}" style="margin-bottom:2px;" class="widget">\
								<div class="widget-head"><span style="padding:5px;" class="fileName"><i class="glyphicon glyphicon-plus"></i> ${fileName}</span></div>\
							</div>',
			'onUploadSuccess' : function(file, data, response) {
		        evalscript(data);
		    }
		});
	}