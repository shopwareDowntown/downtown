export interface Organization {
    email: string;
    password: string;
    salesChannelId: string;
}

export interface OrganizationAuthority {
    id: string;
    name: string;
    domain: string;
    accessKey: string;
}

export interface OrganizationLoginResult {
    'sw-context-token': string;
}
