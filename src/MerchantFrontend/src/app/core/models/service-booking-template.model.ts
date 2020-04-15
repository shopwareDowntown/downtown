import {ServiceBookingDate} from "./service-booking-date.model";

export interface ServiceBookingTemplate {
    id?: string;
    type: string;
    dates: ServiceBookingDate[];
  }