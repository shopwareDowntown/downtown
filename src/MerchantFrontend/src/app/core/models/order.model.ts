export interface Order {
  orderDateTime: string;
  orderNumber: string;
  orderCustomer: OrderCustomer;
  price: OrderPrice;
  stateMachineState: OrderState;
}

export interface OrderCustomer {
  firstName: string;
  lastName: string;
}

export interface OrderPrice {
  netPrice: string;
}

export interface OrderState {
  technicalName: string;
}
