document.addEventListener('scroll', onScroll);
let polling = false;
let xhr = new XMLHttpRequest();
let _token = document.querySelector("meta[name='csrf-token']").getAttribute("content");

function onScroll() {
    distToBottom = getDistToBottom();
    if (!polling && distToBottom < 400) {
        polling = true;
        let params = getParams();
        xhr.open("GET", "/gallery/more" + params);
        xhr.setRequestHeader("X-CSRF-TOKEN", _token);
        xhr.send();
    }
}

function getDistToBottom () {
    let scrollPosition = window.pageYOffset;
    let windowSize = window.innerHeight;
    let bodyHeight = document.body.offsetHeight;
    return Math.max(bodyHeight - (scrollPosition + windowSize), 0);  
}

function getParams() {
    let loading = document.querySelector(".loading");
    let nextUrl = loading.getAttribute("data-next-url");
    let baseUrl = nextUrl.split("?")[0];
    let stringParams = nextUrl.split("?").pop().split("&");
    let accessToken = stringParams[0].split("=").pop();
    let maxTagId = stringParams[1].split("=").pop(); 
    return "?base_url=" + baseUrl + "&access_token=" + accessToken + "&max_tag_id=" + maxTagId;
}

xhr.onreadystatechange = function() {
    if (xhr.readyState == 4 && xhr.status == 200) {
        let response = JSON.parse(xhr.responseText)
        updateNextUrl(response.next_url);
        addGalleryItems(response.media_array);
        polling = false;
    } else {
        polling = false;
    }
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
    gallery.appendChild(galleryItem);
    if (media.type === "video") {
        addVideo(galleryItem, media);
    } else if (media.type === "image") {
        addImage(galleryItem, media);
    }
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
  