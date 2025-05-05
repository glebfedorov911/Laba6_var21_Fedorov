class PostHttpClient {


    constructor(apiUrl, userData) {
        this.apiUrl = apiUrl
        this.userData = userData
    }

    sendRequest() {
        return $.ajax({
            url: this.apiUrl,
            type: "post",
            data: JSON.stringify(this.userData),
            contentType: "application/json",
        })
    }
}

class GetHttpClient {


    constructor(apiUrl) {
        this.apiUrl = apiUrl
    }

    sendRequest() {
        return $.ajax({
            url: this.apiUrl,
            type: "GET"
        })
    }
}

export class HTTPClient {


    request(method, data) {
        if (method === "POST") {
            this.client = new PostHttpClient(data["apiUrl"], data["userData"])
        } else if (method === "GET") {
            this.client = new GetHttpClient(data["apiUrl"])
        } else {
            throw new Error("Not found method for send request")
        }
        return this.client.sendRequest()
    }
}