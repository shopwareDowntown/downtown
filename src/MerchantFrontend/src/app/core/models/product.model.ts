import {ServiceBookingTemplate} from "./service-booking-template.model";

export interface Product {
  id?: string;
  name: string;
  description: string;
  productType: string;
  active: boolean;
  images: any[];
  price?: number;
  tax?: number;
  serviceBookingTemplate?: ServiceBookingTemplate;
}
