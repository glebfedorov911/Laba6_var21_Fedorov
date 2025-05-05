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

/**
 * Очищает контейнеры после того как они были закрыты
 *
 * @param string id айди элемента
 * return этот контейнер
 */
export function clearErrorContainer(id) {
    let container = document.getElementById(id);
    container.innerHTML = "";
    return container;
}

/**
 * Отправляет пост запрос на бэк
 *
 * @param string url путь до файла
 * @param object data данные для отправки
 * return ответ сервера
 */
export function sendDataToBack(url, data) {
    let client = new HTTPClient()
    return client.request("POST", {"apiUrl": url, "userData": data})
}

/**
 * Заполняет контейнер с ошибками которые вернул бэк
 *
 * @param HTTPElementDoc element элемент
 * @param object errors ошибки со стороны бэка
 */
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

/**
 * Cохраняет данные об авторизованном пользователе в локалсторадж
 *
 * @param object data данные о юзере
 */
export function authUserLocalStorage(data) {
    data["data"]["password"] = ""
    localStorage.setItem("userInfo", JSON.stringify(data["data"]))
}

/**
 * Отправляет гет запрос на бэк
 *
 * @param string url путь до файла
 * return ответ сервера
 */
export function getFromPhp(url) {
    let client = new HTTPClient()
    return client.request("GET", {"apiUrl": url})
}