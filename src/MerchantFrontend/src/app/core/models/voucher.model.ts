import { DateModel } from './date.model';

export interface Voucher {
  id?: string;
  status: string;
  code: string;
  name: string;
  redeemedAt?: DateModel;
  createdAt: DateModel;
  value: VoucherData;
}

export interface VoucherData {
  price: number;
}

export interface VoucherListData {
  data: Voucher[];
  total: number;
}
