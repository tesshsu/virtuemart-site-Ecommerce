jQuery.noConflict();
function sameSizeImages(obj,nbr){
	var images = new Array();
	var curparent = jQuery(obj).parents('.nivo-bloc-images');
	curparent.find('img').each(function(){
		images.push(jQuery(this));
		jQuery(this).remove();
	});

	var nImages = shuffle(images);

	for(var i=0; i < nImages.length; i++){
		if(i < nbr){
			curparent.append(nImages[i]);
		}
	}
}

function bigSizeImages(obj,nbr){
	var images = new Array();
	var curparent = jQuery(obj).parents('.nivo-bloc-images-sub');
	curparent.find('img').each(function(){
		images.push(jQuery(this));
		jQuery(this).remove();
	});

	var nImages = shuffle(images);

	for(var i=0; i < nImages.length; i++){
		if(i < nbr){
			curparent.append(nImages[i]);
		}
	}
}


function shuffle(o){ //v1.0
    for(var j, x, i = o.length; i; j = Math.floor(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
    return o;
}