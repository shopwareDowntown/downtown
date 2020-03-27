import { Authority } from './authority.model';

export interface Merchant {
    id?: string;
    authority?: Authority;
    email: string;
    password?: string;
    name: string;
    firstName: string;
    lastName: string;
    salutation: string;
    street: string;
    zipCode: string;
    city: string;
    country: string;
    phoneNumber: string;
}

export interface MerchantRegistration {
    name: string;
    mail: string;
    password: string;
    authority: Authority;
}

export interface MerchantLoginResult {
  'sw-context-token': string;
}

