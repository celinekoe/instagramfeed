let appUrl = "http://192.168.1.43/";
// let appUrl = "/server/public";

/*
 * Alert
 */

let alert = document.querySelector(".alert");

let alertCloseButton = document.querySelector(".alert-close-button");
alertCloseButton.addEventListener("click", closeAlert);

function closeAlert() {
    alert.classList.remove("show");
}

/*
 * Action
 */

let modalConfirmButtons = document.querySelectorAll(".modal-confirm-button");
for(i = 0; i < modalConfirmButtons.length; i++) { 
    addConfirmOnClick(modalConfirmButtons[i]);
}

function addConfirmOnClick(confirmButton) {
    confirmButton.addEventListener("click", confirm);
}

let _token = document.querySelector("meta[name='csrf-token']").getAttribute("content");

function confirm($event) {
    let confirmButton = $event.currentTarget;
    let params = getConfirmParams(confirmButton);
    post(appUrl + "/admin", params)
    .then(() => {
        updateDataStatus(confirmButton);
        deselect();
        alert.classList.add("show");
    })
    .catch(() => {
        console.error("err");
    });
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
    return "action_type=" + actionType + "&urls=" + JSON.stringify(urls);
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
    button.addEventListener("click", deselect);
}

let galleryItems = document.querySelectorAll(".gallery-item");

for(i = 0; i < galleryItems.length; i++) { 
    addSelectOnClick(galleryItems[i]);
}

function addSelectOnClick(galleryItem) {
    galleryItem.addEventListener("click", select);
}

function select($event) {
    let galleryItem = $event.currentTarget;
    if (galleryItem.classList.contains("selected")) {
        galleryItem.classList.remove("selected");
    } else {
        galleryItem.classList.add("selected");
    }
}

/*
 * Scroll to load
 */

document.addEventListener('scroll', onScroll);
let polling = false;

function onScroll() {
    let distToBottom = getDistToBottom();
    if (!polling && distToBottom < 400) {
        let nextUrl = document.querySelector(".loading").getAttribute("data-next-url");
        if (nextUrl !== "") {
            polling = true;
            let params = getParams();
            get(appUrl + "/admin/more", params)
            .then(response => {
                polling = false;
                updateNextUrl(response.next_url);
                addGalleryItems(response.media_array);
            })
            .catch(() => {
                polling = false;
                console.error("err");
            });
        } else {
            let loadingText = document.querySelector(".loading").querySelector("p");
            loadingText.innerHTML = "No more content to load";
        }
    }
}

function getDistToBottom () {
    let scrollPosition = window.pageYOffset;
    let windowSize = window.innerHeight;
    let bodyHeight = document.body.offsetHeight;
    return Math.max(bodyHeight - (scrollPosition + windowSize), 0);  
}

function getParams() {
    let nextUrl = document.querySelector(".loading").getAttribute("data-next-url");
    let baseUrl = nextUrl.split("?")[0];
    let stringParams = nextUrl.split("?").pop().split("&");
    let accessToken = stringParams[0].split("=").pop();
    let count = stringParams[1].split("=").pop(); 
    let maxTagId = stringParams[2].split("=").pop(); 
    return "?base_url=" + baseUrl + "&access_token=" + accessToken + "&count=" + count + "&max_tag_id=" + maxTagId;
}

function updateNextUrl(nextUrl) {
    let loading = document.querySelector(".loading");
    loading.setAttribute("data-next-url", nextUrl);
}

let gallery = document.querySelector(".gallery");

function addGalleryItems(mediaArray) {
    for (let i = 0; i < mediaArray.length; i++) {
        addGalleryItem(mediaArray[i]);
    }
}

function addGalleryItem(media) {
    let galleryItem = document.createElement("div");
    galleryItem.classList.add("gallery-item");
    galleryItem.setAttribute("data-url", media.url);
    galleryItem.setAttribute("data-status", media.status);
    galleryItem.addEventListener("click", select);
    gallery.appendChild(galleryItem);

    if (media.type === "video") {
        addVideo(galleryItem, media);
    } else if (media.type === "image") {
        addImage(galleryItem, media);
    }
    addOverlays(galleryItem);
}

function addVideo(galleryItem, media) {
    let video = document.createElement("video");
    video.setAttribute("controls", true);
    galleryItem.appendChild(video);

    let source = document.createElement("source");
    source.setAttribute("src", media.url);
    source.setAttribute("type", "video/mp4");
    video.appendChild(source);
}

function addImage(galleryItem, media) {
    let image = document.createElement("img");
    image.setAttribute("src", media.url);
    galleryItem.appendChild(image);
}

function addOverlays(galleryItem) {
    let selectedOverlay = document.createElement("div");
    selectedOverlay.classList.add("gallery-item-select-overlay");
    galleryItem.appendChild(selectedOverlay);

    let rejectOverlay = document.createElement("div");
    rejectOverlay.classList.add("gallery-item-reject-overlay");
    galleryItem.appendChild(rejectOverlay);
}

/*
 * GET and POST
 */

function get(url, params) {
    return new Promise((resolve, reject) => {
        let xhr = new XMLHttpRequest();
        xhr.open("GET", url + params);
        xhr.setRequestHeader("X-CSRF-TOKEN", _token);
        xhr.onload = () => {
            if (xhr.status === 200) {
                let response = JSON.parse(xhr.responseText)
                resolve(response);
            } else {
                reject();
            }
        }
        xhr.send();
    });
}

function post(url, params) {
    return new Promise((resolve, reject) => {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", url);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.setRequestHeader("X-CSRF-TOKEN", _token);
        xhr.onload = () => {
            if(xhr.status === 200) {
                resolve();
            } else {
                reject();
            }
        }
        xhr.send(params);
    });
}