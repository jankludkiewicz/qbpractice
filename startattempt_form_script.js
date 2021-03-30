var formElements = document.querySelectorAll("input[name^='subcategories']");
for (var i=1; i<formElements.length; i+=2) {
	formElements[i].addEventListener("change", function() {eventListener(i+"")}, false);
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