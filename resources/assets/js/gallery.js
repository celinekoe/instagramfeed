let appUrl = "http://192.168.1.43";
// let appUrl = "/server/public";

/*
 * Scroll to load
 */

document.addEventListener('scroll', onScroll);

let polling = false;
let _token = document.querySelector("meta[name='csrf-token']").getAttribute("content");

function onScroll() {
    let distToBottom = getDistToBottom();
    if (!polling && distToBottom < 400) {
        let nextUrl = document.querySelector(".loading").getAttribute("data-next-url");
        if (nextUrl !== "") {
            polling = true;
            let params = getParams();
            get(appUrl + "/gallery/more", params)
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
    console.log(mediaArray.length);
    for (let i = 0; i < mediaArray.length; i++) {
        addGalleryItem(mediaArray[i]);
    }
}

function addGalleryItem(media) {
    let galleryItem = document.createElement("div");
    galleryItem.classList.add("gallery-item");
    gallery.appendChild(galleryItem);
    let link = addLink(galleryItem, media);
    if (media.type === "video") {
        addVideo(link, media);
    } else if (media.type === "image") {
        addImage(link, media);
    }
}

function addLink(galleryItem, media) {
    let link = document.createElement("a");
    link.setAttribute("href", media.link);
    link.setAttribute("target", "_blank");
    galleryItem.appendChild(link);
    return link;
}

function addVideo(link, media) {
    let videoContainer = document.createElement("div");
    link.appendChild(videoContainer);

    let video = document.createElement("video");
    videoContainer.classList.add("video-container");
    videoContainer.appendChild(video);

    let source = document.createElement("source");
    source.setAttribute("src", media.url);
    source.setAttribute("type", "video/mp4");
    video.appendChild(source);

    let videoOverlay = document.createElement("div");
    videoOverlay.classList.add("video-overlay");
    videoContainer.appendChild(videoOverlay);

    let playIcon = document.createElement("span");
    playIcon.classList.add("oi");
    playIcon.setAttribute("data-glyph", "media-play");
    videoOverlay.appendChild(playIcon);
}

function addImage(link, media) {
    let image = document.createElement("img");
    image.setAttribute("src", media.url);
    link.appendChild(image);
}

/*
 * GET
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