let alert = document.querySelector(".alert");

let alertCloseButton = document.querySelector(".alert-close-button");
alertCloseButton.addEventListener("click", closeAlert, true);

function closeAlert() {
    alert.classList.remove("show");
}

let modalConfirmButtons = document.querySelectorAll(".modal-confirm-button");
for(i = 0; i < modalConfirmButtons.length; i++) { 
    addConfirmOnClick(modalConfirmButtons[i]);
}

function addConfirmOnClick(confirmButton) {
    confirmButton.addEventListener("click", confirm, true);
}

function confirm($event) {
    let xhr = new XMLHttpRequest();
    let _token = document.querySelector("meta[name='csrf-token']").getAttribute("content");
    let confirmButton = $event.currentTarget;
    let params = getConfirmParams(confirmButton);
    xhr.open("POST", "/admin", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("X-CSRF-TOKEN", _token);
    xhr.onreadystatechange = function() {
        if(xhr.readyState == 4 && xhr.status == 200) {
            updateDataStatus(confirmButton);
            deselect();
            alert.classList.add("show");
        }
    }
    xhr.send(params);
}

function updateDataStatus(confirmButton) {
    let actionType = confirmButton.getAttribute("data-action");
    let selected = document.querySelectorAll(".selected");
    for (let i = 0; i < selected.length; i++) {
        selected[i].setAttribute("data-status", actionType);
    }
}

function deselect() {
    let selected = document.querySelectorAll(".selected");
    for (let i = 0; i < selected.length; i++) {
        selected[i].classList.remove("selected");
    }
}

function getConfirmParams(confirmButton) {
    let actionType = confirmButton.getAttribute("data-action");
    let urls = getUrls();
    return "actionType=" + actionType + "&urls=" + JSON.stringify(urls);
}

function getUrls() {
    let urls = [];
    let selected = document.querySelectorAll(".selected");
    for(i = 0; i < selected.length; i++) { 
        let url = selected[i].getAttribute("data-url");
        urls.push(url);
    }
    return urls;
}

let modalCloseButtons = document.querySelectorAll(".modal-close-button");

for(i = 0; i < modalCloseButtons.length; i++) { 
    addDeselectOnClick(modalCloseButtons[i]);
}

function addDeselectOnClick(button) {
    button.addEventListener("click", deselect, true);
}

let galleryItems = document.querySelectorAll(".gallery-item");

for(i = 0; i < galleryItems.length; i++) { 
    addSelectOnClick(galleryItems[i]);
}

function addSelectOnClick(galleryItem) {
    galleryItem.addEventListener("click", select, true);
}

function select($event) {
    let galleryItem = $event.currentTarget;
    if (galleryItem.classList.contains("selected")) {
        galleryItem.classList.remove("selected");
    } else {
        galleryItem.classList.add("selected");
    }
}
