import {redirectUserIfAuth, clearErrorContainer, sendDataToBack, fillContainerWithError, authUserLocalStorage} from "./utils.js";

function parseDataFromForm() {
    return {
        "email": document.getElementById("email").value,
        "login": document.getElementById("login").value,
        "phone": document.getElementById("phone").value,
        "station": document.getElementById("place").value,
        "password": document.getElementById("password").value
    };
}



$(".submit-btn").click(function () {
    let passwordErrorContainer = clearErrorContainer("passwordError");
    let phoneErrorContainer = clearErrorContainer("phoneError");
    let emailErrorContainer = clearErrorContainer("emailError");
    let mainError = clearErrorContainer("mainError");

    let data = parseDataFromForm()
    sendDataToBack("back/endpoints/signup.php", data).done(function(response, textStatus, jqXHR) {
        let responseJSON = jqXHR.responseJSON;
        if (!response["success"]) {
            if (responseJSON["errors"].length > 0) {
                fillContainerWithError(phoneErrorContainer, responseJSON["errors"][0]["phone"]["errors"]);
                fillContainerWithError(passwordErrorContainer, responseJSON["errors"][1]["password"]["errors"]);
                fillContainerWithError(emailErrorContainer, responseJSON["errors"][2]["email"]["errors"]);
            }

            if (responseJSON["mainError"]) {
                fillContainerWithError(mainError, responseJSON["mainError"]);
            }
        } else {
            authUserLocalStorage(responseJSON);
            redirectUserIfAuth();
        }
    })
})

redirectUserIfAuth();