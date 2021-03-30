var formElements = document.querySelectorAll("input[name^='subcategories']");
var i;
for (i=0; i<formElements.length; i++) {
	formElements[i].addEventListener("change",eventListener(i));
}

function eventListener(var a) {
	console.log(a);
}

function onRangeMouseUp() {
	var rangeElement = document.getElementById('questionsno');
	document.getElementById('questionsnodisplay').innerHTML = rangeElement.value;
	var formelements = document.querySelectorAll("input[name^='subcategories']")
	console.log(formelements);
}