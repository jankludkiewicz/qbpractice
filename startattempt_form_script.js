var formElements = document.querySelectorAll("input[name^='subcategories']");
var i;
for (i=1; i<formElements.length; i+=2) {
	formElements[i].addEventListener("change", function() {eventListener(formElements[i].checked)}, false);
	console.log(i);
}

function eventListener(a) {
	console.log(a);
}

function onRangeMouseUp() {
	var rangeElement = document.getElementById('questionsno');
	document.getElementById('questionsnodisplay').innerHTML = rangeElement.value;
	var formelements = document.querySelectorAll("input[name^='subcategories']")
	console.log(formelements);
}