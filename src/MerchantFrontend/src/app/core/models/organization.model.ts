import {Media} from "./merchant.model";

export interface Organization {
    name: string;
    id: string;
    email: string;
    password: string;
    salesChannelId: string;
    imprint: string;
    tos: string;
    privacy: string;
    firstName: string;
    lastName: string;
    phone: string;
    postCode: string;
    city: string;
    logo: Media;
    homeText: string;
    homeHeroImage: Media;
    disclaimer: Disclaimer
}

export interface Disclaimer {
    active: boolean;
    text: string;
    image: Media;
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
