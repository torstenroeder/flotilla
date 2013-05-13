// JavaScript Document
function openSubtable (TableName) {
  Subtable = window.open("flotilla/popups/subtable.php?table="+TableName, "Auswahlliste", "width=300,height=150,left=300,top=150,dependent=yes,location=no");
  Subtable.focus();  
}

function putSelection (SubtableName) {
  opener.document.getElementById(SubtableName).value=document.getElementById("FlotillaSubtable").options[document.getElementById("FlotillaSubtable").selectedIndex].value;
  self.close();
}
