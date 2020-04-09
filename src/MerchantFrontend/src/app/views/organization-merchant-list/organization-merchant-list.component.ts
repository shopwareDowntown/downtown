import { Component, OnInit } from '@angular/core';
import {Merchant, MerchantListData} from '../../core/models/merchant.model';
import { MerchantApiService } from '../../core/services/merchant-api.service';

@Component({
  selector: 'portal-organization-merchant-list',
  templateUrl: './organization-merchant-list.component.html',
  styleUrls: ['./organization-merchant-list.component.scss']
})
export class OrganizationMerchantListComponent implements OnInit {

  merchantList: Merchant[];
  loading = false;
  total: number;
  limit = 10;
  offset: number;
  currentPage = 1;
  fromMerchant: number;
  tillMerchant: number;

  constructor(private readonly merchantApiService: MerchantApiService) { }

  ngOnInit(): void {
    this.offset = 0;
    this.refresh();
  }

  refresh() {
    this.loading = true;
    this.pageChange();
    this.merchantApiService.getMerchantList(this.limit, this.offset).subscribe((merchantListData: MerchantListData) => {
      this.merchantList = merchantListData.data;
      this.total = merchantListData.total;
      this.pageChange();
      this.loading = false;
    })
  }

  disableMerchant(merchant: Merchant): void {
    this.merchantApiService.changeMerchantsActiveFlag(merchant, false).subscribe(() => {
      merchant.active = false;
    });
  }

  enableMerchant(merchant: Merchant): void {
    this.merchantApiService.changeMerchantsActiveFlag(merchant, true).subscribe(() => {
      merchant.active = true
    });
  }

  pageChange(): void {
    this.offset = (this.currentPage - 1) * 10;

    if (this.currentPage === 1) {
      this.fromMerchant = this.fromMerchant = 1;
      if (this.total === 0) {
        this.fromMerchant = 0;
      }
    } else {
      this.fromMerchant = (this.currentPage -1) * this.limit;
    }

    if (this.fromMerchant + this.limit <= this.total) {
      this.tillMerchant = this.fromMerchant + this.limit;
      if (this.fromMerchant === 1) {
        this.tillMerchant -= 1;
      }
    } else {
      this.tillMerchant = this.total;
    }
  }
}
