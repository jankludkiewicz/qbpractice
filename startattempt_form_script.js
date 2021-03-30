var noofquestions = 0;

var formElements = document.querySelectorAll("input[type='checkbox'][name^='subcategories']");

console.log(formElements);
for (var i=0; i<formElements.length; i++) {
	formElements[i].addEventListener("change", function() {eventListener(this)}, false);
	questionElement = document.querySelector("input[name='"+formElements[i].name+"_noofquestions']");
	noofquestions += parseInt(questionElement.value);
}

function eventListener(a) {
	console.log(a.checked);
	questionElement = document.querySelector("input[name='"+a.name+"_noofquestions']");
	if (a.checked) noofquestions += parseInt(questionElement.value);
	else noofquestions -= parseInt(questionElement.value);
	updateRangeInfo();
}

function updateRangeInfo() {
	var rangeElement = document.getElementById('questionsno');
	document.getElementById('questionsnodisplay').innerHTML = rangeElement.value+" / "+noofquestions;
}