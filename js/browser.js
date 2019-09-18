var displayObject;

function displayApp() {
  displayObject =
    "App name: " +
    navigator.appName +
    "<br>App product: " +
    navigator.product +
    "<br>App version: " +
    navigator.appVersion +
    "<br>User agent: " +
    navigator.userAgent +
    "<br>Platform: " +
    navigator.platform +
    "<br>Language: " +
    navigator.language;
  document.getElementById("browser-content").innerHTML = displayObject;
}

function displayWindow() {
  displayObject =
    "Window information " +
    "<br>Inner height: " +
    window.innerHeight +
    "<br>Inner width: " +
    window.innerWidth;
  document.getElementById("browser-content").innerHTML = displayObject;
}

function displayScreen() {
  displayObject =
    "Screen information" +
    "<br>Screen width: " +
    screen.width +
    "<br>Screen height: " +
    screen.height +
    "<br>Available width: " +
    screen.availWidth +
    "<br>Available height: " +
    screen.availHeight +
    "<br>Color depth: " +
    screen.colorDepth +
    "<br>Pixel depth: " +
    screen.pixelDepth;
  document.getElementById("browser-content").innerHTML = displayObject;
}

function displayLoc() {
  displayObject =
    "Location information" +
    "<br>Href: " +
    window.location.href +
    "<br>Host name: " +
    window.location.hostname +
    "<br> Path name: " +
    window.location.pathname +
    "<br> Protocol: " +
    window.location.protocol;
  document.getElementById("browser-content").innerHTML = displayObject;
}

function getLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition);
  } else {
    document.getElementById("browser-content").innerHTML =
      "Geolocation is not supported by this browser.";
  }
}

function showPosition(position) {
  document.getElementById("browser-content").innerHTML = "Location loading...";
  document.getElementById("browser-content").innerHTML =
    "Latitude: " +
    position.coords.latitude +
    "<br>Longitude: " +
    position.coords.longitude;
}
