const ApiService = Shopware.Classes.ApiService;

class ApiClient extends ApiService{
    constructor(httpClient, loginService, apiEndpoint = 'swag-security') {
        super(httpClient, loginService, apiEndpoint);
    }

    getFixes() {
        const headers = this.getBasicHeaders({});

        return this.httpClient
            .get(`_action/${this.getApiBasePath()}/available-fixes`, {
                headers
            })
            .then((response) => {
                return ApiService.handleResponse(response);
            });
    }

    getUpdate() {
        const headers = this.getBasicHeaders({});

        return this.httpClient
            .get(`_action/${this.getApiBasePath()}/update-available`, {
                headers
            })
            .then((response) => {
                return ApiService.handleResponse(response);
            });
    }

    saveValues(config, currentPassword) {
        const headers = this.getBasicHeaders({});

        return this.httpClient
            .post(`_action/${this.getApiBasePath()}/save-config`, {config, currentPassword},{
                headers
            })
            .then((response) => {
                return ApiService.handleResponse(response);
            });
    }

    cacheClear() {
        const headers = this.getBasicHeaders({});
        return this.httpClient.delete(`_action/${this.getApiBasePath()}/clear-container-cache`, { headers });
    }
}

export default ApiClient;
