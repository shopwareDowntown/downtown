import {DeliveryPackage} from "./delivery-package.model";

export interface DeliveryBoy {
    id?: string;
    title: string;
    firstName: string;
    lastName: string;
    email: string;
    phoneNumber: string;
    street: string;
    zipCode: string;
    city: string;
    deliveryPackages: DeliveryPackage[];
}
