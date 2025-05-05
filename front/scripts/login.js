import {
    redirectUserIfAuth,
    clearErrorContainer,
    sendDataToBack,
    fillContainerWithError,
    authUserLocalStorage
} from "./utils.js";

function parseDataForLogin() {
    return {
        "login": document.getElementById("login").value,
        "password": document.getElementById("password").value
    }
}

if (localStorage.getItem("userInfo")) {
    window.location.href = "#"
}

$(".submit-btn").click(function () {
    let mainError = clearErrorContainer("mainError");
    let data = sendDataToBack("../../back/endpoints/login.php", parseDataForLogin()).done(function(response, textStatus, jqXHR) {
        let responseJSON = jqXHR.responseJSON;
        console.log(responseJSON)

        if (!response["success"]) {
            fillContainerWithError(mainError, responseJSON["mainError"])
        } else {
            authUserLocalStorage(responseJSON);
            redirectUserIfAuth();
        }
    })
})

redirectUserIfAuth();