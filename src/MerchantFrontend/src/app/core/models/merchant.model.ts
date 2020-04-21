import { OrganizationAuthority } from './organization.model';

export interface Merchant {
  id?: string;
  authority?: OrganizationAuthority;
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
  media: Media[];
  cover: Media;
  imprint: string;
  tos: string;
  privacy: string;
  revocation: string;
  active: boolean;
  availability: number;
  availabilityText: string;
  mollieProdKey: string;
  mollieTestKey: string;
  mollieTestEnabled: boolean;
  paymentMethods: string;

  services: {
    id: string;
  }[];
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

export interface PasswordReset {
  email: string
}

export interface Media {
  url: string;
  id: string;
}

export interface MerchantListData {
  total: number;
  data: Merchant[];
}

export interface MerchantService {
  id: string;
  name: string;
  translated: {
    name: string;
  }
}
