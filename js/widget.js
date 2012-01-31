function centerSquareThumbs(container, size) {
  var widget = document.getElementById(container);
  var images = widget.getElementsByTagName('img');
  for(var i=0; i < images.length; i++) {
    var left = ((images[i].width - size) / 2) * -1;
    var top = ((images[i].height - size) / 2) * -1;
    images[i].style.marginLeft = left+'px';
    images[i].style.marginTop  = top+'px';
  }
}