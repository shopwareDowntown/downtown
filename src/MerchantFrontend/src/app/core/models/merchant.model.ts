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
  publicCompanyName: string;
  email: string;
  password: string;
  salesChannelId: string;
}

export interface MerchantLoginResult {
  'sw-context-token': string;
}

