import {HTTPClient} from "./HTTPClient.js";


/**
 * Редиректит пользователя если он авторизован
 *
 */
export function redirectUserIfAuth() {
    if (localStorage.getItem("userInfo")) {
        window.location.href = "/Laba6_var21_Fedorov/front/html/cabinet.html";
    }
}

export function clearErrorContainer(id) {
    let container = document.getElementById(id);
    container.innerHTML = "";
    return container;
}

export function sendDataToBack(url, data) {
    let client = new HTTPClient()
    return client.request("POST", {"apiUrl": url, "userData": data})
}

export function fillContainerWithError(element, errors) {
    let ol = document.createElement("ol");
    for (let error in errors) {
        let li = document.createElement("li");
        li.style.color = "red";
        li.textContent = errors[error];
        ol.appendChild(li)
    }
    element.appendChild(ol)
    element.style.display = "block";
}

export function authUserLocalStorage(data) {
    data["data"]["password"] = ""
    localStorage.setItem("userInfo", JSON.stringify(data["data"]))
}

export function getFromPhp(url) {
    let client = new HTTPClient()
    return client.request("GET", {"apiUrl": url})
}