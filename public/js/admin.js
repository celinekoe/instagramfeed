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

var alert = document.querySelector(".alert");

var alertCloseButton = document.querySelector(".alert-close-button");
alertCloseButton.addEventListener("click", closeAlert);

function closeAlert() {
    alert.classList.remove("show");
}

var modalConfirmButtons = document.querySelectorAll(".modal-confirm-button");
for (i = 0; i < modalConfirmButtons.length; i++) {
    addConfirmOnClick(modalConfirmButtons[i]);
}

function addConfirmOnClick(confirmButton) {
    confirmButton.addEventListener("click", confirm);
}

function confirm($event) {
    var xhr = new XMLHttpRequest();
    var _token = document.querySelector("meta[name='csrf-token']").getAttribute("content");
    var confirmButton = $event.currentTarget;
    var params = getConfirmParams(confirmButton);
    xhr.open("POST", "/admin");
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("X-CSRF-TOKEN", _token);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            updateDataStatus(confirmButton);
            deselect();
            alert.classList.add("show");
        }
    };
    xhr.send(params);
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

/***/ })

/******/ });