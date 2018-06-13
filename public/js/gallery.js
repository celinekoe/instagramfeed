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
/******/ 	return __webpack_require__(__webpack_require__.s = 41);
/******/ })
/************************************************************************/
/******/ ({

/***/ 41:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(42);


/***/ }),

/***/ 42:
/***/ (function(module, exports) {

document.addEventListener('scroll', onScroll);
var polling = false;
var xhr = new XMLHttpRequest();
var _token = document.querySelector("meta[name='csrf-token']").getAttribute("content");

function onScroll() {
    distToBottom = getDistToBottom();
    if (!polling && distToBottom < 400) {
        polling = true;
        var params = getParams();
        xhr.open("GET", "/gallery/more" + params);
        xhr.setRequestHeader("X-CSRF-TOKEN", _token);
        xhr.send();
    }
}

function getDistToBottom() {
    var scrollPosition = window.pageYOffset;
    var windowSize = window.innerHeight;
    var bodyHeight = document.body.offsetHeight;
    return Math.max(bodyHeight - (scrollPosition + windowSize), 0);
}

function getParams() {
    var loading = document.querySelector(".loading");
    var nextUrl = loading.getAttribute("data-next-url");
    var baseUrl = nextUrl.split("?")[0];
    var stringParams = nextUrl.split("?").pop().split("&");
    var accessToken = stringParams[0].split("=").pop();
    var maxTagId = stringParams[1].split("=").pop();
    return "?base_url=" + baseUrl + "&access_token=" + accessToken + "&max_tag_id=" + maxTagId;
}

xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
        var response = JSON.parse(xhr.responseText);
        updateNextUrl(response.next_url);
        addGalleryItems(response.media_array);
        polling = false;
    } else {
        polling = false;
    }
};

function updateNextUrl(nextUrl) {
    var loading = document.querySelector(".loading");
    loading.setAttribute("data-next-url", nextUrl);
}

var gallery = document.querySelector(".gallery");

function addGalleryItems(mediaArray) {
    for (var i = 0; i < mediaArray.length; i++) {
        addGalleryItem(mediaArray[i]);
    }
}

function addGalleryItem(media) {
    var galleryItem = document.createElement("div");
    galleryItem.classList.add("gallery-item");
    gallery.appendChild(galleryItem);
    if (media.type === "video") {
        addVideo(galleryItem, media);
    } else if (media.type === "image") {
        addImage(galleryItem, media);
    }
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

/***/ })

/******/ });