import {ServiceBookingTemplate} from "./service-booking-template.model";

export interface ServiceBookingDate {
  id?: string;
  start: string;
  end: string;
  template: ServiceBookingTemplate;
}
