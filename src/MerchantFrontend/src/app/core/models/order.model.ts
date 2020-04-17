export interface Order {
  id: string
  orderDateTime: string;
  orderNumber: string;
  orderCustomer: OrderCustomer;
  price: OrderPrice;
  stateMachineState: OrderState;
  deliveries: OrderDeliveries[];
  lineItems: OrderItem[];
  transactions: Transaction[];
}

export interface Transaction {
  stateMachineState: OrderState;
}

export interface OrderListData {
  data: Order[],
  total: number
}

export interface OrderCustomer {
  firstName: string;
  lastName: string;
  email: string;
}

export interface OrderPrice {
  netPrice: string;
  totalPrice: string;
}

export interface OrderState {
  technicalName: string;
}

export interface OrderDeliveries {
  shippingOrderAddress: ShippingOrderAddress
}

export interface ShippingOrderAddress {
  firstName: string;
  lastName: string;
  street: string;
  zipcode: string;
  city: string;
  phoneNumber: string;
}

export interface OrderItem {
  label: string;
  price: OrderItemPrice;
}

export interface OrderItemPrice {
  unitPrice: number;
  quantity: number;
  totalPrice: number;
}
