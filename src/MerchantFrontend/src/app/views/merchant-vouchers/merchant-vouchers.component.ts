import { Component, OnInit } from '@angular/core';
import { MerchantApiService } from '../../core/services/merchant-api.service';
import { Voucher, VoucherListData } from '../../core/models/voucher.model';
import { ToastService } from '../../core/services/toast.service';
import { TranslateService } from '@ngx-translate/core';

@Component({
  selector: 'portal-merchant-vouchers',
  templateUrl: './merchant-vouchers.component.html'
})
export class MerchantVouchersComponent implements OnInit {

  vouchers: Voucher[] = [];
  loading: boolean;
  total: number;
  limit = 10;
  offset: number;
  currentPage = 1;
  fromVoucher: number;
  tillVoucher: number;

  constructor(
    private merchantApiService: MerchantApiService,
    private toastService: ToastService,
    private readonly translateService: TranslateService
  ) {
  }

  ngOnInit(): void {
    this.offset = 0;
    this.refresh();
  }

  refresh(): void {
    this.loading = true;
    this.merchantApiService.getVouchers(this.limit, this.offset)
      .subscribe(
        (voucherList: VoucherListData) => {
          this.vouchers = voucherList[0].data;
          this.total = voucherList[0].total;
          this.pageChange();
          this.loading = false;
        }
      )
  }

  pageChange(): void {
    this.offset = (this.currentPage - 1) * 10;

    if (this.currentPage === 1) {
      this.fromVoucher = this.fromVoucher = 1;
      if (this.total === 0) {
        this.fromVoucher = 0;
      }
    } else {
      this.fromVoucher = (this.currentPage -1) * this.limit;
    }

    if (this.fromVoucher + this.limit <= this.total) {
      this.tillVoucher = this.fromVoucher + this.limit;
      if (this.fromVoucher === 1) {
        this.tillVoucher -= 1;
      }
    } else {
      this.tillVoucher = this.total;
    }
  }

  redeemVoucher(voucher: Voucher): void
  {
    this.merchantApiService.redeemVoucher(voucher).subscribe((result) => {
      this.toastService.success(
        this.translateService.instant('MERCHANT.VOUCHERS.REDEEEM_SUCCESSFUL_HEADER'),
        this.translateService.instant('MERCHANT.VOUCHERS.REDEEEM_SUCCESSFUL_BODY')
        );
      this.refresh();
    });
  }
}
