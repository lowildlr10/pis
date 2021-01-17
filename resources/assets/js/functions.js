/*
http://isohunt.com Interface Javascript
by Gary Fung - email: gary{REPLACE_WITH_THE_AT_SIGN}isohunt.com

Feel free to use / mod this to your heart's content,
but keep these lines to acknowledge where this code originated.
Comments, mods or additions you'd like add to this script can be posted here:
http://isohunt.com/forum/viewforum.php?f=1

Tip popup functions adapted from AWStats: http://awstats.sourceforge.net/
*/
var smooth_timer;
function go2(selID, link) {
  var selOBJ = (document.getElementById) ? document.getElementById(selID) : eval("document.all['" + selID + "']");
  window.location=link + selOBJ.options[selOBJ.selectedIndex].value;
}
function toclip(string) { window.clipboardData.setData('Text', string); }
function ShowTip(fArg) {
  var tooltipOBJ = (document.getElementById) ? document.getElementById('ih' + fArg) : eval("document.all['ih" + fArg + "']");
  if (tooltipOBJ != null) {
    var tooltipLft = (document.body.offsetWidth?document.body.offsetWidth:document.body.style.pixelWidth) - (tooltipOBJ.offsetWidth?tooltipOBJ.offsetWidth:(tooltipOBJ.style.pixelWidth?tooltipOBJ.style.pixelWidth:$TOOLTIPWIDTH)) - 5;
    var tooltipTop = 10;
    if (navigator.appName == 'Netscape') {
      if (parseFloat(navigator.appVersion) >= 5) tooltipTop = (document.body.scrollTop>=0?document.body.scrollTop+10:event.clientY+10);
      tooltipOBJ.style.left = tooltipLft; tooltipOBJ.style.top = tooltipTop;
    }
    else {
      tooltipLft -= 30;
      tooltipTop = (document.body.scrollTop?document.body.scrollTop:document.body.offsetTop) + event.clientY - (tooltipOBJ.scrollHeight?tooltipOBJ.scrollHeight:tooltipOBJ.style.pixelHeight) - 30;
      if (tooltipTop < (document.body.scrollTop?document.body.scrollTop:document.body.offsetTop) + 10) {
        if (event.clientX > tooltipLft) tooltipTop = (document.body.scrollTop?document.body.scrollTop:document.body.offsetTop) + event.clientY + 30;
        else tooltipTop = (document.body.scrollTop?document.body.scrollTop:document.body.offsetTop) + 10;
      } 
      tooltipOBJ.style.pixelLeft = tooltipLft; tooltipOBJ.style.pixelTop = tooltipTop;
    }
    tooltipOBJ.style.visibility = "visible";
  }
}
function HideTip(fArg) {
  var tooltipOBJ = (document.getElementById) ? document.getElementById('ih' + fArg) : eval("document.all['ih" + fArg + "']");
  if (tooltipOBJ != null) tooltipOBJ.style.visibility = "hidden";
}
function smoothHeight(id, curH, targetH, stepH, mode) {
  diff = targetH - curH;
  if (diff != 0) {
    newH = (diff > 0) ? curH + stepH : curH - stepH;
    ((document.getElementById) ? document.getElementById(id) : eval("document.all['" + id + "']")).style.height = newH + "px";
    if (smooth_timer) window.clearTimeout(smooth_timer);
    smooth_timer = window.setTimeout( "smoothHeight('" + id + "'," + newH + "," + targetH + "," + stepH + ",'" + mode + "')", 16 );
  }
  else if (mode != "o") ((document.getElementById) ? document.getElementById(mode) : eval("document.all['" + mode + "']")).style.display="none";
}
function rowOver(i, nColor) {
  if (!nColor) nColor = "#ECECD9";
  var nameObj = (document.getElementById) ? document.getElementById('name' + i) : eval("document.all['name" + i + "']");
  if (nameObj != null) nameObj.style.background=nColor;
}
function rowOut(i, nColor) {
  var trObj = (document.getElementById) ? document.getElementById('ihtr' + i) : eval("document.all['ihtr" + i + "']");
  var nameObj = (document.getElementById) ? document.getElementById('name' + i) : eval("document.all['name" + i + "']");
  if (trObj == null || trObj.style.display=="none") nameObj.style.background=nColor;
}
function servOC(i, href, nColor) {
  var trObj = (document.getElementById) ? document.getElementById('ihtr' + i) : eval("document.all['ihtr" + i + "']");
  var nameObj = (document.getElementById) ? document.getElementById('name' + i) : eval("document.all['name" + i + "']");
  var ifObj = (document.getElementById) ? document.getElementById('ihif' + i) : eval("document.all['ihif" + i + "']");
  if (trObj != null) {
    if (trObj.style.display=="none") {
      ifObj.style.height = "0px";
      trObj.style.display="";
      nameObj.style.background=""; //row color;
      if (!ifObj.src) ifObj.src = href;
      smoothHeight('ihif' + i, 0, 210, 42, 'o');
    }
    else {
      nameObj.style.background=nColor;
      smoothHeight('ihif' + i, 210, 0, 42, 'ihtr' + i);
    }
  }
}
function trOC(idHL, idOC, idArrow) {
  var trObj = (document.getElementById) ? document.getElementById(idOC) : eval("document.all['" + idOC + "']");
  var hlObj = (document.getElementById) ? document.getElementById(idHL) : eval("document.all['" + idHL + "']");
  var arrowObj = (document.getElementById) ? document.getElementById(idArrow) : eval("document.all['" + idArrow + "']");
  if (trObj != null && hlObj != null) {
    if (trObj.style.display=="none") {
      trObj.style.display="";
      hlObj.style.background="#003366";
      hlObj.style.color="#EDF6F9";
      arrowObj.innerHTML="v";
    }
    else {
      trObj.style.display="none";
      hlObj.style.background="#ECECD9";
      hlObj.style.color="#000000";
      arrowObj.innerHTML="^";
    }
  }
}
function firstFocus()
{
   if (document.forms.length > 0)
   {
      var TForm = document.forms[0];
      for (i=0;i<TForm.length;i++)
      {
         if ((TForm.elements[i].type=="text")||
           (TForm.elements[i].type=="textarea")||
           (TForm.elements[i].type.toString().charAt(0)=="s"))
         {
            document.forms[0].elements[i].focus();
            break;
         }
      }
   }
}
function HighlightAll(theField) {
  var tempval=eval("document."+theField);
  tempval.focus();
  tempval.select();
  if (document.all) {
    therange=tempval.createTextRange();
    therange.execCommand("Copy");
    window.status="Contents highlighted and copied to clipboard!";
    setTimeout("window.status=''",5000);
  }
}
function NewWindow(mypage, myname, w, h, scroll) {
  var winl = (screen.width - w) / 2;
  var wint = (screen.height - h) / 2;
  winprops = 'height='+h+',width='+w+',top='+wint+',left='+winl+',scrollbars='+scroll+',resizable'
  win = window.open(mypage, myname, winprops)
  if (parseInt(navigator.appVersion) >= 4) { win.window.focus(); }
}
startList = function() {
  if (document.all&&document.getElementById) {
    navRoot = document.getElementById("nav");
    for (i=0; i<navRoot.childNodes.length; i++) {
      node = navRoot.childNodes[i];
      if (node.nodeName=="LI") {
        node.onmouseover=function() {
          this.className+=" navOver";
        }
        node.onmouseout=function() {
          this.className=this.className.replace(" navOver", "");
        }
      }
    }
  }
}
function setImageDimensions(gotImage) {
  if(gotImage.width > 600) gotImage.width = 600;
}
function changeImageDimensions(gotImage, type) {
  if(gotImage.width > 600 && type == 'out') {
	  gotImage.width = 600;
    return;
  }
  if(type == 'over') gotImage.removeAttribute('width');
}
