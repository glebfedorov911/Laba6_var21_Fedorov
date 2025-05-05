import {HTTPClient} from "./HTTPClient.js";
import {getFromPhp, sendDataToBack, fillContainerWithError, clearErrorContainer} from "./utils.js";


function redirectToLogin() {
    if (!localStorage.getItem("userInfo")) {
        window.location.href = "/Laba6_var21_Fedorov/front/html/login.html"
    }
}

function getTime() {
    let client = new HTTPClient();
    client.request("GET", {"apiUrl": "../../back/endpoints/serverTime.php"}).done(function(response, textStatus, jqXHR) {
        document.getElementById("time").innerHTML = JSON.parse(response)["time"]
    })
}

let typeSign = '';
let currentId = 0;

function parseDataSign(typeSign) {
    return {
        "name": document.getElementById("request-name").value,
        "width": document.getElementById("request-width").value,
        "height": document.getElementById("request-height").value,
        "length": document.getElementById("request-length").value,
        "category": document.getElementById("request-category").value,
        "typeSign": typeSign,
        "userId": JSON.parse(localStorage.getItem("userInfo"))["id"],
        "currentId": currentId.innerHTML
    }
}

$(".close-btn").click(function (e) {
    clearErrorContainer("mainError");
    clearErrorContainer("nameError");
    clearErrorContainer("widthError");
    clearErrorContainer("heightError");
    clearErrorContainer("lengthError");
    let element = document.getElementById("request-form-modal");
    element.style.display = "none";
    currentId = 0;
})

$(".btn.add-btn").click(function(e) {
    document.getElementById("request-name").value = '';
    document.getElementById("request-length").value = '';
    document.getElementById("request-height").value = '';
    document.getElementById("request-width").value = '';
    document.getElementById("request-category").value = 'Первая';
    let element = document.getElementById("request-form-modal");
    element.style.display = "block";
    typeSign = "create"
})

$(document).on('click', '.btn.edit-btn', function(e) {
    let currentEdit = this.getAttribute("data-id");
    let id = document.querySelectorAll("td")[7*currentEdit]
    let name = document.querySelectorAll("td")[7*currentEdit+1]
    let width = document.querySelectorAll("td")[7*currentEdit+2]
    let height = document.querySelectorAll("td")[7*currentEdit+3]
    let length = document.querySelectorAll("td")[7*currentEdit+4]
    let category = document.querySelectorAll("td")[7*currentEdit+5]

    let element = document.getElementById("request-form-modal");
    element.style.display = "block";
    typeSign = "update"
    currentId = id;

    document.getElementById("request-name").value = name.innerText;
    document.getElementById("request-width").value = width.innerText;
    document.getElementById("request-height").value = height.innerText;
    document.getElementById("request-length").value = length.innerText;
    document.getElementById("request-category").value = category.innerText;
});

$(document).on('click', '.btn.delete-btn', function(e) {
    let currentEdit = this.getAttribute("data-id");
    let id = document.querySelectorAll("td")[7*currentEdit]
    sendDataToBack("/Laba6_var21_Fedorov/back/endpoints/aboutSpace.php", {"id": id.innerHTML, "typeSign": "delete"})
    window.location.reload();
});


$(".btn.submit-btn").click(function (e) {
    let mainError = clearErrorContainer("mainError");
    let nameError = clearErrorContainer("nameError");
    let widthError = clearErrorContainer("widthError");
    let heightError = clearErrorContainer("heightError");
    let lengthError = clearErrorContainer("lengthError");

    let data = sendDataToBack("/Laba6_var21_Fedorov/back/endpoints/aboutSpace.php", parseDataSign(typeSign)).done(function(response, textStatus, jqXHR) {
        let responseJSON = jqXHR.responseJSON;
        if (!response["success"]) {
            if (responseJSON["errors"].length > 0) {
                fillContainerWithError(nameError, responseJSON["errors"][0]["name"]["errors"]);
                fillContainerWithError(widthError, responseJSON["errors"][1]["width"]["errors"]);
                fillContainerWithError(heightError, responseJSON["errors"][2]["height"]["errors"]);
                fillContainerWithError(lengthError, responseJSON["errors"][3]["length"]["errors"]);
            }

            if (responseJSON["mainError"]) {
                fillContainerWithError(mainError, responseJSON["mainError"]);
            }
        } else {
            currentId = 0;
            typeSign = '';
            let element = document.getElementById("request-form-modal");
            element.style.display = "none";
            window.location.reload();
        }
    })
})

let getData = getFromPhp(`/Laba6_var21_Fedorov/back/endpoints/aboutSpace.php?userId=${JSON.parse(localStorage.getItem("userInfo"))["id"]}`)
    .done(function(response, textStatus, jqXHR) {
    let tbody = document.querySelector("tbody")
    tbody.innerHTML = ""
    for (let i = 0; i < response["data"].length; i++) {
        let tr = document.createElement("tr");
        let fields = ["id", "name", "width", "height", "length", "category"]
        fields.forEach(field => {
            let cell = document.createElement("td")
            cell.textContent = response["data"][i][field]
            tr.appendChild(cell)
        })
        let actionsTd = document.createElement("td");
        actionsTd.innerHTML = `
            <button class="btn edit-btn" data-id="${i}">✏️</button>
            <button class="btn delete-btn" data-id="${i}">🗑️</button>
        `;
        tr.appendChild(actionsTd)
        tbody.appendChild(tr)
    }
})
getTime();
setInterval(getTime, 1000);
redirectToLogin();

document.querySelector(".username").innerText = JSON.parse(localStorage.getItem("userInfo"))["login"];
document.querySelector(".visit").innerText = `Количество посещений: ${JSON.parse(localStorage.getItem("userInfo"))["visit"]}`;
