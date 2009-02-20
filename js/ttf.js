function displayRow(which){
	if (!document.getElementById)
		return
	if (which.style.display=="")
		which.style.display="none"
	else
		which.style.display=""
}