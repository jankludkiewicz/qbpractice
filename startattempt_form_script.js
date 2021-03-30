var formElements = document.querySelectorAll("input[name^='subcategories']");
var i;
for (i=0; i<formElements.length; i++) {
	formElements[i].addEventListener("change", function() {
		console.log(i);
});
}

function onRangeMouseUp() {
	var rangeElement = document.getElementById('questionsno');
	document.getElementById('questionsnodisplay').innerHTML = rangeElement.value;
	var formelements = document.querySelectorAll("input[name^='subcategories']")
	console.log(formelements);
}