var noofquestions = 0;

var formElements = document.querySelectorAll("input[type='checkbox'][name^='subcategories']");

console.log(formElements);
for (var i=0; i<formElements.length; i++) {
	formElements[i].addEventListener("change", function() {eventListener(this)}, false);
	questionElement = document.querySelector("input[name='"+formElements[i].name+"_noofquestions']");
}

function eventListener(a) {
	console.log(a.checked);
}

function onRangeMouseUp() {
	var rangeElement = document.getElementById('questionsno');
	document.getElementById('questionsnodisplay').innerHTML = rangeElement.value;
	var formelements = document.querySelectorAll("input[name^='subcategories']")
	console.log(formelements);
}