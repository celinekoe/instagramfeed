/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 39);
/******/ })
/************************************************************************/
/******/ ({

/***/ 39:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(40);


/***/ }),

/***/ 40:
/***/ (function(module, exports) {

var appUrl = "http://127.0.0.1:8000";
// let appUrl = "http://192.168.1.43/";
// let appUrl = "/server/public";

var _token = document.querySelector("meta[name='csrf-token']").getAttribute("content");

/*
 * Alert
 */

var alertPopup = document.querySelector(".alert");

var alertCloseButton = document.querySelector(".alert-close-button");
alertCloseButton.addEventListener("click", closeAlert);

function closeAlert() {
    alertPopup.classList.remove("show");
}

/*
 * Refresh
 */

var refreshButton = document.querySelector("#refresh-button");
addRefreshOnClick(refreshButton);
function addRefreshOnClick(refreshButton) {
    refreshButton.addEventListener("click", refresh);
}

function refresh($event) {
    var refreshButton = $event.currentTarget;
    refreshButton.disabled = true;
    refreshButton.innerHTML = "Loading...";
    get(appUrl + "/admin/refresh", "").then(function (response) {
        refreshButton.disabled = false;
        refreshButton.innerHTML = "Refresh";
        location.reload();
    }).catch(function () {
        console.error("err");
    });
}

/*
 * Action
 */

var modalConfirmButtons = document.querySelectorAll(".modal-confirm-button");
for (i = 0; i < modalConfirmButtons.length; i++) {
    addConfirmOnClick(modalConfirmButtons[i]);
}

function addConfirmOnClick(confirmButton) {
    confirmButton.addEventListener("click", confirm);
}

function confirm($event) {
    var confirmButton = $event.currentTarget;
    var params = getConfirmParams(confirmButton);
    post(appUrl + "/admin", params).then(function () {
        updateDataStatus(confirmButton);
        deselect();
        alert.classList.add("show");
    }).catch(function () {
        console.error("err");
    });
}

function updateDataStatus(confirmButton) {
    var actionType = confirmButton.getAttribute("data-action");
    var selected = document.querySelectorAll(".selected");
    for (var _i = 0; _i < selected.length; _i++) {
        selected[_i].setAttribute("data-status", actionType);
    }
}

function deselect() {
    var selected = document.querySelectorAll(".selected");
    for (var _i2 = 0; _i2 < selected.length; _i2++) {
        selected[_i2].classList.remove("selected");
    }
}

function getConfirmParams(confirmButton) {
    var actionType = confirmButton.getAttribute("data-action");
    var urls = getUrls();
    return "action_type=" + actionType + "&urls=" + JSON.stringify(urls);
}

function getUrls() {
    var urls = [];
    var selected = document.querySelectorAll(".selected");
    for (i = 0; i < selected.length; i++) {
        var url = selected[i].getAttribute("data-url");
        urls.push(url);
    }
    return urls;
}

var modalCloseButtons = document.querySelectorAll(".modal-close-button");

for (i = 0; i < modalCloseButtons.length; i++) {
    addDeselectOnClick(modalCloseButtons[i]);
}

function addDeselectOnClick(button) {
    button.addEventListener("click", deselect);
}

var galleryItems = document.querySelectorAll(".gallery-item");

for (i = 0; i < galleryItems.length; i++) {
    addSelectOnClick(galleryItems[i]);
}

function addSelectOnClick(galleryItem) {
    galleryItem.addEventListener("click", select);
}

function select($event) {
    var galleryItem = $event.currentTarget;
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
var polling = false;

function onScroll() {
    var distToBottom = getDistToBottom();
    if (!polling && distToBottom < 400) {
        var nextUrl = document.querySelector(".loading").getAttribute("data-next-url");
        if (nextUrl !== "") {
            polling = true;
            var params = getParams();
            get(appUrl + "/admin/more", params).then(function (response) {
                polling = false;
                updateNextUrl(response.next_url);
                addGalleryItems(response.media_array);
            }).catch(function () {
                polling = false;
                console.error("err");
            });
        } else {
            var loadingText = document.querySelector(".loading").querySelector("p");
            loadingText.innerHTML = "No more content to load";
        }
    }
}

function getDistToBottom() {
    var scrollPosition = window.pageYOffset;
    var windowSize = window.innerHeight;
    var bodyHeight = document.body.offsetHeight;
    return Math.max(bodyHeight - (scrollPosition + windowSize), 0);
}

function getParams() {
    var nextUrl = document.querySelector(".loading").getAttribute("data-next-url");
    var baseUrl = nextUrl.split("?")[0];
    var stringParams = nextUrl.split("?").pop().split("&");
    var accessToken = stringParams[0].split("=").pop();
    var count = stringParams[1].split("=").pop();
    var maxTagId = stringParams[2].split("=").pop();
    return "?base_url=" + baseUrl + "&access_token=" + accessToken + "&count=" + count + "&max_tag_id=" + maxTagId;
}

function updateNextUrl(nextUrl) {
    var loading = document.querySelector(".loading");
    loading.setAttribute("data-next-url", nextUrl);
}

var gallery = document.querySelector(".gallery");

function addGalleryItems(mediaArray) {
    for (var _i3 = 0; _i3 < mediaArray.length; _i3++) {
        addGalleryItem(mediaArray[_i3]);
    }
}

function addGalleryItem(media) {
    var galleryItem = document.createElement("div");
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
    var video = document.createElement("video");
    video.setAttribute("controls", true);
    galleryItem.appendChild(video);

    var source = document.createElement("source");
    source.setAttribute("src", media.url);
    source.setAttribute("type", "video/mp4");
    video.appendChild(source);
}

function addImage(galleryItem, media) {
    var image = document.createElement("img");
    image.setAttribute("src", media.url);
    galleryItem.appendChild(image);
}

function addOverlays(galleryItem) {
    var selectedOverlay = document.createElement("div");
    selectedOverlay.classList.add("gallery-item-select-overlay");
    galleryItem.appendChild(selectedOverlay);

    var rejectOverlay = document.createElement("div");
    rejectOverlay.classList.add("gallery-item-reject-overlay");
    galleryItem.appendChild(rejectOverlay);
}

/*
 * GET and POST
 */

function get(url, params) {
    return new Promise(function (resolve, reject) {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", url + params);
        xhr.setRequestHeader("X-CSRF-TOKEN", _token);
        xhr.onload = function () {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                resolve(response);
            } else {
                reject();
            }
        };
        xhr.send();
    });
}

function post(url, params) {
    return new Promise(function (resolve, reject) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", url);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.setRequestHeader("X-CSRF-TOKEN", _token);
        xhr.onload = function () {
            if (xhr.status === 200) {
                resolve();
            } else {
                reject();
            }
        };
        xhr.send(params);
    });
}

/***/ })

/******/ });