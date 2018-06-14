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

/*
 * Scroll to load
 */

document.addEventListener('scroll', onScroll);

var polling = false;
var _token = document.querySelector("meta[name='csrf-token']").getAttribute("content");

function onScroll() {
    var distToBottom = getDistToBottom();
    if (!polling && distToBottom < 400) {
        var nextUrl = document.querySelector(".loading").getAttribute("data-next-url");
        if (nextUrl !== "") {
            polling = true;
            var params = getParams();
            get("/gallery/more", params).then(function (response) {
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
    var maxTagId = stringParams[1].split("=").pop();
    return "?base_url=" + baseUrl + "&access_token=" + accessToken + "&max_tag_id=" + maxTagId;
}

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
    var link = addLink(galleryItem, media);
    if (media.type === "video") {
        addVideo(link, media);
    } else if (media.type === "image") {
        addImage(link, media);
    }
}

function addLink(galleryItem, media) {
    var link = document.createElement("a");
    link.setAttribute("href", media.link);
    link.setAttribute("target", "_blank");
    galleryItem.appendChild(link);
    return link;
}

function addVideo(link, media) {
    var videoContainer = document.createElement("div");
    link.appendChild(videoContainer);

    var video = document.createElement("video");
    videoContainer.classList.add("video-container");
    videoContainer.appendChild(video);

    var source = document.createElement("source");
    source.setAttribute("src", media.url);
    source.setAttribute("type", "video/mp4");
    video.appendChild(source);

    var videoOverlay = document.createElement("div");
    videoOverlay.classList.add("video-overlay");
    videoContainer.appendChild(videoOverlay);

    var playIcon = document.createElement("span");
    playIcon.classList.add("oi");
    playIcon.setAttribute("data-glyph", "media-play");
    videoOverlay.appendChild(playIcon);
}

function addImage(link, media) {
    var image = document.createElement("img");
    image.setAttribute("src", media.url);
    link.appendChild(image);
}

/*
 * GET
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

/***/ })

/******/ });