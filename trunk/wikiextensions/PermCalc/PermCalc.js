function Oct(r,w,x){
        i = 0;
        if(r){ i+=4 };
        if(w){ i+=2 };
        if(x){ i+=1 };
        return i;
}
 
function rwx(r,w,x){
        s = "";
        if(r){ s+="r" }else{ s+="-" };
        if(w){ s+="w" }else{ s+="-" };
        if(x){ s+="x" }else{ s+="-" };
        return s;
}
 
function check(){
	field_rwx = document.getElementById('rwx');
	field_rwx.innerHTML=
		rwx(document.chmod.read.checked, document.chmod.read2.checked, document.chmod.read3.checked)+
		rwx(document.chmod.write.checked, document.chmod.write2.checked, document.chmod.write3.checked)+
		rwx(document.chmod.execute.checked, document.chmod.execute2.checked, document.chmod.execute3.checked);

	field_oktal = document.getElementById('oktal');
	field_oktal.innerHTML = '' +
		Oct(document.chmod.read.checked, document.chmod.read2.checked, document.chmod.read3.checked)+
		Oct(document.chmod.write.checked, document.chmod.write2.checked, document.chmod.write3.checked)+
		Oct(document.chmod.execute.checked, document.chmod.execute2.checked, document.chmod.execute3.checked);
}

function drawForm()
{
html = '<form name="chmod">';
html += '<table cellspacing="0" cellpadding="3" border="0">';
html += '  <tr>';
html += '    <td><b>Owner (Besitzer)</b></td>';
html += '    <td><b>Group (Gruppe)</b></td>';
html += '    <td><b>Other (Andere)</b></td>';
html += '  </tr>';
html += '  <tr>';
html += '    <td colspan="3"></td>';
html += '  </tr>';
html += '  <tr>';
html += '    <td onclick="check();"><input type="checkbox" name="read" id="read" onclick="check()" checked="checked" /> <label for="read" class="l">Lesen</label></td>';
html += '    <td onclick="check();"><input type="checkbox" name="write" id="write" checked="checked" /> <label for="write" class="l">Lesen</label></td>';
html += '    <td onclick="check();"><input type="checkbox" name="execute" id="execute" checked="checked" /> <label for="execute" class="l">Lesen</label></td>';
html += '  </tr>';
html += '  <tr>';
html += '    <td onclick="check();"><input type="checkbox" name="read2" id="read2" checked="checked" /> <label for="read2" class="l">Schreiben</label></td>';
html += '    <td onclick="check();"><input type="checkbox" name="write2" id="write2" /> <label for="write2" class="l">Schreiben</label></td>';
html += '    <td onclick="check();"><input type="checkbox" name="execute2" id="execute2" /> <label for="execute2" class="l">Schreiben</label></td>';
html += '  </tr>';
html += '  <tr>';
html += '    <td onclick="check();"><input type="checkbox" name="read3" id="read3" /> <label for="read3" class="l">Ausf&uuml;hren</label></td>';
html += '    <td onclick="check();"><input type="checkbox" name="write3" id="write3" /> <label for="write3" class="l">Ausf&uuml;hren</label></td>';
html += '    <td onclick="check();"><input type="checkbox" name="execute3" id="execute3" /> <label for="execute3" class="l">Ausf&uuml;hren</label></td>';
html += '  </tr>';
html += '  <tr>';
html += '    <td colspan="3"></td>';
html += '  </tr>';
html += '  <tr>';
html += '    <td><div id="rwx">rw-r--r--</div>';
html += '    <td align="right">Oktalschreibweise:</td><td><div id="oktal">644</div></td>';
html += '  </tr>';
html += '  <tr>';
html += '    <td colspan="3">r=read (Lesen), w=write (Schreiben), x=execute (Ausf&uuml;hren)</td>';
html += '  </tr>';
html += '</table>';
html += '</form>';

el = document.getElementById('permcalc');
el.innerHTML = html;

return;
}