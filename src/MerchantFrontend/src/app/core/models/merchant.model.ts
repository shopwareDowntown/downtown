import { Authority } from './authority.model';

export interface Merchant {
  id?: string;
  authority?: Authority;
  publicCompanyName: string;
  publicOwner: string;
  publicPhoneNumber: string;
  publicEmail: string;
  publicWebsite: string;
  categoryId: string;
  publicOpeningTimes: string;
  publicDescription: string;
  pictures: string[];
  public: boolean;
  firstName: string;
  lastName: string;
  street: string;
  zip: string;
  city: string;
  countryId: string; // New interface for countries?
  email: string;
  password?: string;
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

