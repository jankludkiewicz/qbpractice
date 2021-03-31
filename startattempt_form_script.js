// Define global variables
var allquestions = 0;
var flaggedquestions = 0;
var unseenquestions = 0;
var incorrectquestions = 0;

// Initialize form
initTabs();
initQuestionCategorySelectors();
initRange();

// Function definitions

/*
 * Updates numbers of questions after checking category selector
 */
function updateQuestionNumbers(input) {
	var questionElement = document.querySelector("input[name='"+input.name+"_noofquestions']");
	if (input.checked) allquestions += parseInt(questionElement.value);
	else allquestions -= parseInt(questionElement.value);
	
	updateRange();
}

/*
 * Updates tabs after user selection
 */
function updateTabs(input) {
	
	var selectedLabel;
	switch(parseInt(input.value)) {
		case 0:
		selectedLabel = document.getElementById('allquestions');
		break;
		case 1:
		selectedLabel = document.getElementById('flagged');
		break;
		case 2:
		selectedLabel = document.getElementById('unseen');
		break;
		case 3:
		selectedLabel = document.getElementById('incorrect');
		break;
		case 4:
		selectedLabel = document.getElementById('exam');
		break;
	}
	
	//Clear all labels
	labels = document.querySelectorAll("label[class='studypreference-label']");
	for (var i=0; i<labels.length; i++) labels[i].style = "box-shadow: none";
	
	//Select label
	selectedLabel.style = "box-shadow: 0 3px 0 -1px #fff, inset 0 5px 0 -1px #13cd4a;";
}

/*
 * Updates question number range selector
 */
function updateRange() {
	var rangeElement = document.getElementById('questionsno');
	if (allquestions == 0) {
		rangeElement.min = 0;
		document.getElementById('id_submitbutton').disabled = true;
	}
	else {
		rangeElement.min = 1;
		document.getElementById('id_submitbutton').disabled = false;
	}
	rangeElement.max = allquestions;
	if (rangeElement.value > allquestions) rangeElement.value = allquestions;
	document.getElementById('questionsnodisplay').innerHTML = rangeElement.value+" / "+allquestions;
}

/*
 * Initializes tabs: 
 * 1) adds eventListeners (change)
 * 2) marks first element
 */
function initTabs() {
	var radioTabInputs = document.querySelectorAll("input[name='studypreference']");
	
	for (var i = 0; i < radioTabInputs.length; i++) radioTabInputs[i].addEventListener("change", function() {updateTabs(this)}, false);
	
	// Check first element
	radioTabInputs[0].checked = true;
	updateTabs(radioTabInputs[0]);
}

/*
 * Initializes question category selectors:
 * 1) adds eventListeners (change)
 * 2) marks all checked
 * 3) calculates total question numbers of each type
 */
function initQuestionCategorySelectors() {
	var formElements = document.querySelectorAll("input[type='checkbox'][name^='subcategories']");

console.log(formElements);
	for (var i = 0; i < formElements.length; i++) {
		formElements[i].addEventListener("change", function() {updateQuestionNumbers(this)}, false);
		formElements[i].checked = true;
	
		allquestionElement = document.querySelector("input[name='"+formElements[i].name+"_allquestions']");
		allquestions += parseInt(allquestionElement.value);
	
/*		flaggedquestionElement = document.querySelector("input[name='"+formElements[i].name+"_flaggedquestions']");
		flaggedquestions += parseInt(flaggedquestionElement.value);
	
		unseenquestionElement = document.querySelector("input[name='"+formElements[i].name+"_unseenquestions']");
		unseenquestions += parseInt(unseenquestionElement.value);
	
		incorrectquestionElement = document.querySelector("input[name='"+formElements[i].name+"_incorrectquestions']");
		incorrectquestions += parseInt(incorrectquestionElement.value);*/
	}
}

/*
 * Initializes question number range selector
 * 1) adjusts min, max and value of a range according to logic
 * 2) diables submit button when questionnumber = 0
 */
function initRange() {
	var rangeElement = document.getElementById('questionsno');
	if (allquestions == 0) {
		rangeElement.min = 0;
		document.getElementById('id_submitbutton').disabled = true;
	}
	else {
		rangeElement.min = 1;
		document.getElementById('id_submitbutton').disabled = false;
	}
	rangeElement.max = allquestions;
	rangeElement.value = Math.round(allquestions/2);
	console.log(rangeElement.value);
	document.getElementById('questionsnodisplay').innerHTML = rangeElement.value+" / "+allquestions;
}