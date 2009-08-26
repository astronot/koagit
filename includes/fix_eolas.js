// (c) David Grudl aka -dgx-
//
// version 2
// more info: http://www.dgx.cz/knowhow/eolas-workaround/


var objects = document.getElementsByTagName("object");

function eolas(i)
{
    objects[i].outerHTML = objects[i].outerHTML;
}

for (var i=0; i<objects.length; i++)
    window.setTimeout("eolas(" + i + ")", 1);
